<?php

namespace App\Livewire\Stores;

use App\Models\Store;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['storeSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteStore($id)
    {
        Gate::authorize('manage_stores');

        try {
            $store = Store::findOrFail($id);
            $store->delete();
            $this->dispatch('toast', message: 'Cabang berhasil dihapus.', type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus cabang: '.$e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus cabang.', type: 'error');
        }
    }

    public function render()
    {
        $stores = Store::query()
            ->when($this->search, function ($query) {
                $query->where(function ($q) {
                    $q->where('name', 'ilike', '%'.$this->search.'%')
                      ->orWhere('city', 'ilike', '%'.$this->search.'%');
                });
            })
            ->orderBy('name')
            ->paginate(10);

        $view = 'livewire.stores.index';
        return view($view, [
            'stores' => $stores,
        ])->layout('components.layouts.app', [
            'title' => 'Cabang',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Cabang'],
            ],
        ]);
    }
}
