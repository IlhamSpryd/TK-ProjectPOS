<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

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
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_product_variants_active ON product_variants(active)');
        DB::statement('CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_customers_active_name ON customers(active, name)');
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_customers_active_name');
        DB::statement('DROP INDEX CONCURRENTLY IF EXISTS idx_product_variants_active');
    }
};
