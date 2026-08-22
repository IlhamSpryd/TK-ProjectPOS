# Audit Arsitektur Database & Keamanan — Sistem POS

**Reviewer:** Principal Database Architect & Cyber Security Perspective
**Objek Audit:** 34 tabel `public.*` (Supabase/PostgreSQL) + 45 RLS policies
**Tanggal:** 22 Agustus 2026

---

## 0. Catatan Metodologi & Gap Data (WAJIB dibaca dulu)

Sebelum masuk temuan, ada beberapa data yang **tidak tersedia** di dua file yang Anda kirim, dan ini mengubah tingkat keparahan beberapa temuan secara signifikan. Saya beri asumsi eksplisit supaya laporan tetap actionable, tapi Anda **wajib** verifikasi poin-poin ini:

| Data yang hilang | Kenapa penting |
| --- | --- |
| Kolom `roles` pada `pg_policies` (siapa target policy: `anon`, `authenticated`, atau role custom) | Ini **satu-satunya** cara memastikan apakah policy `ALL / true` di tabel `users`, `sessions`, dll bisa diakses publik lewat API Supabase atau tidak. |
| Struktur FK (`ON DELETE CASCADE/RESTRICT`), UNIQUE constraint, CHECK constraint | Tidak ada di `information_schema.columns`, perlu query terpisah ke `information_schema.table_constraints` / `key_column_usage`. |
| Definisi fungsi `get_current_staff_id()`, `is_admin()`, `is_staff_of_store()` | Menentukan apakah RLS Anda benar-benar aman dari spoofing (lihat B.2). |
| Apakah aplikasi (Laravel — lihat di bawah) connect ke Postgres pakai `service_role`/superuser atau role terbatas | Ini **menentukan apakah seluruh RLS Anda efektif atau sekadar dekorasi**. |

**Temuan struktural pertama:** skema Anda mengandung tabel `users`, `sessions`, `password_reset_tokens`, `passkeys`, `jobs`, `failed_jobs`, `cache`, `cache_locks`, `migrations` — ini adalah tabel bawaan **Laravel** (kemungkinan Laravel Fortify/Jetstream + queue system), sementara `staff` adalah tabel auth terpisah dengan `password_hash` dan `pin_hash` sendiri. Ini artinya sistem Anda **kemungkinan besar TIDAK memakai Supabase Auth (GoTrue)** — Anda memakai Laravel sebagai backend yang connect langsung ke Postgres-nya Supabase, dan RLS di-drive oleh session variable custom (bukan `auth.uid()` bawaan Supabase).

Ini penting karena **RLS di PostgreSQL hanya efektif untuk role yang bukan superuser/table owner**, dan hanya benar-benar "melihat" identitas user jika ada mekanisme yang mengikat setiap koneksi/transaksi ke satu identitas (biasanya via `SET LOCAL app.current_staff_id = '...'` per request). Kalau Laravel Anda connect pakai satu koneksi pool dengan role yang sama untuk semua user (pola umum Laravel), maka:

- Jika role itu adalah table owner atau punya `BYPASSRLS`, **semua 45 policy yang Anda buat itu tidak berlaku sama sekali** — proteksi 100% bergantung pada logika PHP di aplikasi.
- Kalau begitu, database Anda TIDAK punya defense-in-depth. Satu bug SQL injection atau logic error di controller Laravel = akses penuh ke semua data semua toko.

**Rekomendasi langsung:** jalankan ini di SQL Editor Supabase untuk memastikan:

```sql
SELECT rolname, rolsuper, rolbypassrls
FROM pg_roles
WHERE rolname = current_user; -- jalankan dari koneksi yang dipakai Laravel

SELECT relname, relrowsecurity, relforcerowsecurity
FROM pg_class
WHERE relnamespace = 'public'::regnamespace AND relkind = 'r';
```

Kalau `rolbypassrls = true` atau `relforcerowsecurity = false` sementara koneksi pakai role yang sama dengan owner tabel → **RLS Anda saat ini bocor total untuk trafik dari Laravel sendiri**, dan hanya efektif untuk request yang lewat PostgREST/API langsung. Jika Laravel-lah satu-satunya klien database, maka RLS pada dasarnya hanya berguna sebagai *safety net* kalau suatu saat Anda expose API Supabase langsung ke frontend.

