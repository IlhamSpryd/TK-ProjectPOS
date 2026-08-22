# Dokumentasi Proyek Point of Sale (POS)

Dokumen ini berisi rangkuman komprehensif mengenai status proyek aplikasi Point of Sale (POS), fitur-fitur yang sudah berjalan, berkas-berkas penting yang telah diubah atau dibuat, serta potongan kode krusial yang menggerakkan sistem keamanan dan autentikasi.

---

## 1. Status Proyek (Apa yang Sudah dan Belum Ada)

### ✅ Yang Sudah Ada (Telah Diimplementasikan)
1. **Koneksi Database Supabase**: Aplikasi telah terhubung dengan baik ke PostgreSQL yang di-hosting di Supabase menggunakan _transaction pooler_.
2. **22 Model Eloquent**: Semua tabel dari skema database telah dipetakan menjadi model Laravel (seperti `Staff`, `Product`, `Sale`, `Customer`, dll.) lengkap dengan relasi antartabelnya.
3. **Autentikasi Kustom (Staff)**: Sistem login bawaan Laravel (yang biasanya menggunakan tabel `users`) telah dimodifikasi secara total untuk menggunakan tabel `staff`.
4. **Row Level Security (RLS) PostgreSQL**: Middleware khusus telah dibuat untuk memastikan kebijakan RLS pada database Supabase dapat membaca data pengguna yang sedang login (membuat data saling terisolasi dengan aman).
5. **Manajemen Profil & Keamanan**: Pengguna (Staff) sudah dapat memperbarui nama (`full_name`), email, serta mengubah kata sandi mereka secara aman, karena sistem *hashing* sudah disesuaikan dengan kolom `password_hash`.
6. **Penghapusan Fitur yang Tidak Kompatibel**: Fitur seperti *Passkeys*, Autentikasi 2 Langkah (2FA), dan verifikasi email bawaan telah dinonaktifkan agar tidak terjadi konflik dengan tabel `staff` yang tidak memiliki kolom-kolom tersebut.
7. **Antarmuka Utama Kasir (POS / Checkout)**: Pembuatan UI/UX modern berbasis Tailwind CSS. Telah dioptimasi dengan pencegahan klik ganda (*double-submit*), indikator *loading*, aksesibilitas *keyboard* yang baik, serta perbaikan performa *N+1 query* menggunakan *Eager Loading* dan *Pagination*.
8. **Manajemen CRUD Data Master (Katalog)**: UI/UX untuk pengelolaan Produk dan Kategori telah selesai dengan desain *enterprise*, dan dilengkapi dengan validasi pesan error secara *inline* langsung di bawah kolom isian.
9. **Laporan & Analitik (Dashboard)**: UI dasbor kasir yang terintegrasi penuh dengan komponen Livewire untuk menyajikan data metrik secara *real-time* (pendapatan, jumlah order, produk aktif).

### ❌ Yang Belum Ada (Belum Diimplementasikan)
1. **Sistem Manajemen Inventaris**: Pelacakan masuk-keluarnya stok barang (`stock_movements`) melalui antarmuka web.
2. **Role-Based Access Control (RBAC) pada UI**: Meskipun API dan Backend aman dengan RLS, belum ada pembatasan tampilan (menu yang disembunyikan) berdasarkan peran (*role*) dari masing-masing *staff*.

---

