<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * 
     * Membuat:
     * 1. sale_discounts  — pelacak diskon per transaksi (audit A.1.1)
     * 2. loyalty_ledger  — ledger poin loyalty pelanggan (audit A.1.3)
     * 3. Trigger DB untuk update customers.loyalty_points dari ledger
     * 4. Sequence-based sale number generator (audit: anti-collision Priority 6)
     */
    public function up(): void
    {
        // ─── 1. Tabel sale_discounts ────────────────────────────────────────────
        DB::connection('pgsql_admin')->unprepared("
            CREATE TABLE IF NOT EXISTS sale_discounts (
                id            uuid         PRIMARY KEY DEFAULT gen_random_uuid(),
                sale_id       uuid         NOT NULL REFERENCES sales(id)       ON DELETE CASCADE,
                discount_id   uuid         REFERENCES discounts(id)            ON DELETE SET NULL,
                label         varchar(255),           -- snapshot nama diskon saat dipakai
                discount_type varchar(50)  NOT NULL,  -- 'percentage' | 'fixed'
                value         numeric(15,4) NOT NULL, -- nilai diskon (persen atau nominal)
                amount_applied numeric(15,4) NOT NULL, -- rupiah yang dipotong
                created_at    timestamptz  NOT NULL DEFAULT now()
            );

            -- Index untuk query 'diskon apa saja yang dipakai di transaksi X?'
            CREATE INDEX IF NOT EXISTS idx_sale_discounts_sale_id
                ON sale_discounts (sale_id);

            -- Revoke akses API publik (PostgREST) — tabel internal
            REVOKE ALL ON sale_discounts FROM anon, authenticated;

            -- RLS: staff hanya bisa insert/select diskon di toko mereka sendiri (via sales)
            ALTER TABLE sale_discounts ENABLE ROW LEVEL SECURITY;
            ALTER TABLE sale_discounts FORCE ROW LEVEL SECURITY;

            DROP POLICY IF EXISTS sale_discounts_select ON sale_discounts;
            CREATE POLICY sale_discounts_select ON sale_discounts
                FOR SELECT USING (
                    EXISTS (
                        SELECT 1 FROM sales s
                        WHERE s.id = sale_discounts.sale_id
                          AND is_staff_of_store(s.store_id)
                    )
                );

            DROP POLICY IF EXISTS sale_discounts_insert ON sale_discounts;
            CREATE POLICY sale_discounts_insert ON sale_discounts
                FOR INSERT WITH CHECK (
                    EXISTS (
                        SELECT 1 FROM sales s
                        WHERE s.id = sale_discounts.sale_id
                          AND is_staff_of_store(s.store_id)
                    )
                );
            -- Tidak ada UPDATE/DELETE policy → diskon yang sudah dicatat tidak bisa diubah
        ");

        // ─── 2. Tabel loyalty_ledger ────────────────────────────────────────────
        DB::connection('pgsql_admin')->unprepared("
            CREATE TABLE IF NOT EXISTS loyalty_ledger (
                id              uuid         PRIMARY KEY DEFAULT gen_random_uuid(),
                customer_id     uuid         NOT NULL REFERENCES customers(id) ON DELETE CASCADE,
                sale_id         uuid         REFERENCES sales(id)              ON DELETE SET NULL,
                points_change   integer      NOT NULL, -- positif = earn, negatif = redeem/expire
                description     text,
                created_at      timestamptz  NOT NULL DEFAULT now()
            );

            CREATE INDEX IF NOT EXISTS idx_loyalty_ledger_customer_id
                ON loyalty_ledger (customer_id, created_at DESC);

            REVOKE ALL ON loyalty_ledger FROM anon, authenticated;

            ALTER TABLE loyalty_ledger ENABLE ROW LEVEL SECURITY;
            ALTER TABLE loyalty_ledger FORCE ROW LEVEL SECURITY;

            DROP POLICY IF EXISTS loyalty_ledger_select ON loyalty_ledger;
            CREATE POLICY loyalty_ledger_select ON loyalty_ledger
                FOR SELECT USING (get_current_staff_id() IS NOT NULL);
            -- Staff manapun bisa lihat ledger pelanggan (global catalog model)
        ");

        // ─── 3. Trigger: sync customers.loyalty_points dari ledger ──────────────
        DB::connection('pgsql_admin')->unprepared("
            CREATE OR REPLACE FUNCTION fn_sync_loyalty_points()
            RETURNS TRIGGER
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            BEGIN
                -- Update kolom cache di customers dengan SUM dari ledger
                UPDATE customers
                   SET loyalty_points = COALESCE((
                           SELECT SUM(points_change)
                           FROM loyalty_ledger
                           WHERE customer_id = NEW.customer_id
                       ), 0)
                 WHERE id = NEW.customer_id;

                RETURN NEW;
            END;
            $$;

            DROP TRIGGER IF EXISTS trg_sync_loyalty_points ON loyalty_ledger;
            CREATE TRIGGER trg_sync_loyalty_points
            AFTER INSERT ON loyalty_ledger
            FOR EACH ROW
            EXECUTE FUNCTION fn_sync_loyalty_points();
        ");

        // ─── 4. Sequence-based sale number per store (Priority 6) ───────────────
        // Ganti pendekatan random dengan counter per store di database.
        // Tabel ini menyimpan counter terakhir; UPDATE+RETURNING bersifat atomik.
        DB::connection('pgsql_admin')->unprepared("
            CREATE TABLE IF NOT EXISTS sale_number_sequences (
                store_id   uuid         PRIMARY KEY REFERENCES stores(id) ON DELETE CASCADE,
                date_key   char(8)      NOT NULL DEFAULT to_char(now(), 'YYYYMMDD'),
                last_seq   integer      NOT NULL DEFAULT 0
            );

            REVOKE ALL ON sale_number_sequences FROM anon, authenticated;

            -- Fungsi atomik: increment counter dan return nomor baru
            CREATE OR REPLACE FUNCTION fn_next_sale_number(p_store_id uuid)
            RETURNS varchar
            LANGUAGE plpgsql
            SECURITY DEFINER
            SET search_path = public
            AS $$
            DECLARE
                v_date_key  char(8);
                v_seq       integer;
                v_prefix    varchar(4);
            BEGIN
                v_date_key := to_char(now() AT TIME ZONE 'Asia/Jakarta', 'YYYYMMDD');

                INSERT INTO sale_number_sequences (store_id, date_key, last_seq)
                VALUES (p_store_id, v_date_key, 1)
                ON CONFLICT (store_id) DO UPDATE
                    SET last_seq   = CASE
                                        WHEN sale_number_sequences.date_key = v_date_key
                                        THEN sale_number_sequences.last_seq + 1
                                        ELSE 1  -- hari baru, reset counter
                                     END,
                        date_key   = v_date_key
                RETURNING last_seq INTO v_seq;

                -- Prefix 4 karakter dari store_id (cukup untuk identifikasi cabang)
                v_prefix := upper(left(replace(p_store_id::text, '-', ''), 4));

                -- Format: INV-20260822-ABCD-0001
                RETURN 'INV-' || v_date_key || '-' || v_prefix || '-' || lpad(v_seq::text, 4, '0');
            END;
            $$;
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::connection('pgsql_admin')->unprepared("
            DROP TRIGGER  IF EXISTS trg_sync_loyalty_points ON loyalty_ledger;
            DROP FUNCTION IF EXISTS fn_sync_loyalty_points();
            DROP FUNCTION IF EXISTS fn_next_sale_number(uuid);
            DROP TABLE    IF EXISTS sale_number_sequences;
            DROP TABLE    IF EXISTS loyalty_ledger;
            DROP TABLE    IF EXISTS sale_discounts;
        ");
    }
};