**Asumsi model tenant:** Berdasarkan skema (`stores`, `staff_stores` many-to-many, fungsi `is_staff_of_store()`, tapi `products`, `customers`, `categories`, `suppliers`, `roles` bersifat **global tanpa `store_id`**), pola ini terlihat seperti **satu bisnis dengan banyak cabang** (Single-Business Multi-Branch), bukan Multi-Tenant SaaS untuk merchant yang independen. Saya audit dengan asumsi ini, tapi saya tandai risikonya kalau ternyata asumsi Anda adalah "banyak merchant independen berbeda" — karena kalau itu benar, ini adalah **cacat arsitektur besar**, bukan sekadar celah RLS (lihat B.1).

---

## RINGKASAN EKSEKUTIF — Temuan Kritis (urut prioritas)

1. 🔴 **`sales`, `payments`, `shifts`, `expenses`, `sale_returns` punya policy `ALL` untuk staff toko sendiri** — artinya kasir bisa **UPDATE/DELETE** transaksi yang sudah selesai, mengubah nominal pembayaran, dan mengedit hasil rekonsiliasi kas. Ini **melanggar langsung** requirement fraud-prevention Anda.
2. 🔴 **`users` (tabel Laravel) diberi policy `ALL / true`** — jika tabel ini bisa diakses lewat API publik Supabase, kolom `two_factor_secret`, `two_factor_recovery_codes`, `password` (hash), dan `remember_token` **bocor total** ke siapa pun yang punya API key.
3. 🔴 **`inventory_movements_insert` dan `customers_insert` tidak punya `WITH CHECK`** — siapa pun yang lolos syarat awal bisa INSERT baris dengan `store_id` toko lain, memalsukan mutasi stok atau data pelanggan.
4. 🟠 **`customers`, `products`, `product_variants` (termasuk `cost_price`), `categories`, `suppliers` bisa dibaca oleh staff toko manapun** — kalau ini benar Multi-Tenant merchant independen, ini kebocoran data bisnis lintas kompetitor.
5. 🟠 Hampir semua **foreign key tidak punya index eksplisit** (PostgreSQL tidak auto-index FK) — akan jadi bottleneck serius begitu data jutaan baris, apalagi RLS Anda banyak pakai `EXISTS` subquery ke tabel induk.
6. 🟡 Tidak ada tabel `discounts_applied`/junction — diskon yang dipakai di sebuah transaksi tidak bisa ditelusuri diskon spesifik mana yang dipakai.

Detail lengkap di bawah.

---

## A. AUDIT STRUKTUR & PERFORMA

### A.1 Normalisasi

**Yang sudah benar:**

- Pemisahan `products` ↔ `product_variants` ↔ `inventory_stock` ↔ `inventory_movements` sudah tepat: `inventory_stock` sebagai cache saldo real-time, `inventory_movements` sebagai ledger append-only. Ini pola yang benar untuk OLTP tinggi (hindari `SUM()` dari ledger tiap kali cek stok).
- `sale_items.unit_price` dan `sale_items.cost_price` **sengaja di-snapshot** dari harga saat transaksi, bukan join real-time ke `product_variants`. Ini **bukan redundansi buruk** — ini kewajiban untuk sistem transaksi finansial (harga & histori tidak boleh berubah kalau harga produk berubah di kemudian hari). Pertahankan pola ini.
- Pemisahan `purchase_orders`/`purchase_order_items` dan `sale_returns`/`sale_return_items` sudah normal (1 header : banyak baris), sesuai standar akuntansi.

**Yang perlu diperbaiki:**

1. **Tidak ada tabel penghubung diskon ↔ transaksi.** `discounts` berdiri sendiri, `sales.discount_total` cuma angka. Anda tidak bisa menjawab "diskon apa yang dipakai di transaksi #1234?" untuk keperluan audit atau laporan efektivitas promo. Rekomendasi: tambah `sale_discounts (sale_id, discount_id, amount_applied)`.

2. **`role_id` melekat di tabel `staff` (global), bukan di `staff_stores`.** Kalau bisnis Anda punya kasir yang levelnya beda di tiap cabang (mis. supervisor di Toko A, kasir biasa di Toko B), skema sekarang tidak bisa mengakomodasi — role selalu sama di semua toko. Kalau kebutuhan bisnis Anda memang "1 orang = 1 role di semua cabang", ini tidak masalah; kalau tidak, pindahkan `role_id` ke `staff_stores`.

