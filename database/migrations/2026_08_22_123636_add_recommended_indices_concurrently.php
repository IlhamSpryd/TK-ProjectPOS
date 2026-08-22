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
        // FK YANG WAJIB DI-INDEX
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_sale_items_sale_id ON sale_items (sale_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_sale_items_variant_id ON sale_items (variant_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_payments_sale_id ON payments (sale_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_shifts_register_id ON shifts (register_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_purchase_order_items_po_id ON purchase_order_items (purchase_order_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_sale_returns_sale_id ON sale_returns (sale_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_sale_return_items_return_id ON sale_return_items (sale_return_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_staff_stores_store_id ON staff_stores (store_id);");

        // LAPORAN KEUANGAN
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_sales_store_date ON sales (store_id, sale_date DESC);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_sales_store_status ON sales (store_id, status);");
        // For idx_sales_store_number, using a UNIQUE INDEX CONCURRENTLY
        DB::connection('pgsql_admin')->unprepared("CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS idx_sales_store_number ON sales (store_id, sale_number);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_expenses_store_date ON expenses (store_id, expense_date DESC);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_purchase_orders_store_status ON purchase_orders (store_id, status);");

        // STOK
        // Using UNIQUE INDEX CONCURRENTLY for idx_inventory_stock_variant_store
        DB::connection('pgsql_admin')->unprepared("CREATE UNIQUE INDEX CONCURRENTLY IF NOT EXISTS idx_inventory_stock_variant_store ON inventory_stock (variant_id, store_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_inventory_movements_variant_store_date ON inventory_movements (variant_id, store_id, created_at DESC);");

        // AUDIT LOG
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_audit_logs_table_record ON audit_logs (table_name, record_id);");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_audit_logs_changed_by ON audit_logs (changed_by);");

        // PELANGGAN
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_customers_phone ON customers (phone) WHERE deleted_at IS NULL AND phone IS NOT NULL;");
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_customers_active ON customers (id) WHERE deleted_at IS NULL;");

        // JSONB
        DB::connection('pgsql_admin')->unprepared("CREATE INDEX CONCURRENTLY IF NOT EXISTS idx_roles_permissions_gin ON roles USING gin (permissions);");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sale_items_sale_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sale_items_variant_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_payments_sale_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_shifts_register_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_purchase_order_items_po_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sale_returns_sale_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sale_return_items_return_id;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_staff_stores_store_id;");

        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sales_store_date;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sales_store_status;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_sales_store_number;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_expenses_store_date;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_purchase_orders_store_status;");

        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_inventory_stock_variant_store;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_inventory_movements_variant_store_date;");

        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_audit_logs_table_record;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_audit_logs_changed_by;");

        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_customers_phone;");
        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_customers_active;");

        DB::connection('pgsql_admin')->unprepared("DROP INDEX CONCURRENTLY IF EXISTS idx_roles_permissions_gin;");
    }
};
