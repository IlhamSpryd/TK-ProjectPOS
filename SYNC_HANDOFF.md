# SYNC HANDOFF: TK-Project POS
*(Dokumen Sinkronisasi Antigravity - Claude - ChatGPT)*

## 1. Ringkasan Eksekutif
TK-Project POS adalah sistem kasir berbasis Laravel (Livewire) dengan database Supabase (PostgreSQL). Proyek ini saat ini baru menyelesaikan fase inisiasi backend, konektivitas database, serta sistem autentikasi kustom untuk *Staff* lengkap dengan Row Level Security (RLS). Sementara fondasi keamanannya sudah solid, seluruh lapisan antarmuka operasional bisnis (kasir, inventori, laporan) masih sama sekali belum dikerjakan.

## 2. Temuan Paling Penting
- **RLS Dinamis**: Keamanan data di-handle langsung di database PostgreSQL melalui Middleware `SetStaffContext.php` yang menjalankan perintah SQL agar sesi dibatasi per Staff/Toko.
- **Autentikasi Modifikasi**: Laravel telah dimodifikasi agar membaca tabel `staff` (via model `Staff`) dan kolom `password_hash`, serta meninggalkan tabel `users` standar.
- **Struktur Database Lengkap**: Meski UI kosong, skema database (22 tabel) sudah sangat komprehensif mendukung fitur *multi-store*, inventori, pergerakan barang, return, hingga absen (*shift*) dan mesin kasir (*register*).
- **Semua ID Berbasis UUID**: Database tidak memakai auto-increment konvensional, tapi *random UUID*.

## 3. Risiko & Area Audit (Untuk Claude)
- **Celah Session Leakage (Sangat Kritis)**: Tolong audit `SetStaffContext.php`. Jika menggunakan mekanisme *connection pooling*, penggunaan `DB::statement("SET app.staff_id...")` berpotensi bocor (leaked session) dari satu request ke request user lain. Harus dicek mitigasinya.
- **Efisiensi Indeks Transaksional**: Cek `schema_summary.json` di tabel `sale_items` dan `inventory_movements`, apakah struktur *foreign key* dan *constraint* sudah cukup efisien untuk jutaan transaksi.
- **Keamanan Hashing**: Evaluasi kesesuaian modifikasi `getAuthPassword()` pada `Staff.php` dengan fitur update password bawaan Laravel Fortify.

## 4. Ide & Brainstorming (Untuk ChatGPT)
- **Arsitektur Front-End Kasir**: Karena kita memakai Livewire yang selalu hit ke server, bagaimana strategi terbaik merancang halaman POS agar tidak *lagging* saat _scanning_ barang secara cepat? Haruskah state keranjang dibantu Alpine.js secara lokal?
- **Sistem Offline/Fallback**: POS rawan internet mati. Berhubung database kita Supabase (Cloud), adakah saran pola *Service Worker* atau integrasi PWA agar toko tetap bisa mencetak struk saat *offline*, lalu *sync* ke Supabase ketika *online*?
- **Pemanfaatan Loyalty Points**: Ada *field* `loyalty_points` di tabel `customers`. Sebaiknya skema pengurangan/penambahan poin saat checkout POS dirancang seperti apa?

## 5. Langkah Eksekusi Selanjutnya (Oleh Antigravity)
1. **Pembuatan App Shell (Layout)**: Merancang dan membuat antarmuka dashboard utama (Navigasi Kiri / Atas) menggunakan komponen Livewire/Tailwind.
2. **Implementasi CRUD Kategori & Produk**: Membuat halaman pengelola daftar barang dagangan sebagai fondasi awal data master.
3. **Mendesain UI POS (Layar Kasir)**: Membuat prototipe interaktif untuk halaman penjualan dengan fungsi _search/scan_, keranjang *cart*, dan *checkout*.