## 2. Pembaruan Terbaru UI/UX (Clean, Modern, Notion-Style)
- **Tema dan Tipografi**: Seluruh antarmuka telah direvisi dengan pendekatan desain minimalis (bersih, kontras tinggi ala Notion/GitHub).
- **Penghapusan Dependensi Flux**: Menghapus penggunaan direktif `@fluxStyles` dan komponen UI Flux (`<flux:switch>`, `<flux:icon>`) karena tidak ter-*render* dengan benar dan menyebabkan komponen patah/hilang. Digantikan dengan input form standar berpadu Tailwind CSS agar lebih ringan dan konsisten.
- **Sinkronisasi Backend & Frontend**: Telah dipastikan tidak ada miskomunikasi antara form blade (Frontend) dan Livewire Model (Backend). Seluruh input (`wire:model`) dari Form Staff, Cabang, Pelanggan, dan Kategori terhubung sempurna.
- **Optimalisasi Sidebar & Layout**: Mengurangi *padding* dan ukuran *font* pada Sidebar agar muat dalam satu layar tanpa *scroll*, serta membersihkan duplikasi SVG.
- **Pembersihan Root Direktori**: Skrip Python (`extract_schema.py`, dll.) serta file skema `*.sql`/`*.json` telah dipindahkan ke folder `scripts/` untuk menjaga kebersihan root direktori proyek. File sementara seperti `temp_pos.blade.php` telah dihapus.

---

## 3. Berkas yang Dibuat / Diubah Beserta Kegunaannya

| Nama Berkas | Kegunaan / Peran |
|-------------|------------------|
| `app/Models/Staff.php` | Model untuk tabel `staff`. Berkas ini sangat vital karena digunakan sebagai model Autentikasi. Di sini kita mendeklarasikan bahwa *password* menggunakan kolom `password_hash`. |
| `app/Models/*.php` | Kumpulan 21 model lainnya (seperti `Product.php`, `Sale.php`). Dibuat menggunakan _script_ generator Python agar otomatis menggunakan UUID dan tidak *incrementing*. |
| `app/Models/ProductVariant.php` | Diperbarui dengan menambahkan fungsi relasi `stocks()` ke entitas `InventoryStock` agar *Eager Loading* di POS berjalan tanpa *error relation not found*. |
| `app/Http/Middleware/SetStaffContext.php` | Middleware keamanan yang memasukkan ID *staff* yang sedang *login* ke variabel _session_ di PostgreSQL. Sangat penting untuk fitur RLS Supabase. |
| `config/auth.php` | Konfigurasi autentikasi telah diubah agar default _guard_ `web` menggunakan *provider* yang mengarah ke model `Staff` (bukan `User`). |
| `config/fortify.php` | Pengaturan *backend* autentikasi Laravel. Telah diedit untuk mematikan fitur 2FA, registrasi, dan Passkey. |
| `app/Livewire/Dashboard.php` | Komponen utama untuk dasbor, mengambil agregasi data penjualan dan status inventori toko secara *real-time* ke tampilan dasbor kasir. |
| `app/Livewire/PosScreen.php` | Komponen utama layar kasir. Diperbarui untuk menggunakan metode `paginate(24)` serta *eager-loading* untuk menangani isu kebocoran memori (N+1 query) pada *listing* katalog produk. |
| `app/Livewire/Settings/Profile.php` | Pengontrol antarmuka halaman Profil. Telah disesuaikan untuk menggunakan nama variabel `full_name` dan menonaktifkan kode verifikasi email. |
| `app/Livewire/Settings/Security.php` | Pengontrol antarmuka untuk mengganti kata sandi. Diperbarui untuk menggunakan kolom `password_hash`. |
| `app/Concerns/ProfileValidationRules.php` | (*Trait*) Aturan validasi profil. Diperbarui untuk menggunakan model `Staff` agar sistem tahu tabel mana yang harus dicek saat validasi email unik. |
| `routes/web.php` | Aturan rute aplikasi. Diubah agar rute `/dashboard` terikat langsung ke komponen Livewire baru. |
| `resources/views/dashboard.blade.php` | Diperbarui dengan menghapus bungkus *hardcoded layout* dan mengganti variabel statis menjadi data *real-time* dari backend Livewire. |
| `resources/views/livewire/pos-screen.blade.php` | Perombakan UI untuk memenuhi standar aksesibilitas (ubah `div` menjadi `button`), penambahan indikator pencarian (*spinner*), dan fitur proteksi pencegahan klik-ganda (*double submit*). |
| `resources/views/livewire/catalog/*-form.blade.php` | Diperbarui untuk menambahkan dukungan `<flux:error>` (pesan error sebaris) secara langsung tanpa perantara _toast modal_. Juga telah dioptimalkan agar tidak membangkitkan *component missing exception*. |
| `resources/views/livewire/auth/*` | Template antarmuka autentikasi. Diperbarui untuk menghapus/menyembunyikan render tombol Passkeys. |
| `scripts/*` | Folder baru yang berisi skrip utilitas (Python) untuk *generate* model dan *dump* skema database (`schema_dump.sql`, `schema_summary.json`). Dipindahkan dari root agar lebih rapi. |

