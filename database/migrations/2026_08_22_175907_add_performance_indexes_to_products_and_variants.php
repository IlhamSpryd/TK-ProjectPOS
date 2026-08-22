<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Disable transactions for concurrent index creation.
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_product_variants_product_id ON product_variants(product_id)');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_category_store ON products(category_id, store_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_products_category_store');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_product_variants_product_id');
    }
};
