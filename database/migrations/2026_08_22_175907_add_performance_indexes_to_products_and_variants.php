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
        DB::connection('pgsql_admin')->unprepared('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_product_variants_product_id ON product_variants(product_id)');
        DB::connection('pgsql_admin')->unprepared('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_category_tenant ON products(category_id, tenant_id)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared('DROP INDEX CONCURRENTLY IF EXISTS idx_products_category_tenant');
        DB::connection('pgsql_admin')->unprepared('DROP INDEX CONCURRENTLY IF EXISTS idx_product_variants_product_id');
    }
};
