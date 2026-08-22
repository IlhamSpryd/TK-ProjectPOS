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
        // Trigger 1: Stock Decrement on Sale Item Insert
        DB::connection('pgsql_admin')->unprepared("
            CREATE OR REPLACE FUNCTION fn_decrement_stock_on_sale_item()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            DECLARE
              v_store_id   uuid;
              v_staff_id   uuid;
              v_track_stock boolean;
              v_new_qty    numeric;
            BEGIN
              -- Ambil store_id & staff_id dari HEADER sales, bukan dari input sale_items
              SELECT store_id, staff_id INTO v_store_id, v_staff_id
              FROM sales WHERE id = NEW.sale_id;
            
              IF v_store_id IS NULL THEN
                RAISE EXCEPTION 'Sale % tidak ditemukan', NEW.sale_id;
              END IF;
            
              SELECT p.track_stock INTO v_track_stock
              FROM product_variants pv
              JOIN products p ON p.id = pv.product_id
              WHERE pv.id = NEW.variant_id;
            
              IF v_track_stock IS DISTINCT FROM TRUE THEN
                RETURN NEW; -- item jasa / non-stok, lewati
              END IF;
            
              -- Atomic decrement
              UPDATE inventory_stock
                 SET quantity = quantity - NEW.quantity,
                     updated_at = now()
               WHERE variant_id = NEW.variant_id
                 AND store_id = v_store_id
              RETURNING quantity INTO v_new_qty;
            
              IF NOT FOUND THEN
                RAISE EXCEPTION 'Baris stok untuk variant % di store % tidak ditemukan', NEW.variant_id, v_store_id;
              END IF;
            
              IF v_new_qty < 0 THEN
                RAISE EXCEPTION 'Stok tidak cukup untuk variant % (sisa setelah transaksi: %)', NEW.variant_id, v_new_qty;
              END IF;
            
              INSERT INTO inventory_movements (
                id, variant_id, store_id, movement_type, quantity_change,
                reference_table, reference_id, staff_id, created_at, updated_at
              ) VALUES (
                gen_random_uuid(), NEW.variant_id, v_store_id, 'sale', -NEW.quantity,
                'sale_items', NEW.id, v_staff_id, now(), now()
              );
            
              RETURN NEW;
            END;
            $$;
            
            DROP TRIGGER IF EXISTS trg_decrement_stock_on_sale_item ON sale_items;
            CREATE TRIGGER trg_decrement_stock_on_sale_item
            AFTER INSERT ON sale_items
            FOR EACH ROW
            EXECUTE FUNCTION fn_decrement_stock_on_sale_item();
        ");

        // Trigger 2: Audit Price Change
        DB::connection('pgsql_admin')->unprepared("
            CREATE OR REPLACE FUNCTION fn_audit_price_change()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            BEGIN
              IF NEW.selling_price IS DISTINCT FROM OLD.selling_price
                 OR NEW.cost_price IS DISTINCT FROM OLD.cost_price THEN
                INSERT INTO audit_logs (id, table_name, record_id, action, old_data, new_data, changed_by, created_at, updated_at)
                VALUES (
                  gen_random_uuid(), 'product_variants', NEW.id, 'PRICE_CHANGE',
                  jsonb_build_object('cost_price', OLD.cost_price, 'selling_price', OLD.selling_price),
                  jsonb_build_object('cost_price', NEW.cost_price, 'selling_price', NEW.selling_price),
                  get_current_staff_id(), now(), now()
                );
              END IF;
              RETURN NEW;
            END;
            $$;
            
            DROP TRIGGER IF EXISTS trg_audit_price_change ON product_variants;
            CREATE TRIGGER trg_audit_price_change
            AFTER UPDATE ON product_variants
            FOR EACH ROW
            EXECUTE FUNCTION fn_audit_price_change();
        ");

        // Trigger 3: Audit Sale Void
        DB::connection('pgsql_admin')->unprepared("
            CREATE OR REPLACE FUNCTION fn_audit_sale_void()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            BEGIN
              -- if it was changed to voided, or if status became void
              -- The schema might not have voided_at but might have status = 'void'
              IF NEW.status = 'void' AND OLD.status IS DISTINCT FROM 'void' THEN
                INSERT INTO audit_logs (id, table_name, record_id, action, old_data, new_data, changed_by, created_at, updated_at)
                VALUES (
                  gen_random_uuid(), 'sales', NEW.id, 'VOID',
                  to_jsonb(OLD), to_jsonb(NEW), get_current_staff_id(), now(), now()
                );
              END IF;
              RETURN NEW;
            END;
            $$;
            
            DROP TRIGGER IF EXISTS trg_audit_sale_void ON sales;
            CREATE TRIGGER trg_audit_sale_void
            AFTER UPDATE ON sales
            FOR EACH ROW
            EXECUTE FUNCTION fn_audit_sale_void();
        ");

        // Revoke direct write access to audit_logs and inventory_stock for staff
        DB::connection('pgsql_admin')->unprepared("
            REVOKE INSERT, UPDATE, DELETE ON audit_logs FROM authenticated;
            REVOKE INSERT, UPDATE, DELETE ON inventory_stock FROM authenticated;
            
            REVOKE INSERT, UPDATE, DELETE ON audit_logs FROM anon;
            REVOKE INSERT, UPDATE, DELETE ON inventory_stock FROM anon;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared("
            DROP TRIGGER IF EXISTS trg_audit_sale_void ON sales;
            DROP FUNCTION IF EXISTS fn_audit_sale_void();
            
            DROP TRIGGER IF EXISTS trg_audit_price_change ON product_variants;
            DROP FUNCTION IF EXISTS fn_audit_price_change();
            
            DROP TRIGGER IF EXISTS trg_decrement_stock_on_sale_item ON sale_items;
            DROP FUNCTION IF EXISTS fn_decrement_stock_on_sale_item();
            
            GRANT INSERT, UPDATE, DELETE ON audit_logs TO authenticated;
            GRANT INSERT, UPDATE, DELETE ON inventory_stock TO authenticated;
        ");
    }
};
