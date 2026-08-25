<?php

namespace App\Services;

use App\Models\Category;
use App\Models\InventoryStock;
use App\Models\ProductVariant;
use App\Models\TaxCategory;
use Illuminate\Support\Facades\Cache;

/**
 * ProductCatalogService — Membangun dan men-cache katalog produk terdenormalisasi
 * yang dioptimalkan untuk layar POS. Menghilangkan query N+1 dengan
 * menyusun struktur array produk beserta stoknya.
 */
class ProductCatalogService
{
    /**
     * Ambil katalog produk lengkap untuk sebuah toko.
     * Cache key: "pos_catalog:{storeId}" — TTL 60 detik.
     *
     * @return array Array siap pakai untuk konsumsi Alpine.js
     */
    public function getCatalog(string $storeId, string $tenantId): array
    {
        return Cache::rememberForever(
            "pos_catalog:{$storeId}",
            fn () => $this->buildCatalog($storeId, $tenantId)
        );
    }

    /**
     * Ambil pemetaan tarif pajak berdasarkan tax_category_id.
     * Jarang berubah — di-cache forever.
     */
    public function getTaxRates(string $tenantId): array
    {
        return Cache::rememberForever(
            "tax_rates:{$tenantId}",
            fn () => TaxCategory::where('tenant_id', $tenantId)
                ->pluck('rate', 'id')
                ->toArray()
        );
    }

    /**
     * Ambil daftar kategori produk. Di-cache forever.
     */
    public function getCategories(string $tenantId): array
    {
        return Cache::rememberForever(
            "categories:{$tenantId}",
            fn () => Category::where('tenant_id', $tenantId)
                ->where('active', true)
                ->whereNull('deleted_at')
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray()
        );
    }

    /**
     * Bersihkan cache katalog (panggil saat stok/produk berubah).
     */
    public function invalidateCatalog(string $storeId): void
    {
        Cache::forget("pos_catalog:{$storeId}");
    }

    /**
     * Bersihkan cache kategori.
     */
    public function invalidateCategories(string $tenantId): void
    {
        Cache::forget("categories:{$tenantId}");
    }

    /**
     * Bersihkan cache tarif pajak.
     */
    public function invalidateTaxRates(string $tenantId): void
    {
        Cache::forget("tax_rates:{$tenantId}");
    }

    /**
     * Bangun katalog produk dari awal.
     * Menggunakan JOIN untuk efisiensi maksimum guna mencegah query N+1.
     */
    private function buildCatalog(string $storeId, string $tenantId): array
    {
        // Query O(1) Database Trip: Gabungkan variant, product, category, dan inventory_stocks secara langsung.
        $variants = ProductVariant::query()
            ->select([
                'product_variants.id',
                'product_variants.sku',
                'product_variants.barcode',
                'product_variants.selling_price',
                'product_variants.attributes',
                'products.name as product_name',
                'products.image_url',
                'products.category_id',
                'products.tax_category_id',
                'products.unit',
                'categories.name as category_name',
                'inventory_stocks.quantity as stock',
            ])
            ->join('products', 'products.id', '=', 'product_variants.product_id')
            ->leftJoin('categories', 'categories.id', '=', 'products.category_id')
            ->leftJoin('inventory_stocks', function ($join) use ($storeId) {
                $join->on('inventory_stocks.variant_id', '=', 'product_variants.id')
                     ->where('inventory_stocks.store_id', '=', $storeId);
            })
            ->where('product_variants.active', true)
            ->where('products.active', true)
            ->whereNull('products.deleted_at')
            ->whereNull('product_variants.deleted_at')
            ->where('product_variants.tenant_id', $tenantId)
            ->orderBy('products.name')
            ->get();

        return $variants->map(fn ($v) => [
            'id' => $v->id,
            'name' => trim($v->product_name . ' ' . ($v->sku ?? '')),
            'productName' => $v->product_name,
            'sku' => $v->sku ?? '',
            'barcode' => $v->barcode ?? '',
            'price' => (float) $v->selling_price,
            'stock' => (int) ($v->stock ?? 0),
            'categoryId' => $v->category_id,
            'categoryName' => $v->category_name ?? '',
            'taxCategoryId' => $v->tax_category_id,
            'imageUrl' => $v->image_url,
            'unit' => $v->unit ?? '',
        ])->toArray();
    }
}