---

## 4. Kode-Kode Penting dan Fungsinya Secara Detail

### A. Konteks Keamanan RLS (Row Level Security)
**Berkas:** `app/Http/Middleware/SetStaffContext.php`
```php
public function handle(Request $request, Closure $next): Response
{
    if (Auth::check()) {
        $staffId = Auth::user()->id;
        // Memberitahu PostgreSQL (Supabase) siapa yang sedang mengakses database
        DB::statement("SET app.staff_id = '{$staffId}'");
    }

    return $next($request);
}
```
**Fungsi:** 
Supabase memiliki aturan keamanan di level database. Agar database tahu siapa yang sedang beroperasi, kode ini dipanggil pada setiap permintaan (request) dari browser. Jika seorang kasir melakukan *query* stok, Supabase akan membaca `app.staff_id` dan hanya memberikan data yang boleh diakses kasir tersebut.

### B. Konfigurasi Autentikasi Kustom (Staff Model)
**Berkas:** `app/Models/Staff.php`
```php
protected $casts = [
    'id' => 'string',
    'active' => 'boolean',
    'password_hash' => 'hashed', // (1) Penting untuk enkripsi
];

public $incrementing = false;
protected $keyType = 'string'; // (2) Penting untuk UUID

// (3) Mengganti nama kolom sandi default Laravel
public function getAuthPassword() {
    return $this->password_hash;
}
```
**Fungsi:**
1. `'hashed'` memastikan bahwa setiap kali kita meng-assign kata sandi baru (seperti `$staff->password_hash = '123'`), Laravel akan otomatis mengenkripsinya dengan algoritma Bcrypt/Argon.
2. Memastikan Laravel tidak mencoba mencari *ID* berbentuk angka (integer/auto-increment), karena kita menggunakan standar UUID global (`string`).
3. `getAuthPassword()` memberitahu fungsi internal `Auth::attempt()` milik Laravel untuk mengecek ke kolom `password_hash`, karena secara bawaan Laravel akan ngeyel mencari kolom bernama `password`.

### C. Injeksi Konteks pada Konfigurasi Global (Kernel)
**Berkas:** `bootstrap/app.php`
```php
->withMiddleware(function (Middleware $middleware) {
    $middleware->web(append: [
        \App\Http\Middleware\SetStaffContext::class,
    ]);
})
```
**Fungsi:** 
Mendaftarkan middleware `SetStaffContext` agar dieksekusi secara otomatis setiap kali ada pengunjung yang mengakses rute-rute _web_ (browser). Tanpa ini, RLS akan memblokir sebagian besar _query_ Eloquent yang meminta data transaksi.

### D. Penonaktifan Komponen yang Tidak Digunakan
**Berkas:** `resources/views/livewire/auth/login.blade.php` dan `confirm-password.blade.php`
```html
{{-- @chisel-passkeys --}}
{{-- <x-passkey-verify /> --}}
{{-- @end-chisel-passkeys --}}
```
**Fungsi:**
Menghentikan render komponen *login* biometrik/Passkey. Jika baris ini tidak di-*comment*, aplikasi akan *crash* (Internal Server Error) karena sistem _routing_ `passkey.login-options` sudah dimatikan di backend.
