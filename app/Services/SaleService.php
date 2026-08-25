<?php

namespace App\Services;

use App\Models\Discount;
use App\Models\InventoryStock;
use App\Models\Payment;
use App\Models\ProductVariant;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\Staff;
use App\Models\Store;
use App\Models\TaxCategory;
use Carbon\Carbon;
use App\Exceptions\BusinessException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use InvalidArgumentException;

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
     * Buat transaksi penjualan baru (selesai/lunas).
     *
     * @param  array  $data   Data keranjang dan pembayaran
     * @param  Staff  $staff  Karyawan yang bertugas
     * @throws BusinessException
     */
    public function createSale(array $data, Staff $staff): Sale
    {
        return $this->processTransaction($data, $staff, 'completed');
    }

    /**
     * Buat pesanan baru dengan status terbuka (hold).
     *
     * @param  array  $data   Data keranjang
     * @param  Staff  $staff  Karyawan yang bertugas
     * @throws BusinessException
     */
    public function openOrder(array $data, Staff $staff): Sale
    {
        return $this->processTransaction($data, $staff, 'open');
    }

    /**
     * Selesaikan pembayaran untuk pesanan yang masih terbuka (hold).
     *
     * @param  Sale   $sale         Pesanan yang terbuka
     * @param  array  $paymentData  Detail pembayaran
     * @param  Staff  $staff        Karyawan yang bertugas
     * @throws BusinessException
     */
    public function finalizePayment(Sale $sale, array $paymentData, Staff $staff): Sale
    {
        return DB::transaction(function () use ($sale, $paymentData, $staff) {
            // Kunci baris sale untuk mencegah finalisasi ganda (race condition)
            $lockedSale = Sale::lockForUpdate()->findOrFail($sale->id);

            if ($lockedSale->status !== 'open') {
                throw new BusinessException("Pesanan tidak dalam status open (status saat ini: {$lockedSale->status}).");
            }

            $grandTotal = $lockedSale->grand_total;
            $paymentMethod = $paymentData['payment_method'] ?? 'cash';
            $amountPaid = floatval($paymentData['cash_received'] ?? $grandTotal);
            $changeAmount = max(0, $amountPaid - $grandTotal);
            $paymentStatus = ($amountPaid >= $grandTotal) ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid');

            // Perbarui data Sale
            $lockedSale->status = 'completed';
            $lockedSale->payment_status = $paymentStatus;
            $lockedSale->voided_at = null;
            $lockedSale->save();

            // Simpan pembayaran
            $this->insertPayment($staff->tenant_id, $lockedSale->id, $paymentMethod, $amountPaid, $grandTotal, $changeAmount);

            // Perbarui poin loyalitas pelanggan
            $this->insertLoyaltyPoints($staff->tenant_id, $lockedSale->customer_id, $lockedSale->id, $lockedSale->sale_number, $grandTotal);

            return $lockedSale->load(['items.variant.product', 'payments']);
        });
    }

    /**
     * Proses inti transaksi, digunakan oleh createSale dan openOrder.
     */
    private function processTransaction(array $data, Staff $staff, string $status): Sale
    {
        $store = $staff->getActiveStore();
        if (! $store) {
            throw new BusinessException('Staff tidak memiliki store aktif.');
        }

        $items = $data['cart'] ?? [];
        if (empty($items)) {
            throw new InvalidArgumentException('Keranjang belanja kosong.');
        }

        return DB::transaction(function () use ($data, $staff, $store, $items, $status) {
            $saleId = Str::uuid()->toString();
            $processed = $this->processItems($items, $store, $staff, $saleId);

            $grandTotal = $processed['grandTotal'];
            $saleNumber = DB::selectOne('SELECT fn_next_sale_number(?) AS sale_number', [$store->id])->sale_number;
            
            $paymentMethod = $data['payment_method'] ?? 'cash';
            $amountPaid = floatval($data['cash_received'] ?? $grandTotal);
            $changeAmount = max(0, $amountPaid - $grandTotal);

            // Jika status open, otomatis unpaid, jika tidak, cek jumlah pembayaran
            $paymentStatus = ($status === 'open') ? 'unpaid' : (($amountPaid >= $grandTotal) ? 'paid' : ($amountPaid > 0 ? 'partial' : 'unpaid'));

            // Simpan Sale
            $sale = new Sale();
            $sale->id = $saleId;
            $sale->tenant_id = $staff->tenant_id;
            $sale->store_id = $store->id;
            $sale->customer_id = $data['customer_id'] ?? null;
            $sale->staff_id = $staff->id;
            $sale->sale_number = $saleNumber;
            $sale->sale_date = Carbon::now();
            $sale->status = $status;
            $sale->subtotal = $processed['subtotal'];
            $sale->discount_total = $processed['discountTotal'];
            $sale->tax_total = $processed['taxTotal'];
            $sale->service_charge_total = 0;
            $sale->grand_total = $grandTotal;
            $sale->payment_status = $paymentStatus;
            $sale->notes = $data['notes'] ?? null;
            $sale->table_number = $data['table_number'] ?? null;
            $sale->save();

            // Simpan Sale Items (Ini akan memicu DB Trigger untuk memotong stok)
            SaleItem::insert($processed['saleItemsData']);

            // Simpan Diskon Penjualan
            if (! empty($processed['saleDiscountsData'])) {
                $now = Carbon::now();
                $discountsData = array_map(function ($sd) use ($now) {
                    $sd['created_at'] = $now;
                    return $sd;
                }, $processed['saleDiscountsData']);
                
                DB::table('sale_discounts')->insert($discountsData);
            }

            // Simpan Pembayaran (Hanya jika status bukan open)
            if ($status !== 'open') {
                $this->insertPayment($staff->tenant_id, $saleId, $paymentMethod, $amountPaid, $grandTotal, $changeAmount);
                $this->insertLoyaltyPoints($staff->tenant_id, $sale->customer_id, $saleId, $saleNumber, $grandTotal);
                return $sale->load(['items.variant.product', 'payments']);
            }

            return $sale->load(['items.variant.product']);
        });
    }

    /**
     * Hitung total belanjaan dan siapkan data item untuk disimpan ke database.
     */
    private function processItems(array $items, Store $store, Staff $staff, string $saleId): array
    {
        $subtotal = 0;
        $discountTotal = 0;
        $taxTotal = 0;
        $saleItemsData = [];
        $saleDiscountsData = [];

        $variantIds = array_keys($items);
        $variants = ProductVariant::with(['product', 'product.taxCategory'])
            ->whereIn('id', $variantIds)
            ->get()
            ->keyBy('id');

        $stocks = InventoryStock::where('store_id', $store->id)
            ->whereIn('variant_id', $variantIds)
            ->orderBy('variant_id')
            ->lockForUpdate() // Cegah race condition saat mengambil stok
            ->get()
            ->keyBy('variant_id');

        $discountIds = collect($items)->pluck('discount_id')->filter()->unique()->toArray();
        $discounts = ! empty($discountIds)
            ? Discount::whereIn('id', $discountIds)->where('active', true)->get()->keyBy('id')
            : collect();

        $taxCategoryIds = $variants->map(fn($v) => $v->product->tax_category_id ?? $store->default_tax_category_id)->filter()->unique()->toArray();
        $taxCategories = ! empty($taxCategoryIds)
            ? TaxCategory::whereIn('id', $taxCategoryIds)->get()->keyBy('id')
            : collect();

        foreach ($items as $variantId => $itemData) {
            $variant = $variants->get($variantId);
            if (! $variant) {
                throw new BusinessException("Produk variant dengan ID {$variantId} tidak ditemukan.");
            }

            $quantity = $itemData['quantity'];

            // Validasi kuantitas: harus numerik dan lebih dari 0
            if (!is_numeric($quantity) || (int)$quantity <= 0) {
                throw new BusinessException(
                    "Kuantitas tidak valid untuk produk: {$variant->product->name}. Kuantitas harus lebih dari 0."
                );
            }
            $quantity = (int) $quantity;
            
            $unitPrice = $variant->selling_price;
            $discount = $this->calculateItemDiscount($itemData, $discounts, $unitPrice, $quantity);
            $stock = $stocks->get($variantId);

            if (! $stock || $stock->quantity < $quantity) {
                throw new BusinessException("Stok tidak mencukupi untuk: {$variant->product->name} (tersedia: " . ($stock?->quantity ?? 0) . ")");
            }

            $taxCategoryId = $variant->product->tax_category_id ?? $store->default_tax_category_id;
            $taxAmount = 0;

            if ($taxCategoryId) {
                $taxCategory = $taxCategories->get($taxCategoryId);
                if ($taxCategory) {
                    $this->validateTaxCombination($store, $taxCategory);
                    $amountBeforeTax = ($unitPrice * $quantity) - $discount;
                    $taxAmount = $amountBeforeTax * ($taxCategory->rate / 100);
                } else {
                    $taxCategoryId = null;
                }
            }

            $subtotal += ($unitPrice * $quantity);
            $discountTotal += $discount;
            $taxTotal += $taxAmount;

            $saleItemsData[] = $this->buildSaleItemData($staff->tenant_id, $saleId, $variant, $quantity, $unitPrice, $discount, $taxCategoryId, $taxCategory ?? null, $taxAmount, $itemData['modifiers'] ?? []);

            if ($discount > 0) {
                $discountRecord = ! empty($itemData['discount_id']) ? $discounts->get($itemData['discount_id']) : null;
                $saleDiscountsData[] = [
                    'sale_id' => $saleId,
                    'tenant_id' => $staff->tenant_id,
                    'discount_id' => $discountRecord?->id,
                    'label' => $discountRecord?->name ?? 'Diskon Manual',
                    'discount_type' => $discountRecord?->type ?? 'fixed',
                    'value' => $discountRecord?->value ?? $discount,
                    'amount_applied' => $discount,
                ];
            }
        }

        return [
            'saleItemsData' => $saleItemsData,
            'saleDiscountsData' => $saleDiscountsData,
            'subtotal' => $subtotal,
            'discountTotal' => $discountTotal,
            'taxTotal' => $taxTotal,
            'grandTotal' => $subtotal - $discountTotal + $taxTotal,
        ];
    }

    /**
     * Hitung diskon per item (baik diskon persentase maupun nominal tetap).
     */
    private function calculateItemDiscount(array $itemData, $discounts, float $unitPrice, int $quantity): float
    {
        if (! empty($itemData['discount_id'])) {
            $discountRecord = $discounts->get($itemData['discount_id']);
            if ($discountRecord) {
                return $discountRecord->type === 'percentage'
                    ? ($unitPrice * $quantity) * ($discountRecord->value / 100)
                    : floatval($discountRecord->value);
            }
        } elseif (! empty($itemData['discount'])) {
            return min(floatval($itemData['discount']), $unitPrice * $quantity);
        }
        return 0;
    }

    /**
     * Susun array data untuk bulk insert ke tabel sale_items.
     */
    private function buildSaleItemData(string $tenantId, string $saleId, ProductVariant $variant, int $quantity, float $unitPrice, float $discount, ?string $taxCategoryId, ?TaxCategory $taxCategory, float $taxAmount, array $modifiers): array
    {
        return [
            'id' => Str::uuid()->toString(),
            'tenant_id' => $tenantId,
            'sale_id' => $saleId,
            'product_id' => $variant->product_id,
            'product_name' => $variant->product->name,
            'variant_id' => $variant->id,
            'variant_sku' => $variant->sku,
            'variant_attributes' => json_encode($variant->attributes ?? []),
            'quantity' => $quantity,
            'unit' => $variant->product->unit,
            'unit_price' => $unitPrice,
            'cost_price' => $variant->cost_price,
            'discount' => $discount,
            'tax_category_id' => $taxCategoryId,
            'tax_name' => $taxCategory ? $taxCategory->name : null,
            'tax_rate' => $taxCategory ? $taxCategory->rate : 0,
            'tax_amount' => $taxAmount,
            'modifiers' => json_encode($modifiers),
        ];
    }

    /**
     * Simpan data pembayaran (Payment) ke database.
     */
    private function insertPayment(string $tenantId, string $saleId, string $paymentMethod, float $amountPaid, float $grandTotal, float $changeAmount): void
    {
        if ($amountPaid > 0) {
            Payment::create([
                'id' => Str::uuid()->toString(),
                'tenant_id' => $tenantId,
                'sale_id' => $saleId,
                'payment_method' => $paymentMethod,
                'amount' => min($amountPaid, $grandTotal),
                'change_amount' => $changeAmount,
                'paid_at' => Carbon::now(),
            ]);
        }
    }

    /**
     * Simpan poin loyalitas pelanggan jika memenuhi syarat (setiap Rp10.000 = 1 poin).
     */
    private function insertLoyaltyPoints(string $tenantId, ?string $customerId, string $saleId, string $saleNumber, float $grandTotal): void
    {
        if ($customerId) {
            $pointsEarned = (int) floor($grandTotal / 10000);
            if ($pointsEarned > 0) {
                DB::table('loyalty_ledger')->insert([
                    'id' => Str::uuid()->toString(),
                    'tenant_id' => $tenantId,
                    'customer_id' => $customerId,
                    'sale_id' => $saleId,
                    'points_change' => $pointsEarned,
                    'description' => "Poin dari transaksi {$saleNumber}",
                    'created_at' => Carbon::now(),
                ]);
            }
        }
    }

    /**
     * Validasi aturan perpajakan (PPN / PBJT) terhadap tipe bisnis toko.
     * @throws BusinessException
     */
    private function validateTaxCombination(Store $store, TaxCategory $taxCategory): void
    {
        $taxType = strtoupper($taxCategory->tax_type);
        $businessType = strtoupper($store->business_type);

        if ($businessType === 'F&B' && $taxType === 'PPN') {
            throw new BusinessException('Toko F&B tidak boleh dikenakan PPN.');
        }

        if (! $store->is_pkp && $taxType === 'PPN') {
            throw new BusinessException('Toko berstatus Non-PKP tidak boleh memungut PPN.');
        }

        if ($businessType !== 'F&B' && $taxType === 'PBJT') {
            throw new BusinessException('Toko Non-F&B (Retail) tidak boleh dikenakan PBJT.');
        }
    }
}
