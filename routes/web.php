<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', \App\Livewire\Dashboard::class)->name('dashboard');
    Route::get('/pos', \App\Livewire\PosScreen::class)->middleware('permission:pos_access')->name('pos');
    
    // Katalog
    Route::get('/katalog/kategori', \App\Livewire\Catalog\CategoryIndex::class)->middleware('permission:manage_catalog')->name('catalog.categories');
    Route::get('/katalog/produk', \App\Livewire\Catalog\ProductIndex::class)->middleware('permission:manage_catalog')->name('catalog.products');
    Route::get('/katalog/produk/tambah', \App\Livewire\Catalog\ProductForm::class)->middleware('permission:manage_catalog')->name('catalog.products.create');
    Route::get('/katalog/produk/{id}/edit', \App\Livewire\Catalog\ProductForm::class)->middleware('permission:manage_catalog')->name('catalog.products.edit');

    // Inventaris
    Route::get('/inventori/pergerakan', \App\Livewire\Inventory\MovementIndex::class)->middleware('permission:manage_inventory')->name('inventory.movements');
    Route::get('/inventori/pergerakan/tambah', \App\Livewire\Inventory\MovementForm::class)->middleware('permission:manage_inventory')->name('inventory.movements.create');

    // Manajemen
    Route::get('/pelanggan', \App\Livewire\Customers\CustomerIndex::class)->middleware('permission:manage_customers')->name('customers');
    Route::get('/cabang', \App\Livewire\Stores\StoreIndex::class)->middleware('permission:manage_stores')->name('stores');
    Route::get('/staff', \App\Livewire\Staff\StaffIndex::class)->middleware('permission:manage_staff')->name('staff.index');
    Route::get('/staff/tambah', \App\Livewire\Staff\StaffForm::class)->middleware('permission:manage_staff')->name('staff.create');
    Route::get('/laporan', \App\Livewire\Reports\ReportDashboard::class)->middleware('permission:view_reports')->name('reports');
});

require __DIR__.'/settings.php';
