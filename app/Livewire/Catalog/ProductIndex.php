<?php

namespace App\Livewire\Catalog;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Product;
use Illuminate\Support\Facades\Log;

class ProductIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteProduct($id)
    {
        try {
            $product = Product::findOrFail($id);
            // This will cascade delete variants via DB constraints or model events
            // but let's make sure it deletes safely.
            $product->delete();
            $this->dispatch('toast', message: 'Produk berhasil dihapus.', type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus produk: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus produk.', type: 'error');
        }
    }

    public function render()
    {
        $products = Product::with('category')
            ->where('name', 'ilike', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.catalog.product-index', [
            'products' => $products
        ])->layout('layouts.app');
    }
}
