ATURAN KETAT (wajib dipatuhi)

1. Jangan ubah logika bisnis, query, validasi, atau permission check di komponen
   Livewire — hanya sentuh presentation layer (markup Blade, class Tailwind, struktur
   layout, state kosong/loading/error).
2. Jangan hardcode warna/hex baru. Pakai token yang sudah ada di app.css. Kalau memang
   perlu token baru (mis. warna badge status pesanan), tambahkan dulu ke @theme di
   app.css dengan penamaan konsisten, jangan tempel inline.
3. Setiap halaman WAJIB dibungkus <x-layouts.app :title="..." :breadcrumbs="[...]">
   dengan title & breadcrumb yang masuk akal, dan pakai slot actions untuk tombol
   utama (Tambah, Ekspor, dll) alih-alih menaruh tombol bebas di dalam body halaman.
4. Rapikan/duplikasi elemen UI berulang (tombol, badge status, tabel data, form,
   modal, pagination, empty state) menjadi komponen Blade reusable di
   resources/views/components/ui/ — jangan biarkan setiap halaman menulis markup
   tabel/form dari nol dengan gaya berbeda-beda.
5. Desain eksplisit untuk 3 kondisi yang sering dilupakan: state kosong (belum ada
   data), state loading (wire:loading), dan state error/gagal — jangan dibiarkan
   kosong putih begitu saja.
6. Wajib accessible: kontras teks minimal AA, focus-visible di semua elemen
   interaktif, aria-label pada tombol ikon-saja, elemen form berlabel jelas.
7. Mobile-first & responsif — uji breakpoint mobile, tablet, desktop, khususnya untuk
   tabel data (pertimbangkan card-view di mobile) dan form panjang.
8. Semua teks tetap Bahasa Indonesia, konsisten dengan istilah yang sudah dipakai
   (Kasir/POS, Katalog Produk, Kategori, Inventaris, Pelanggan, Cabang, Laporan,
   Daftar Staff, dst).
