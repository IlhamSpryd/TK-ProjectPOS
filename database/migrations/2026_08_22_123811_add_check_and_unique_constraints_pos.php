<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add Check Constraints
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_status_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales ADD CONSTRAINT sales_status_check CHECK (status IN ('draft', 'open', 'completed', 'void', 'refunded')) NOT VALID;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales VALIDATE CONSTRAINT sales_status_check;");

        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_payment_status_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales ADD CONSTRAINT sales_payment_status_check CHECK (payment_status IN ('unpaid', 'partial', 'paid', 'refunded')) NOT VALID;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales VALIDATE CONSTRAINT sales_payment_status_check;");

        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sale_items DROP CONSTRAINT IF EXISTS sale_items_quantity_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sale_items ADD CONSTRAINT sale_items_quantity_check CHECK (quantity > 0) NOT VALID;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sale_items VALIDATE CONSTRAINT sale_items_quantity_check;");

        DB::connection('pgsql_admin')->unprepared("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_amount_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE payments ADD CONSTRAINT payments_amount_check CHECK (amount >= 0) NOT VALID;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE payments VALIDATE CONSTRAINT payments_amount_check;");

        // The UNIQUE indices for sales and inventory_stock were handled in the previous index migration using CONCURRENTLY.
        // Wait, I put them in the concurrent indices migration to avoid locking the tables!
        // But if I want true constraints, I could add them here using ALTER TABLE ADD CONSTRAINT USING INDEX...
        
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_store_number_unique;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales ADD CONSTRAINT sales_store_number_unique UNIQUE USING INDEX idx_sales_store_number;");
        
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE inventory_stock DROP CONSTRAINT IF EXISTS inventory_stock_variant_store_unique;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE inventory_stock ADD CONSTRAINT inventory_stock_variant_store_unique UNIQUE USING INDEX idx_inventory_stock_variant_store;");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_status_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_payment_status_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sale_items DROP CONSTRAINT IF EXISTS sale_items_quantity_check;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE payments DROP CONSTRAINT IF EXISTS payments_amount_check;");

        DB::connection('pgsql_admin')->unprepared("ALTER TABLE sales DROP CONSTRAINT IF EXISTS sales_store_number_unique;");
        DB::connection('pgsql_admin')->unprepared("ALTER TABLE inventory_stock DROP CONSTRAINT IF EXISTS inventory_stock_variant_store_unique;");
    }
};
