<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
use App\Models\InventoryMovement;
use App\Models\Discount;
use App\Models\InventoryStock;
use App\Models\ProductVariant;
use App\Models\Staff;
use App\Models\Store;
use App\Models\TaxCategory;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Exception;
use InvalidArgumentException;
use Carbon\Carbon;

class SaleService
{
    /**
     * Create a new sale transaction.
     *
     * @param array $data Data keranjang dan pembayaran
     * @param Staff $staff Karyawan yang melakukan transaksi
     * @return Sale
     * @throws Exception
     */
    public function createSale(array $data, Staff $staff): Sale
    {
        $store = $staff->getActiveStore();
        if (!$store) {
            throw new Exception("Staff tidak memiliki store aktif.");
        }

        // 2. Persiapkan items
        $items = $data['cart'] ?? [];
        if (empty($items)) {
            throw new InvalidArgumentException("Keranjang belanja kosong.");
        }

        return DB::transaction(function () use ($data, $staff, $store, $items) {
            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;
            
            $saleItemsData = [];
            $inventoryMovementsData = [];

            // 1. Preload semua variant
            $variantIds = array_keys($items);
            $variants = ProductVariant::with(['product', 'product.taxCategory'])
                ->whereIn('id', $variantIds)
                ->get()
                ->keyBy('id');

            // 2. Preload semua stok sekaligus dengan lock, urutkan by id (hindari deadlock)
            $stocks = InventoryStock::where('store_id', $store->id)
                ->whereIn('variant_id', $variantIds)
                ->orderBy('variant_id')
                ->lockForUpdate()
                ->get()
                ->keyBy('variant_id');

            // 3. Preload semua diskon
            $discountIds = collect($items)->pluck('discount_id')->filter()->unique()->toArray();
            $discounts = !empty($discountIds) 
                ? Discount::whereIn('id', $discountIds)->where('active', true)->get()->keyBy('id') 
                : collect();

            // 4. Preload tax categories
            $taxCategoryIds = $variants->map(function ($variant) use ($store) {
                return $variant->product->tax_category_id ?? $store->default_tax_category_id;
            })->filter()->unique()->toArray();
            $taxCategories = !empty($taxCategoryIds) 
                ? TaxCategory::whereIn('id', $taxCategoryIds)->get()->keyBy('id') 
                : collect();

            foreach ($items as $variantId => $itemData) {
                $variant = $variants->get($variantId);
                if (!$variant) {
                    throw new Exception("Produk variant dengan ID $variantId tidak ditemukan.");
                }

                $quantity = $itemData['quantity'];
                $unitPrice = $variant->selling_price; // Always use database price

                $discount = 0;
                if (!empty($itemData['discount_id'])) {
                    $discountRecord = $discounts->get($itemData['discount_id']);
                    if ($discountRecord) {
                        if ($discountRecord->type === 'percentage') {
                            $discount = ($unitPrice * $quantity) * ($discountRecord->value / 100);
                        } else {
                            $discount = $discountRecord->value;
                        }
                    }
                } elseif (!empty($itemData['discount'])) {
                    // Fallback, but enforce limits
                    $discount = min(floatval($itemData['discount']), $unitPrice * $quantity);
                }

                // Validasi Stok (dengan Row Lock yang sudah didapat di atas)
                $stock = $stocks->get($variantId);
                    
                if (!$stock || $stock->quantity < $quantity) {
                    throw new Exception("Stok tidak mencukupi untuk: " . $variant->product->name);
                }

                // Kurangi stok langsung untuk menghindari race condition
                $stock->decrement('quantity', $quantity);

                // Tentukan Tax Category
                $taxCategoryId = $variant->product->tax_category_id ?? $store->default_tax_category_id;
                $taxAmount = 0;
                
                if ($taxCategoryId) {
                    $taxCategory = $taxCategories->get($taxCategoryId);
                    if ($taxCategory) {
                        $this->validateTaxCombination($store, $taxCategory);
                        
                        // Hitung PPN/PBJT
                        $amountBeforeTax = ($unitPrice * $quantity) - $discount;
                        $taxAmount = $amountBeforeTax * ($taxCategory->rate / 100);
                    } else {
                        $taxCategoryId = null;
                    }
                }

                // Hitung Subtotal
                $subtotal += ($unitPrice * $quantity);
                $discountTotal += $discount;
                $taxTotal += $taxAmount;

                $saleItemId = Str::uuid()->toString();

                $saleItemsData[] = [
                    'id' => $saleItemId,
                    'variant_id' => $variantId,
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                    'cost_price' => $variant->cost_price,
                    'discount' => $discount,
                    'tax_category_id' => $taxCategoryId,
                    'tax_amount' => $taxAmount,
                ];

                // Siapkan InventoryMovement
                $inventoryMovementsData[] = [
                    'id' => Str::uuid()->toString(),
                    'variant_id' => $variantId,
                    'store_id' => $store->id,
                    'movement_type' => 'sale',
                    'quantity_change' => -$quantity, // Negative untuk pengurangan stok
                    'reference_table' => 'sale_items',
                    'reference_id' => $saleItemId,
                    'staff_id' => $staff->id,
                    'note' => 'Penjualan'
                ];
            }

            $grandTotal = $subtotal - $discountTotal + $taxTotal;
            
            $saleId = Str::uuid()->toString();
            $saleNumber = $this->generateSaleNumber($store->id);
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $amountPaid = floatval($data['cash_received'] ?? $grandTotal);
            $changeAmount = max(0, $amountPaid - $grandTotal);
            
            // jika kas bon / piutang, dsb maka status payment perlu menyesuaikan. Asumsi saat ini full_paid.
            $paymentStatus = ($amountPaid >= $grandTotal) ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

            // Insert Sale
            $sale = new Sale();
            $sale->id = $saleId;
            $sale->store_id = $store->id;
            $sale->customer_id = $data['customer_id'] ?? null;
            $sale->staff_id = $staff->id;
            $sale->sale_number = $saleNumber;
            $sale->sale_date = Carbon::now();
            $sale->status = 'completed';
            $sale->subtotal = $subtotal;
            $sale->discount_total = $discountTotal;
            $sale->tax_total = $taxTotal;
            $sale->service_charge_total = 0;
            $sale->grand_total = $grandTotal;
            $sale->payment_status = $paymentStatus;
            $sale->notes = $data['notes'] ?? null;
            $sale->save();

            // Insert Sale Items
            foreach ($saleItemsData as $index => $item) {
                $saleItemsData[$index]['sale_id'] = $saleId;
            }
            SaleItem::insert($saleItemsData);

            // Insert Payment
            if ($amountPaid > 0) {
                Payment::create([
                    'id' => Str::uuid()->toString(),
                    'sale_id' => $saleId,
                    'payment_method' => $paymentMethod,
                    'amount' => min($amountPaid, $grandTotal), // yang diakui sbg omset real sesuai grand total di sistem akutansi atau total bayar
                    'change_amount' => $changeAmount,
                    'paid_at' => Carbon::now()
                ]);
            }

            // Insert Inventory Movements (akan memicu trigger update ke inventory_stock otomatis)
            InventoryMovement::insert($inventoryMovementsData);

            return $sale->load(['items.variant.product', 'payments']);
        });
    }

