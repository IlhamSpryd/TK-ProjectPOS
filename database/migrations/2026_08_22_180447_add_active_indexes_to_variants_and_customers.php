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
        DB::connection('pgsql_admin')->unprepared('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_product_variants_active ON product_variants(active)');
        DB::connection('pgsql_admin')->unprepared('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_customers_active_name ON customers(active, name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared('DROP INDEX CONCURRENTLY IF EXISTS idx_customers_active_name');
        DB::connection('pgsql_admin')->unprepared('DROP INDEX CONCURRENTLY IF EXISTS idx_product_variants_active');
    }
};
