<?php

namespace App\Livewire\Catalog;

use App\Models\Category;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
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
        Gate::authorize('manage_catalog');

        try {
            $category = Category::withCount('products')->findOrFail($id);

            if ($category->products_count > 0) {
                $this->dispatch('toast',
                    message: "Kategori masih dipakai oleh {$category->products_count} produk. Pindahkan produk terlebih dahulu.",
                    type: 'error'
                );
                return;
            }

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

        $view = 'livewire.catalog.category-index';
        return view($view, [
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