    /**
     * Dapatkan store aktif bagi staff
     */
    // private function getActiveStore(Staff $staff)
    // {
    //     $primaryStore = $staff->stores()->wherePivot('is_primary', true)->first();
    //     if ($primaryStore) {
    //         return $primaryStore;
    //     }
    //     return $staff->stores()->first();
    // }

    /**
     * Generate Sale Number unik
     */
    private function generateSaleNumber(string $storeId): string
    {
        $dateStr = Carbon::now()->format('Ymd');
        $storePrefix = strtoupper(substr($storeId, 0, 4));
        $random = strtoupper(Str::random(4));
        return "INV-{$dateStr}-{$storePrefix}-{$random}";
    }

    /**
     * Validasi Kombinasi Tax dan Store
     */
    private function validateTaxCombination(Store $store, TaxCategory $taxCategory)
    {
        $taxType = strtoupper($taxCategory->tax_type);
        $businessType = strtoupper($store->business_type);

        if ($businessType === 'F&B' && $taxType === 'PPN') {
            throw new Exception("Store F&B tidak boleh dikenakan PPN.");
        }

        if (!$store->is_pkp && $taxType === 'PPN') {
            throw new Exception("Store berstatus Non-PKP tidak boleh memungut PPN.");
        }

        // F&B disini bs Restoran/Kafe yg memungut PBJT10%. Non-F&B (Retail) tidak boleh PBJT
        if ($businessType !== 'F&B' && $taxType === 'PBJT') {
            throw new Exception("Store Non-F&B tidak boleh dikenakan PBJT.");
        }
    }
}
