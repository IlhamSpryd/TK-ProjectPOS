<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Set withinTransaction to false so we can run CREATE INDEX CONCURRENTLY.
     */
    public $withinTransaction = false;

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // PENCARIAN PRODUK
        DB::connection('pgsql_admin')->unprepared("CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS idx_variants_barcode ON product_variants (barcode) WHERE deleted_at IS NULL AND barcode IS NOT NULL;");
        DB::connection('pgsql_admin')->unprepared("CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS idx_variants_sku ON product_variants (sku) WHERE deleted_at IS NULL;");
        
        // Requires superuser or DB owner to create extensions in Postgres.
        DB::connection('pgsql_admin')->unprepared("CREATE EXTENSION IF NOT EXISTS pg_trgm;");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_products_name_trgm ON products USING gin (name gin_trgm_ops);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_variants_barcode;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_variants_sku;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_products_name_trgm;");
        // Extension pg_trgm is not dropped to avoid breaking other parts of the DB that might rely on it.
    }
};