3. **`customers.loyalty_points` sebagai kolom mutable tanpa ledger.** Sama seperti stok, poin loyalty yang langsung di-`UPDATE` rawan drift dan sulit diaudit ("kenapa poin customer ini tiba-tiba 5000?"). Rekomendasi minor: tambah `loyalty_ledger (customer_id, sale_id, points_change, created_at)`, dengan `customers.loyalty_points` jadi kolom cache yang hanya diubah lewat trigger dari ledger — pola yang sama persis dengan `inventory_stock`/`inventory_movements` yang sudah Anda terapkan dengan baik untuk stok.

4. **`stores.currency` mengindikasikan multi-currency**, tapi tidak ada normalisasi nilai ke base currency di `sales`/`expenses`. Jika laporan keuangan Anda nanti menjumlahkan `grand_total` lintas toko dengan currency berbeda, hasilnya akan salah tanpa disadari. Kalau semua toko sebenarnya pakai IDR, pertimbangkan hapus fleksibilitas ini untuk menyederhanakan; kalau memang multi-currency riil, laporan agregat wajib group by currency.

### A.2 Optimalisasi Indeks

PostgreSQL **tidak otomatis membuat index untuk kolom foreign key** (hanya primary key & unique constraint yang otomatis ter-index). Karena RLS Anda sangat bergantung pada `EXISTS (...)` subquery ke tabel induk (`sale_items` → `sales`, `payments` → `sales`, `shifts` → `registers`, dst), **tanpa index yang tepat setiap SELECT akan full-scan tabel induk** begitu data membesar — ini bukan cuma soal performa laporan, tapi RLS Anda sendiri akan jadi lambat di setiap query.

```sql
-- === PENCARIAN PRODUK (kebutuhan #1: barcode & nama harus instan) ===
CREATE UNIQUE INDEX CONCURRENTLY idx_variants_barcode
  ON product_variants (barcode) WHERE deleted_at IS NULL AND barcode IS NOT NULL;

CREATE UNIQUE INDEX CONCURRENTLY idx_variants_sku
  ON product_variants (sku) WHERE deleted_at IS NULL;

-- Full text / partial search nama produk (butuh extension pg_trgm)
CREATE EXTENSION IF NOT EXISTS pg_trgm;
CREATE INDEX CONCURRENTLY idx_products_name_trgm
  ON products USING gin (name gin_trgm_ops);

-- === FK YANG WAJIB DI-INDEX (dipakai RLS + join laporan) ===
CREATE INDEX CONCURRENTLY idx_sale_items_sale_id ON sale_items (sale_id);
CREATE INDEX CONCURRENTLY idx_sale_items_variant_id ON sale_items (variant_id);
CREATE INDEX CONCURRENTLY idx_payments_sale_id ON payments (sale_id);
CREATE INDEX CONCURRENTLY idx_shifts_register_id ON shifts (register_id);
CREATE INDEX CONCURRENTLY idx_purchase_order_items_po_id ON purchase_order_items (purchase_order_id);
CREATE INDEX CONCURRENTLY idx_sale_returns_sale_id ON sale_returns (sale_id);
CREATE INDEX CONCURRENTLY idx_sale_return_items_return_id ON sale_return_items (sale_return_id);
CREATE INDEX CONCURRENTLY idx_staff_stores_store_id ON staff_stores (store_id);

-- === LAPORAN KEUANGAN (filter tanggal per toko — pola query paling sering) ===
CREATE INDEX CONCURRENTLY idx_sales_store_date ON sales (store_id, sale_date DESC);
CREATE INDEX CONCURRENTLY idx_sales_store_status ON sales (store_id, status);
CREATE UNIQUE INDEX CONCURRENTLY idx_sales_store_number ON sales (store_id, sale_number);
CREATE INDEX CONCURRENTLY idx_expenses_store_date ON expenses (store_id, expense_date DESC);
CREATE INDEX CONCURRENTLY idx_purchase_orders_store_status ON purchase_orders (store_id, status);

-- === STOK ===
CREATE UNIQUE INDEX CONCURRENTLY idx_inventory_stock_variant_store
  ON inventory_stock (variant_id, store_id); -- juga jadi constraint anti-duplikat
CREATE INDEX CONCURRENTLY idx_inventory_movements_variant_store_date
  ON inventory_movements (variant_id, store_id, created_at DESC);

-- === AUDIT LOG ===
CREATE INDEX CONCURRENTLY idx_audit_logs_table_record ON audit_logs (table_name, record_id);
CREATE INDEX CONCURRENTLY idx_audit_logs_changed_by ON audit_logs (changed_by);

-- === PELANGGAN ===
CREATE INDEX CONCURRENTLY idx_customers_phone ON customers (phone) WHERE deleted_at IS NULL;
CREATE INDEX CONCURRENTLY idx_customers_active ON customers (id) WHERE deleted_at IS NULL;

-- === JSONB (kalau permissions/attributes di-query berdasarkan isi) ===
CREATE INDEX CONCURRENTLY idx_roles_permissions_gin ON roles USING gin (permissions);
```

