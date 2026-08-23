<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\WithPagination;

class CategoryIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['categorySaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCategory($id)
    {
        try {
            $category = Category::findOrFail($id);
            // TODO: Tambahkan validasi pengecekan apakah kategori sedang digunakan oleh produk
            $category->delete();
            $this->dispatch('toast', message: 'Kategori berhasil dihapus.', type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus kategori: '.$e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus kategori.', type: 'error');
        }
    }

    public function render()
    {
        // Supabase uses PostgreSQL, so ilike is safe and case-insensitive
        $categories = Category::where('name', 'ilike', '%'.$this->search.'%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.catalog.category-index', [
            'categories' => $categories,
        ])->layout('components.layouts.app', [
            'title' => 'Kategori Produk',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Katalog Produk', 'route' => route('catalog.products')],
                ['label' => 'Kategori'],
            ],
            'actions' => new HtmlString(Blade::render(
                '<x-ui.button variant="primary" icon="plus" x-data x-on:click="$dispatch(\'editCategory\')">Tambah Kategori</x-ui.button>'
            )),
        ]);
    }
}
