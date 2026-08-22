<?php

namespace App\Services;

use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Payment;
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

// ┌─────────────────────────────────────────────────────────────────────────────┐
// │  ARSITEKTUR STOK — PENTING, BACA SEBELUM MODIFIKASI                        │
// │                                                                             │
// │  Pengurangan stok dan pencatatan inventory_movements dilakukan              │
// │  SEPENUHNYA oleh DB trigger: trg_decrement_stock_on_sale_item               │
// │  (lihat migration: 2026_08_22_132309_add_business_logic_triggers_pos.php)   │
// │                                                                             │
// │  SaleService hanya boleh:                                                   │
// │    1. Memvalidasi stok SEBELUM INSERT (guard di sisi PHP untuk UX error)    │
// │    2. Melakukan INSERT ke sale_items (yang memicu trigger)                  │
// │                                                                             │
// │  JANGAN tambahkan $stock->decrement() atau InventoryMovement::insert()      │
// │  di sini — itu akan menyebabkan stok berkurang DUA KALI per transaksi.      │
// └─────────────────────────────────────────────────────────────────────────────┘

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
            $subtotal      = 0;
            $discountTotal = 0;
            $taxTotal      = 0;
            
            $saleItemsData    = [];
            $saleDiscountsData = []; // Priority 4: pelacak diskon per transaksi

            // 1. Preload semua variant
            $variantIds = array_keys($items);
            $variants = ProductVariant::with(['product', 'product.taxCategory'])
                ->whereIn('id', $variantIds)
                ->get()
                ->keyBy('id');

            // 2. Preload semua stok untuk VALIDASI (bukan untuk decrement — itu tugas trigger DB).
            //    lockForUpdate dipakai agar transaksi konkuren mengantre dan membaca nilai stok
            //    yang sudah dicommit oleh transaksi sebelumnya, bukan nilai stale.
            $stocks = InventoryStock::where('store_id', $store->id)
                ->whereIn('variant_id', $variantIds)
                ->orderBy('variant_id') // urutan konsisten → cegah deadlock
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

                // Guard validasi stok (sisi PHP) — memberikan pesan error yang jelas ke kasir
                // SEBELUM trigger DB dijalankan. Trigger sendiri juga memvalidasi (defense-in-depth).
                $stock = $stocks->get($variantId);
                    
                if (!$stock || $stock->quantity < $quantity) {
                    throw new Exception("Stok tidak mencukupi untuk: " . $variant->product->name . " (tersedia: " . ($stock?->quantity ?? 0) . ")");
                }

                // ⚠️  JANGAN tambahkan $stock->decrement() di sini.
                //     Pengurangan stok dilakukan OTOMATIS oleh trigger DB:
                //     trg_decrement_stock_on_sale_item (AFTER INSERT ON sale_items)

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
                    'id'              => $saleItemId,
                    'variant_id'      => $variantId,
                    'quantity'        => $quantity,
                    'unit_price'      => $unitPrice,
                    'cost_price'      => $variant->cost_price,
                    'discount'        => $discount,
                    'tax_category_id' => $taxCategoryId,
                    'tax_amount'      => $taxAmount,
                ];
                // ⚠️  JANGAN tambahkan inventory_movements insert di sini.
                //     Trigger trg_decrement_stock_on_sale_item sudah insert ke inventory_movements
                //     secara atomik setelah baris sale_items di atas diinsert.

                // Kumpulkan data diskon untuk di-insert ke sale_discounts (Priority 4)
                if ($discount > 0) {
                    $discountRecord = !empty($itemData['discount_id'])
                        ? $discounts->get($itemData['discount_id'])
                        : null;

                    $saleDiscountsData[] = [
                        'sale_id'       => null, // akan diisi setelah sale_id diketahui
                        'discount_id'   => $discountRecord?->id,
                        'label'         => $discountRecord?->name ?? 'Diskon Manual',
                        'discount_type' => $discountRecord?->type ?? 'fixed',
                        'value'         => $discountRecord?->value ?? $discount,
                        'amount_applied'=> $discount,
                    ];
                }
            }

            $grandTotal = $subtotal - $discountTotal + $taxTotal;
            
            $saleId = Str::uuid()->toString();

            // Priority 6: Nomor transaksi berbasis sequence DB (anti-collision)
            // fn_next_sale_number() menggunakan INSERT ... ON CONFLICT DO UPDATE
            // yang bersifat atomik, menggantikan Str::random(4) yang rawan collision.
            $saleNumber = DB::selectOne('SELECT fn_next_sale_number(?) AS sale_number', [$store->id])->sale_number;
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

            // Insert Sale Items — trigger DB `trg_decrement_stock_on_sale_item` dieksekusi
            // OTOMATIS setelah setiap baris INSERT ini, mengurangi inventory_stock dan
            // mencatat inventory_movements secara atomik.
            // ⚠️  Jangan tambahkan SaleItem::insert() lagi di bawah blok ini.
            foreach ($saleItemsData as $index => $item) {
                $saleItemsData[$index]['sale_id'] = $saleId;
            }
            SaleItem::insert($saleItemsData);

            // Priority 4: Insert sale_discounts (pelacak diskon per transaksi)
            if (!empty($saleDiscountsData)) {
                $now = Carbon::now();
                foreach ($saleDiscountsData as $i => $sd) {
                    $saleDiscountsData[$i]['sale_id']    = $saleId;
                    $saleDiscountsData[$i]['created_at'] = $now;
                }
                DB::table('sale_discounts')->insert($saleDiscountsData);
            }

            // Insert Payment
            if ($amountPaid > 0) {
                Payment::create([
                    'id'             => Str::uuid()->toString(),
                    'sale_id'        => $saleId,
                    'payment_method' => $paymentMethod,
                    'amount'         => min($amountPaid, $grandTotal),
                    'change_amount'  => $changeAmount,
                    'paid_at'        => Carbon::now()
                ]);
            }

            // Priority 5: Insert loyalty_ledger jika ada pelanggan
            $customerId = $data['customer_id'] ?? null;
            if ($customerId) {
                // Earn points: 1 poin per Rp 10.000 yang dibayarkan (bisa dikonfigurasi)
                $pointsEarned = (int) floor($grandTotal / 10000);
                if ($pointsEarned > 0) {
                    DB::table('loyalty_ledger')->insert([
                        'id'            => Str::uuid()->toString(),
                        'customer_id'   => $customerId,
                        'sale_id'       => $saleId,
                        'points_change' => $pointsEarned,
                        'description'   => 'Poin dari transaksi ' . $saleNumber,
                        'created_at'    => Carbon::now(),
                    ]);
                    // customers.loyalty_points diupdate OTOMATIS oleh trigger trg_sync_loyalty_points
                }
            }

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