Catatan: pakai `CREATE INDEX CONCURRENTLY` di production supaya tidak lock table saat data sudah besar.

### A.3 Tipe Data

- ✅ Pemakaian `uuid` untuk hampir semua PK sudah tepat untuk sistem multi-cabang (tidak collision antar toko, aman untuk generate offline). Pemakaian `numeric` (bukan `float`/`double`) untuk semua nilai uang juga sudah **benar** — ini poin bagus, banyak tim justru salah pakai `float` yang punya rounding error.
- ⚠️ **Inkonsistensi ID:** `users.id` dan `passkeys.id`/`user_id` pakai `bigint`, sedangkan `staff.id` pakai `uuid`. Ini mengonfirmasi ada **dua sistem identitas paralel** (`users` ala Laravel Fortify vs `staff` custom Anda) — dan **tidak ada kolom penghubung** antara keduanya (staff tidak punya `user_id`). Kalau `users` memang tidak dipakai sama sekali untuk staff POS (mungkin cuma untuk super-admin panel Laravel), sebaiknya didokumentasikan jelas biar tidak ambigu siapa "identitas sebenarnya" saat audit RLS.
- ⚠️ `sales.status` dan `sales.payment_status` bertipe `character varying` bebas (bukan enum/CHECK). Tanpa constraint, aplikasi bisa menyimpan nilai typo (`'compelted'`) yang lolos begitu saja dan bikin laporan salah hitung. Rekomendasi:

```sql
ALTER TABLE sales ADD CONSTRAINT chk_sales_status
  CHECK (status IN ('draft','open','completed','void','refunded'));
ALTER TABLE sales ADD CONSTRAINT chk_sales_payment_status
  CHECK (payment_status IN ('unpaid','partial','paid','refunded'));
```

- ⚠️ Tidak ada `CHECK` yang terlihat untuk mencegah nilai negatif pada `quantity`, `unit_price`, `amount`, dsb (constraint tidak muncul di `information_schema.columns`, perlu dicek terpisah — lihat gap data di atas). Minimal tambahkan:

```sql
ALTER TABLE sale_items ADD CONSTRAINT chk_sale_items_qty CHECK (quantity > 0);
ALTER TABLE payments ADD CONSTRAINT chk_payments_amount CHECK (amount >= 0);
```

---

## B. AUDIT KEAMANAN

### B.1 Celah Kebocoran Data Antar Toko/Tenant

**Sudah aman:** Isolasi cukup baik untuk data transaksional inti — `sales`, `sale_items` (via `EXISTS` ke `sales`), `payments`, `purchase_orders`, `shifts`, `expenses`, `sale_returns` semuanya di-scope lewat `is_staff_of_store(store_id)`. Selama fungsi ini diimplementasi benar (lihat B.2), staff Toko A tidak akan melihat penjualan Toko B.

**Bocor / perlu dikonfirmasi kesengajaannya:**

1. `customers` **tidak punya `store_id` sama sekali**, dan policy `customers_select` mengizinkan `get_current_staff_id() IS NOT NULL` — artinya **staff dari toko manapun bisa melihat dan mengubah SEMUA data pelanggan di semua cabang**, termasuk `npwp` (NPWP), `phone`, `address`, `email`. Kalau ini "1 bisnis banyak cabang" dengan CRM/loyalty terpadu, ini **disengaja dan wajar**. Kalau ini platform yang menaungi merchant-merchant independen, ini **kebocoran PII lintas tenant** yang serius.

