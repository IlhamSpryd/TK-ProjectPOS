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
        // Trigger 1: Stock Increment on Purchase Order Item Received
        DB::connection('pgsql_admin')->unprepared("
            CREATE OR REPLACE FUNCTION fn_increment_stock_on_po_item_received()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            DECLARE
              v_store_id   uuid;
              v_staff_id   uuid;
              v_track_stock boolean;
            BEGIN
              -- Only trigger when received becomes true and received_quantity > 0
              IF NEW.received = true AND OLD.received IS DISTINCT FROM true AND NEW.received_quantity > 0 THEN
                  -- Get store_id from purchase_orders
                  SELECT store_id INTO v_store_id
                  FROM purchase_orders WHERE id = NEW.purchase_order_id;
                
                  IF v_store_id IS NULL THEN
                    RAISE EXCEPTION 'Purchase Order % tidak ditemukan', NEW.purchase_order_id;
                  END IF;
                
                  SELECT p.track_stock INTO v_track_stock
                  FROM product_variants pv
                  JOIN products p ON p.id = pv.product_id
                  WHERE pv.id = NEW.variant_id;
                
                  IF v_track_stock IS DISTINCT FROM TRUE THEN
                    RETURN NEW; -- item jasa / non-stok, lewati
                  END IF;
                
                  -- Atomic increment
                  UPDATE inventory_stock
                     SET quantity = quantity + NEW.received_quantity,
                         updated_at = now()
                   WHERE variant_id = NEW.variant_id
                     AND store_id = v_store_id;
                
                  IF NOT FOUND THEN
                     -- If stock doesn't exist yet, insert it
                     INSERT INTO inventory_stock (id, store_id, variant_id, quantity, created_at, updated_at)
                     VALUES (gen_random_uuid(), v_store_id, NEW.variant_id, NEW.received_quantity, now(), now());
                  END IF;
                
                  INSERT INTO inventory_movements (
                    id, variant_id, store_id, movement_type, quantity_change,
                    reference_table, reference_id, staff_id, created_at, updated_at
                  ) VALUES (
                    gen_random_uuid(), NEW.variant_id, v_store_id, 'purchase', NEW.received_quantity,
                    'purchase_order_items', NEW.id, get_current_staff_id(), now(), now()
                  );
              END IF;
            
              RETURN NEW;
            END;
            $$;
            
            DROP TRIGGER IF EXISTS trg_increment_stock_on_po_item_received ON purchase_order_items;
            CREATE TRIGGER trg_increment_stock_on_po_item_received
            AFTER UPDATE ON purchase_order_items
            FOR EACH ROW
            EXECUTE FUNCTION fn_increment_stock_on_po_item_received();
        ");

        // Trigger 2: Stock Increment on Sale Return Item Restock
        DB::connection('pgsql_admin')->unprepared("
            CREATE OR REPLACE FUNCTION fn_increment_stock_on_sale_return_item()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            DECLARE
              v_store_id   uuid;
              v_variant_id uuid;
              v_track_stock boolean;
            BEGIN
              IF NEW.restock = true AND NEW.quantity > 0 THEN
                  -- Get store_id from sale_returns -> sales
                  SELECT s.store_id INTO v_store_id
                  FROM sale_returns sr
                  JOIN sales s ON s.id = sr.sale_id
                  WHERE sr.id = NEW.sale_return_id;
                  
                  -- Get variant_id from sale_items
                  SELECT variant_id INTO v_variant_id
                  FROM sale_items
                  WHERE id = NEW.sale_item_id;
                
                  IF v_store_id IS NULL THEN
                    RAISE EXCEPTION 'Sale Return % tidak ditemukan', NEW.sale_return_id;
                  END IF;
                  
                  IF v_variant_id IS NULL THEN
                    RAISE EXCEPTION 'Sale Item % tidak ditemukan', NEW.sale_item_id;
                  END IF;
                
                  SELECT p.track_stock INTO v_track_stock
                  FROM product_variants pv
                  JOIN products p ON p.id = pv.product_id
                  WHERE pv.id = v_variant_id;
                
                  IF v_track_stock IS DISTINCT FROM TRUE THEN
                    RETURN NEW; -- item jasa / non-stok, lewati
                  END IF;
                
                  -- Atomic increment
                  UPDATE inventory_stock
                     SET quantity = quantity + NEW.quantity,
                         updated_at = now()
                   WHERE variant_id = v_variant_id
                     AND store_id = v_store_id;
                
                  IF NOT FOUND THEN
                     INSERT INTO inventory_stock (id, store_id, variant_id, quantity, created_at, updated_at)
                     VALUES (gen_random_uuid(), v_store_id, v_variant_id, NEW.quantity, now(), now());
                  END IF;
                
                  INSERT INTO inventory_movements (
                    id, variant_id, store_id, movement_type, quantity_change,
                    reference_table, reference_id, staff_id, created_at, updated_at
                  ) VALUES (
                    gen_random_uuid(), v_variant_id, v_store_id, 'sale_return', NEW.quantity,
                    'sale_return_items', NEW.id, get_current_staff_id(), now(), now()
                  );
              END IF;
            
              RETURN NEW;
            END;
            $$;
            
            DROP TRIGGER IF EXISTS trg_increment_stock_on_sale_return_item ON sale_return_items;
            CREATE TRIGGER trg_increment_stock_on_sale_return_item
            AFTER INSERT ON sale_return_items
            FOR EACH ROW
            EXECUTE FUNCTION fn_increment_stock_on_sale_return_item();
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared("
            DROP TRIGGER IF EXISTS trg_increment_stock_on_sale_return_item ON sale_return_items;
            DROP FUNCTION IF EXISTS fn_increment_stock_on_sale_return_item();
            
            DROP TRIGGER IF EXISTS trg_increment_stock_on_po_item_received ON purchase_order_items;
            DROP FUNCTION IF EXISTS fn_increment_stock_on_po_item_received();
        ");
    }
};
