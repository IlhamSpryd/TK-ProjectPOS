<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/dashboard')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::view('dashboard', 'dashboard')->name('dashboard');
    Route::get('/pos', \App\Livewire\PosScreen::class)->name('pos');
    
    // Katalog
    Route::get('/katalog/kategori', \App\Livewire\Catalog\CategoryIndex::class)->name('catalog.categories');
    Route::get('/katalog/produk', \App\Livewire\Catalog\ProductIndex::class)->name('catalog.products');
    Route::get('/katalog/produk/tambah', \App\Livewire\Catalog\ProductForm::class)->name('catalog.products.create');
    Route::get('/katalog/produk/{id}/edit', \App\Livewire\Catalog\ProductForm::class)->name('catalog.products.edit');
});

require __DIR__.'/settings.php';