2. `products`, `product_variants`, `categories`, `suppliers`, `tax_categories`, `roles` semuanya **global**, bisa dibaca staff toko manapun (`catalog_read`, `variants_read`: syaratnya cuma "staff yang login", bukan staff toko tertentu). Ini termasuk **`cost_price`** di `product_variants` — HPP produk Anda terlihat oleh semua staff semua cabang, termasuk kasir level bawah. Kalau HPP adalah informasi sensitif (biasanya iya, untuk mencegah kasir tahu margin dan bernegosiasi harga di luar sistem), pertimbangkan pisahkan kolom sensitif:

```sql
-- Kasir hanya boleh lihat kolom yang perlu untuk transaksi, bukan cost_price
CREATE VIEW pos_variant_view AS
SELECT id, product_id, sku, barcode, attributes, selling_price, active
FROM product_variants;
-- lalu buat RLS/grant SELECT hanya ke view ini untuk role kasir, bukan ke tabel aslinya
```

1. **Konfirmasi wajib dari Anda:** apakah `staff` dari Cabang A memang seharusnya bisa lihat semua produk dan semua pelanggan Cabang B? Kalau tidak, `catalog_read` dan `customers_select` harus diubah jadi store-scoped juga.

### B.2 Akses Publik yang Berbahaya

1. 🔴 **Policy `tkpos_app_full_access` (`ALL`, kondisi `true`) menempel di `users`, `sessions`, `password_reset_tokens`, `cache`, `cache_locks`, `jobs`, `job_batches`, `failed_jobs`, `passkeys`, `migrations`.** Ini semua tabel internal Laravel yang **seharusnya tidak pernah bisa diakses lewat auto-API Supabase (PostgREST)**. Karena data policy yang Anda kirim tidak menyertakan kolom "roles" (target `TO` di `CREATE POLICY`), saya tidak bisa memastikan role mana yang kena ini — tapi risikonya sangat tinggi:
   - `users` menyimpan `two_factor_secret`, `two_factor_recovery_codes`, `password` (hash) → kalau tabel ini ter-expose ke role `anon`/`authenticated` Supabase, ini setara **kebocoran kredensial total + bypass 2FA**.
   - `sessions` menyimpan `payload` mentah sesi + `ip_address` → kalau bisa dibaca, bisa dipakai untuk **session hijacking**.
   - `password_reset_tokens` → kalau bisa dibaca, siapa pun bisa **reset password akun manapun**.

   **Tindakan wajib, tidak bisa ditunda:**

   ```sql
   -- 1. Cek role apa saja yang punya akses ke tabel-tabel ini
   SELECT grantee, table_name, privilege_type
   FROM information_schema.role_table_grants
   WHERE table_schema = 'public'
     AND table_name IN ('users','sessions','password_reset_tokens','cache','jobs','failed_jobs','passkeys')
     AND grantee IN ('anon','authenticated');

   -- 2. Kalau ada baris hasil query di atas, cabut SEMUA privilege dari anon & authenticated
   REVOKE ALL ON users, sessions, password_reset_tokens, cache, cache_locks,
                 jobs, job_batches, failed_jobs, passkeys, migrations
   FROM anon, authenticated;
   ```

   Tabel-tabel Laravel ini idealnya **hanya boleh diakses oleh role koneksi Laravel itu sendiri** (atau lebih baik lagi, taruh di schema terpisah, mis. `app_internal`, yang secara default tidak pernah ter-expose lewat PostgREST — PostgREST hanya mengekspos schema yang didaftarkan, biasanya `public`).

2. 🟠 **`inventory_movements_insert` (INSERT) dan `customers_insert` (INSERT) memiliki `kondisi_filter = null`.** Untuk policy INSERT, kondisi ini seharusnya diisi via `WITH CHECK`, bukan dibiarkan kosong — kalau kosong, PostgreSQL memperlakukannya sebagai **mengizinkan semua baris**. Efeknya: staff toko A bisa INSERT `inventory_movements` dengan `store_id` milik toko B (memalsukan mutasi stok toko lain), atau meng-input `staff_id` orang lain di kolom movement (memalsukan jejak audit "siapa yang melakukan"). Perbaikan:

```sql
DROP POLICY IF EXISTS inventory_movements_insert ON inventory_movements;
CREATE POLICY inventory_movements_insert ON inventory_movements
  FOR INSERT WITH CHECK (
    is_staff_of_store(store_id)
    AND staff_id = get_current_staff_id()
  );

DROP POLICY IF EXISTS customers_insert ON customers;
CREATE POLICY customers_insert ON customers
  FOR INSERT WITH CHECK (get_current_staff_id() IS NOT NULL);
  -- (kalau customers akhirnya di-scope per store, tambahkan store_id di sini juga)
```

   Idealnya, sebenarnya **staff tidak perlu bisa INSERT `inventory_movements` secara langsung sama sekali** — mutasi stok harus selalu lewat trigger (lihat bagian C.1), bukan tulisan manual dari aplikasi. Kalau begitu, hapus saja policy INSERT untuk role staff dan biarkan hanya `SECURITY DEFINER` trigger function yang bisa menulis ke situ.

1. Pastikan `FORCE ROW LEVEL SECURITY` diaktifkan di semua tabel sensitif — ini poin yang sering terlewat: RLS **tidak berlaku untuk owner tabel** kecuali `FORCE ROW LEVEL SECURITY` di-set:

```sql
ALTER TABLE sales FORCE ROW LEVEL SECURITY;
ALTER TABLE payments FORCE ROW LEVEL SECURITY;
ALTER TABLE product_variants FORCE ROW LEVEL SECURITY;
-- ulangi untuk semua tabel sensitif lainnya
```

### B.3 Pencegahan Fraud

Ini bagian paling kritis relatif terhadap requirement bisnis Anda ("kasir tidak boleh mengubah harga dasar atau memanipulasi transaksi selesai").

**Sudah benar:** `catalog_write` dan `variants_write` (mengubah `products`/`product_variants`, termasuk `selling_price`/`cost_price`) dibatasi `is_admin()` saja. Kasir betul-betul tidak bisa UPDATE harga dasar lewat jalur ini. Bagus.

**Bermasalah — policy `ALL` terlalu longgar:**

| Tabel | Policy saat ini | Skenario fraud yang masih mungkin |
| --- | --- | --- |
| `sales` | `sales_scoped ALL is_staff_of_store(store_id)` | Kasir bisa **UPDATE** `grand_total`, `payment_status` jadi `'paid'` tanpa bayar, atau **DELETE** transaksi selesai untuk hilangkan jejak. |
| `payments` | `payments_scoped ALL` (lewat EXISTS ke sales) | Kasir bisa **edit `amount`** setelah dicatat (skimming kas), atau **DELETE** baris pembayaran. |
| `shifts` | `shifts_scoped ALL` | Kasir yang selisih kasnya minus bisa **edit `actual_cash`/`difference`** setelah shift ditutup supaya terlihat pas. |
| `expenses` | `expenses_scoped ALL` | Kasir catat pengeluaran fiktif lalu **DELETE** setelah "berhasil", atau ubah nominal. |
| `sale_returns` | `sale_returns_scoped ALL` | Kasir bikin retur fiktif untuk kolega/diri sendiri, ambil uang refund, lalu **DELETE** buktinya. |

**Perbaikan yang direkomendasikan** — pecah `ALL` jadi per-operasi, dan pakai **RESTRICTIVE policy** untuk mengunci status final (RESTRICTIVE di-AND-kan dengan policy permissive lain, jadi walau ada policy lain yang mengizinkan, restrictive ini tetap memblokir):

