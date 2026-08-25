<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Partial Index for Open Held Orders
        // Accelerates POS fetching open orders for F&B stores.
        DB::statement("CREATE INDEX sales_open_orders_idx ON sales (store_id, sale_date DESC) WHERE status = 'open'");

        // 2. Covering Index for Category Active checks
        // Supports: SELECT * FROM categories WHERE tenant_id = ? AND active = true ORDER BY name
        DB::statement("CREATE INDEX idx_categories_tenant_active_name ON categories (tenant_id, active, name)");

        Schema::table('product_variants', function (Blueprint $table) {
            $table->index(['tenant_id', 'active', 'deleted_at'], 'idx_product_variants_active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->index(['tenant_id', 'active', 'deleted_at'], 'idx_products_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("DROP INDEX IF EXISTS sales_open_orders_idx");
        DB::statement("DROP INDEX IF EXISTS idx_categories_tenant_active_name");

        Schema::table('product_variants', function (Blueprint $table) {
            $table->dropIndex('idx_product_variants_active');
        });

        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_active');
        });
    }
};
