<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $sql = "
            CREATE OR REPLACE FUNCTION is_staff_of_store(p_store_id UUID)
            RETURNS BOOLEAN AS $$
              SELECT EXISTS (
                SELECT 1 FROM staff_stores 
                WHERE staff_id = get_current_staff_id() 
                  AND store_id = p_store_id
              );
            $$ LANGUAGE sql SECURITY DEFINER;

            -- ================= SALES =================
            DROP POLICY IF EXISTS sales_scoped ON sales;

            CREATE POLICY sales_select ON sales
              FOR SELECT USING (is_staff_of_store(store_id));

            CREATE POLICY sales_insert ON sales
              FOR INSERT WITH CHECK (
                is_staff_of_store(store_id) AND staff_id = get_current_staff_id()
              );

            CREATE POLICY sales_update_open ON sales
              FOR UPDATE
              USING (is_staff_of_store(store_id) AND status IN ('draft','open'))
              WITH CHECK (is_staff_of_store(store_id));

            CREATE POLICY sales_update_admin ON sales
              FOR UPDATE USING (is_admin()) WITH CHECK (is_admin());

            CREATE POLICY sales_lock_completed ON sales
              AS RESTRICTIVE
              FOR UPDATE USING (status <> 'completed' OR is_admin());

            -- ================= PAYMENTS =================
            DROP POLICY IF EXISTS payments_scoped ON payments;

            CREATE POLICY payments_select ON payments
              FOR SELECT USING (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = payments.sale_id
                AND is_staff_of_store(sales.store_id)
              ));

            CREATE POLICY payments_insert ON payments
              FOR INSERT WITH CHECK (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = payments.sale_id
                AND is_staff_of_store(sales.store_id) AND sales.status IN ('draft','open')
              ));

            CREATE POLICY payments_admin_all ON payments
              FOR ALL USING (is_admin()) WITH CHECK (is_admin());

            -- ================= SHIFTS =================
            DROP POLICY IF EXISTS shifts_scoped ON shifts;

            CREATE POLICY shifts_select ON shifts FOR SELECT USING (EXISTS (
              SELECT 1 FROM registers r WHERE r.id = shifts.register_id AND is_staff_of_store(r.store_id)
            ));
            
            CREATE POLICY shifts_insert ON shifts FOR INSERT WITH CHECK (EXISTS (
              SELECT 1 FROM registers r WHERE r.id = shifts.register_id AND is_staff_of_store(r.store_id)
            ));
            
            CREATE POLICY shifts_update_open ON shifts FOR UPDATE
              USING (closed_at IS NULL AND EXISTS (
                SELECT 1 FROM registers r WHERE r.id = shifts.register_id AND is_staff_of_store(r.store_id)
              ));
              
            CREATE POLICY shifts_admin_all ON shifts
              FOR ALL USING (is_admin()) WITH CHECK (is_admin());
              
            CREATE POLICY shifts_lock_closed ON shifts
              AS RESTRICTIVE
              FOR UPDATE USING (closed_at IS NULL OR is_admin());

            -- ================= EXPENSES =================
            DROP POLICY IF EXISTS expenses_scoped ON expenses;
            
            CREATE POLICY expenses_select ON expenses
              FOR SELECT USING (is_staff_of_store(store_id));
              
            CREATE POLICY expenses_insert ON expenses
              FOR INSERT WITH CHECK (is_staff_of_store(store_id));
              
            CREATE POLICY expenses_admin_all ON expenses
              FOR ALL USING (is_admin()) WITH CHECK (is_admin());

            -- ================= SALE RETURNS =================
            DROP POLICY IF EXISTS sale_returns_scoped ON sale_returns;
            
            CREATE POLICY sale_returns_select ON sale_returns
              FOR SELECT USING (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = sale_returns.sale_id
                AND is_staff_of_store(sales.store_id)
              ));
              
            CREATE POLICY sale_returns_insert ON sale_returns
              FOR INSERT WITH CHECK (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = sale_returns.sale_id
                AND is_staff_of_store(sales.store_id)
              ));
              
            CREATE POLICY sale_returns_admin_all ON sale_returns
              FOR ALL USING (is_admin()) WITH CHECK (is_admin());

            -- ================= FIXING LEAKY INSERTS =================
            DROP POLICY IF EXISTS inventory_movements_insert ON inventory_movements;
            CREATE POLICY inventory_movements_insert ON inventory_movements
              FOR INSERT WITH CHECK (
                is_staff_of_store(store_id)
                AND staff_id = get_current_staff_id()
              );

            DROP POLICY IF EXISTS customers_insert ON customers;
            CREATE POLICY customers_insert ON customers
              FOR INSERT WITH CHECK (get_current_staff_id() IS NOT NULL);
        ";
        DB::connection('pgsql_admin')->unprepared($sql);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $sql = "
            -- Restore Sales
            DROP POLICY IF EXISTS sales_select ON sales;
            DROP POLICY IF EXISTS sales_insert ON sales;
            DROP POLICY IF EXISTS sales_update_open ON sales;
            DROP POLICY IF EXISTS sales_update_admin ON sales;
            DROP POLICY IF EXISTS sales_lock_completed ON sales;
            CREATE POLICY sales_scoped ON sales FOR ALL USING (is_staff_of_store(store_id)) WITH CHECK (is_staff_of_store(store_id));
            
            -- Restore Payments
            DROP POLICY IF EXISTS payments_select ON payments;
            DROP POLICY IF EXISTS payments_insert ON payments;
            DROP POLICY IF EXISTS payments_admin_all ON payments;
            CREATE POLICY payments_scoped ON payments FOR ALL USING (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = payments.sale_id AND is_staff_of_store(sales.store_id)
            )) WITH CHECK (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = payments.sale_id AND is_staff_of_store(sales.store_id)
            ));
            
            -- Restore Shifts
            DROP POLICY IF EXISTS shifts_select ON shifts;
            DROP POLICY IF EXISTS shifts_insert ON shifts;
            DROP POLICY IF EXISTS shifts_update_open ON shifts;
            DROP POLICY IF EXISTS shifts_admin_all ON shifts;
            DROP POLICY IF EXISTS shifts_lock_closed ON shifts;
            CREATE POLICY shifts_scoped ON shifts FOR ALL USING (EXISTS (
                SELECT 1 FROM registers r WHERE r.id = shifts.register_id AND is_staff_of_store(r.store_id)
            )) WITH CHECK (EXISTS (
                SELECT 1 FROM registers r WHERE r.id = shifts.register_id AND is_staff_of_store(r.store_id)
            ));
            
            -- Restore Expenses
            DROP POLICY IF EXISTS expenses_select ON expenses;
            DROP POLICY IF EXISTS expenses_insert ON expenses;
            DROP POLICY IF EXISTS expenses_admin_all ON expenses;
            CREATE POLICY expenses_scoped ON expenses FOR ALL USING (is_staff_of_store(store_id)) WITH CHECK (is_staff_of_store(store_id));
            
            -- Restore Sale Returns
            DROP POLICY IF EXISTS sale_returns_select ON sale_returns;
            DROP POLICY IF EXISTS sale_returns_insert ON sale_returns;
            DROP POLICY IF EXISTS sale_returns_admin_all ON sale_returns;
            CREATE POLICY sale_returns_scoped ON sale_returns FOR ALL USING (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = sale_returns.sale_id AND is_staff_of_store(sales.store_id)
            )) WITH CHECK (EXISTS (
                SELECT 1 FROM sales WHERE sales.id = sale_returns.sale_id AND is_staff_of_store(sales.store_id)
            ));
            
            -- Restore Leaky Inserts
            DROP POLICY IF EXISTS inventory_movements_insert ON inventory_movements;
            CREATE POLICY inventory_movements_insert ON inventory_movements FOR INSERT WITH CHECK (true);
            
            DROP POLICY IF EXISTS customers_insert ON customers;
            CREATE POLICY customers_insert ON customers FOR INSERT WITH CHECK (true);
        ";
        DB::connection('pgsql_admin')->unprepared($sql);
    }
};