```sql
-- ================= SALES =================
DROP POLICY IF EXISTS sales_scoped ON sales;

CREATE POLICY sales_select ON sales
  FOR SELECT USING (is_staff_of_store(store_id));

CREATE POLICY sales_insert ON sales
  FOR INSERT WITH CHECK (
    is_staff_of_store(store_id) AND staff_id = get_current_staff_id()
  );

-- Kasir hanya boleh UPDATE transaksi yang statusnya masih draft/open
CREATE POLICY sales_update_open ON sales
  FOR UPDATE
  USING (is_staff_of_store(store_id) AND status IN ('draft','open'))
  WITH CHECK (is_staff_of_store(store_id));

-- Admin boleh update kapan pun (mis. untuk proses void resmi)
CREATE POLICY sales_update_admin ON sales
  FOR UPDATE USING (is_admin()) WITH CHECK (is_admin());

-- RESTRICTIVE: begitu status = 'completed', TIDAK ADA yang boleh ubah kecuali admin,
-- apapun policy permissive di atas
CREATE POLICY sales_lock_completed AS RESTRICTIVE ON sales
  FOR UPDATE USING (status <> 'completed' OR is_admin());

-- Tidak ada policy DELETE sama sekali → default DENY untuk semua role non-owner.
-- Pembatalan transaksi HARUS lewat UPDATE status='void' + voided_by, bukan DELETE.

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

-- Tidak ada UPDATE/DELETE untuk staff sama sekali → payment bersifat append-only.
-- Kalau perlu koreksi, buat baris payment baru bertanda "adjustment", jangan edit yang lama.
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
-- Kasir boleh update HANYA selama shift belum ditutup
CREATE POLICY shifts_update_open ON shifts FOR UPDATE
  USING (closed_at IS NULL AND EXISTS (
    SELECT 1 FROM registers r WHERE r.id = shifts.register_id AND is_staff_of_store(r.store_id)
  ));
-- Kunci permanen begitu closed_at terisi
CREATE POLICY shifts_lock_closed AS RESTRICTIVE ON shifts
  FOR UPDATE USING (closed_at IS NULL OR is_admin());
```

Terapkan pola yang sama (pisah SELECT/INSERT, batasi UPDATE ke status "belum final", RESTRICTIVE untuk kunci status final, DELETE dihapus total) untuk `expenses` dan `sale_returns`.

---

## C. REKOMENDASI OTOMATISASI & ARSITEKTUR

### C.1 Trigger Pengurangan Stok Otomatis (Aman dari Race Condition)

Prinsip kunci: **jangan pernah pakai pola "SELECT stok dulu, cek di aplikasi, baru UPDATE"** — itu yang menyebabkan race condition (dua kasir checkout barang terakhir bersamaan, keduanya lolos). Gunakan **satu statement `UPDATE ... SET qty = qty - x`**, karena UPDATE di PostgreSQL otomatis mengunci baris yang tersentuh (row-level lock via MVCC), sehingga transaksi kedua yang bersamaan akan menunggu transaksi pertama commit dulu sebelum baca nilai terbaru — otomatis serial, tanpa perlu `SELECT ... FOR UPDATE` manual.

```sql
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
  -- Ambil store_id & staff_id dari HEADER sales, bukan dari input sale_items,
  -- supaya tidak bisa dipalsukan lewat payload klien
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

  -- Atomic decrement. Baris ini terkunci sampai transaksi ini commit/rollback,
  -- sehingga dua sale_items bersamaan untuk variant yang sama akan diproses berurutan.
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
    -- RAISE EXCEPTION akan rollback UPDATE stok DAN INSERT sale_items sekaligus
    -- (satu transaksi), jadi stok tidak akan pernah tersimpan negatif.
    RAISE EXCEPTION 'Stok tidak cukup untuk variant % (sisa setelah transaksi: %)',
      NEW.variant_id, v_new_qty;
  END IF;

  INSERT INTO inventory_movements (
    id, variant_id, store_id, movement_type, quantity_change,
    reference_table, reference_id, staff_id, created_at
  ) VALUES (
    gen_random_uuid(), NEW.variant_id, v_store_id, 'sale', -NEW.quantity,
    'sale_items', NEW.id, v_staff_id, now()
  );

  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_decrement_stock_on_sale_item
AFTER INSERT ON sale_items
FOR EACH ROW
EXECUTE FUNCTION fn_decrement_stock_on_sale_item();
```

**Catatan penting:**

- Fungsi ini `SECURITY DEFINER` supaya bisa menulis ke `inventory_stock`/`inventory_movements` meski role staff biasa tidak punya izin tulis langsung ke tabel itu (sesuai temuan B.2 poin 2 — staff sebaiknya *tidak* punya policy INSERT/UPDATE langsung ke `inventory_stock`, hanya lewat trigger ini).
- Kalau bisnis Anda mengizinkan stok minus (pre-order/backorder), hapus blok `IF v_new_qty < 0` — tapi berdasarkan requirement Anda ("stok harus berkurang otomatis"), asumsi saya adalah stok tidak boleh negatif.
- Buat fungsi simetris untuk **penambahan stok** saat `purchase_order_items.received = true` (barang masuk dari supplier), dengan pola `UPDATE ... SET quantity = quantity + x` yang sama.

