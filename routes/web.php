<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/pos', \App\Livewire\PosScreen::class)->name('pos');
    
    // Katalog
    Route::get('/katalog/kategori', \App\Livewire\Catalog\CategoryIndex::class)->name('catalog.categories');
    Route::get('/katalog/produk', \App\Livewire\Catalog\ProductIndex::class)->name('catalog.products');
    Route::get('/katalog/produk/tambah', \App\Livewire\Catalog\ProductForm::class)->name('catalog.products.create');
    Route::get('/katalog/produk/{id}/edit', \App\Livewire\Catalog\ProductForm::class)->name('catalog.products.edit');

    // Manajemen
    Route::get('/pelanggan', \App\Livewire\Customers\CustomerIndex::class)->name('customers');
    Route::get('/cabang', \App\Livewire\Stores\StoreIndex::class)->name('stores');
    Route::get('/staff', \App\Livewire\Staff\StaffIndex::class)->name('staff.index');
    Route::get('/staff/tambah', \App\Livewire\Staff\StaffForm::class)->name('staff.create');
    Route::get('/laporan', \App\Livewire\Reports\ReportDashboard::class)->name('reports');
});

require __DIR__.'/settings.php';
