<?php

use App\Livewire\Catalog\CategoryIndex;
use App\Livewire\Catalog\ProductForm;
use App\Livewire\Catalog\ProductIndex;
use App\Livewire\Customers\Index;
use App\Livewire\Dashboard;
use App\Livewire\Inventory\MovementForm;
use App\Livewire\Inventory\MovementIndex;
use App\Livewire\PosScreen;
use App\Livewire\Staff\Form;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', Dashboard::class)->name('dashboard');
    Route::get('/pos', PosScreen::class)->middleware('permission:pos_access')->name('pos');
    Route::get('/api/pos/catalog', \App\Http\Controllers\PosCatalogController::class)->middleware('permission:pos_access')->name('api.pos.catalog');

    // Katalog
    Route::get('/katalog/kategori', CategoryIndex::class)->middleware('permission:manage_catalog')->name('catalog.categories');
    Route::get('/katalog/produk', ProductIndex::class)->middleware('permission:manage_catalog')->name('catalog.products');
    Route::get('/katalog/produk/tambah', ProductForm::class)->middleware('permission:manage_catalog')->name('catalog.products.create');
    Route::get('/katalog/produk/{id}/edit', ProductForm::class)->middleware('permission:manage_catalog')->name('catalog.products.edit');

    // Inventaris
    Route::get('/inventori/pergerakan', MovementIndex::class)->middleware('permission:manage_inventory')->name('inventory.movements');
    Route::get('/inventori/pergerakan/tambah', MovementForm::class)->middleware('permission:manage_inventory')->name('inventory.movements.create');

    // Manajemen
    Route::get('/pelanggan', Index::class)->middleware('permission:manage_customers')->name('customers');
    Route::get('/cabang', App\Livewire\Stores\Index::class)->middleware('permission:manage_stores')->name('stores');
    Route::get('/staff', App\Livewire\Staff\Index::class)->middleware('permission:manage_staff')->name('staff.index');
    Route::get('/staff/tambah', Form::class)->middleware('permission:manage_staff')->name('staff.create');
    Route::get('/staff/{id}/edit', Form::class)->middleware('permission:manage_staff')->name('staff.edit');
    Route::get('/laporan', App\Livewire\Reports\Index::class)->middleware('permission:view_reports')->name('reports');
});

require __DIR__.'/settings.php';