### C.2 Audit Log untuk Perubahan Harga & Void Transaksi

Tabel `audit_logs` Anda sudah didesain dengan baik (`old_data`/`new_data` jsonb). Yang belum ada adalah trigger otomatis yang mengisinya — jangan andalkan aplikasi Laravel untuk INSERT ke `audit_logs` secara manual, karena itu bisa dilewati/dilupakan. Buat di level database:

```sql
-- Trigger #1: catat setiap perubahan harga di product_variants
CREATE OR REPLACE FUNCTION fn_audit_price_change()
RETURNS TRIGGER
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
BEGIN
  IF NEW.selling_price IS DISTINCT FROM OLD.selling_price
     OR NEW.cost_price IS DISTINCT FROM OLD.cost_price THEN
    INSERT INTO audit_logs (id, table_name, record_id, action, old_data, new_data, changed_by, created_at)
    VALUES (
      gen_random_uuid(), 'product_variants', NEW.id, 'PRICE_CHANGE',
      jsonb_build_object('cost_price', OLD.cost_price, 'selling_price', OLD.selling_price),
      jsonb_build_object('cost_price', NEW.cost_price, 'selling_price', NEW.selling_price),
      get_current_staff_id(), now()
    );
  END IF;
  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_audit_price_change
AFTER UPDATE ON product_variants
FOR EACH ROW
EXECUTE FUNCTION fn_audit_price_change();

-- Trigger #2: catat setiap void transaksi
CREATE OR REPLACE FUNCTION fn_audit_sale_void()
RETURNS TRIGGER
LANGUAGE plpgsql
SECURITY DEFINER
SET search_path = public
AS $$
BEGIN
  IF NEW.voided_at IS NOT NULL AND OLD.voided_at IS NULL THEN
    INSERT INTO audit_logs (id, table_name, record_id, action, old_data, new_data, changed_by, created_at)
    VALUES (
      gen_random_uuid(), 'sales', NEW.id, 'VOID',
      to_jsonb(OLD), to_jsonb(NEW), NEW.voided_by, now()
    );
  END IF;
  RETURN NEW;
END;
$$;

CREATE TRIGGER trg_audit_sale_void
AFTER UPDATE ON sales
FOR EACH ROW
EXECUTE FUNCTION fn_audit_sale_void();
```

Terakhir, kunci `audit_logs` itu sendiri supaya tidak bisa dimanipulasi:

```sql
-- Pastikan TIDAK ADA policy INSERT/UPDATE/DELETE untuk role staff biasa.
-- Hanya boleh SELECT (yang sudah Anda punya: audit_logs_read is_admin()) dan
-- INSERT lewat trigger SECURITY DEFINER di atas (yang bypass RLS by design).
REVOKE INSERT, UPDATE, DELETE ON audit_logs FROM authenticated;
```

Log yang bisa diedit/dihapus oleh pihak yang sedang diaudit bukan log audit — pastikan satu-satunya jalur tulis adalah trigger.

---

## Ringkasan Tindakan Prioritas

1. **Segera:** verifikasi role/grant di tabel Laravel (`users`, `sessions`, `password_reset_tokens`, dll) — cabut akses `anon`/`authenticated` kalau ada.
2. **Segera:** perbaiki `WITH CHECK` yang `null` di `inventory_movements_insert` dan `customers_insert`.
3. **Minggu ini:** pecah policy `ALL` di `sales`, `payments`, `shifts`, `expenses`, `sale_returns` jadi per-operasi + RESTRICTIVE lock untuk status final.
4. **Minggu ini:** tambahkan seluruh index di A.2, terutama index FK dan `(store_id, sale_date)`.
5. **Sebelum go-live:** pasang trigger stok (C.1) dan audit log (C.2), lalu cabut izin tulis langsung staff ke `inventory_stock` dan `audit_logs`.
6. **Konfirmasi ke tim:** apakah `customers`/`products` memang seharusnya global lintas cabang, dan apakah koneksi Laravel Anda memakai role dengan `BYPASSRLS`.
