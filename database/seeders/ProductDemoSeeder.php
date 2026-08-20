<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\InventoryStock;
use App\Models\InventoryMovement;
use App\Models\Store;
use Carbon\Carbon;

class ProductDemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $store = Store::first();
        if (!$store) {
            $this->command->warn('No store found. Ensure a store exists before running ProductDemoSeeder.');
            return;
        }

        // Kategori Data
        $categories = [
            'Makanan',
            'Minuman',
            'Elektronik',
        ];

        $categoryIds = [];
        foreach ($categories as $catName) {
            $cat = Category::where('name', $catName)->first();
            if (!$cat) {
                $cat = Category::create([
                    'id' => Str::uuid()->toString(),
                    'name' => $catName,
                    'active' => true,
                ]);
            }
            $categoryIds[$catName] = $cat->id;
        }

        // Produk Data
        $products = [
            [
                'name' => 'Nasi Goreng Spesial',
                'cat' => 'Makanan',
                'sku_base' => 'NG-001',
                'price' => 25000,
                'cost' => 15000,
            ],
            [
                'name' => 'Es Teh Manis',
                'cat' => 'Minuman',
                'sku_base' => 'ET-001',
                'price' => 5000,
                'cost' => 2000,
            ],
            [
                'name' => 'Kopi Susu Gula Aren',
                'cat' => 'Minuman',
                'sku_base' => 'KPG-001',
                'price' => 18000,
                'cost' => 8000,
            ],
            [
                'name' => 'Mouse Wireless Logitech',
                'cat' => 'Elektronik',
                'sku_base' => 'MWS-LOGI',
                'price' => 150000,
                'cost' => 100000,
            ],
        ];

        foreach ($products as $prodInfo) {
            $product = Product::where('sku', $prodInfo['sku_base'])->first();
            if (!$product) {
                $product = Product::create([
                    'id' => Str::uuid()->toString(),
                    'sku' => $prodInfo['sku_base'],
                    'category_id' => $categoryIds[$prodInfo['cat']],
                    'name' => $prodInfo['name'],
                    'active' => true,
                    'track_stock' => true
                ]);
            }

            $variant = ProductVariant::where('sku', $prodInfo['sku_base'] . '-1')->first();
            if (!$variant) {
                $variant = ProductVariant::create([
                    'id' => Str::uuid()->toString(),
                    'sku' => $prodInfo['sku_base'] . '-1',
                    'product_id' => $product->id,
                    'cost_price' => $prodInfo['cost'],
                    'selling_price' => $prodInfo['price'],
                    'active' => true
                ]);
            }

            // Check if movement for this variant already exists to prevent duplicate
            $movement = InventoryMovement::where('variant_id', $variant->id)
                ->where('note', 'Initial Stock Seeder')
                ->first();
                
            if (!$movement) {
                // Add stock via movement
                $movementId = Str::uuid()->toString();
                InventoryMovement::create([
                    'id' => $movementId,
                    'variant_id' => $variant->id,
                    'store_id' => $store->id,
                    'movement_type' => 'adjustment', // Or 'purchase'
                    'quantity_change' => 50,
                    'reference_table' => 'seeder',
                    'reference_id' => Str::uuid()->toString(),
                    'note' => 'Initial Stock Seeder'
                ]);
                
                $stock = InventoryStock::where('store_id', $store->id)
                            ->where('variant_id', $variant->id)
                            ->first();
                if ($stock) {
                    $stock->quantity += 50;
                    $stock->save();
                } else {
                    InventoryStock::create([
                        'id' => Str::uuid()->toString(),
                        'store_id' => $store->id,
                        'variant_id' => $variant->id,
                        'quantity' => 50,
                        'reorder_point' => 10,
                    ]);
                }
            }
        }

        $this->command->info('Produk demo berhasil dibuat.');
    }
}
