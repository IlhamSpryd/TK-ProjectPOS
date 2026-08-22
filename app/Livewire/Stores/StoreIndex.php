<?php

namespace App\Livewire\Stores;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Store;

class StoreIndex extends Component
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
        try {
            $store = Store::findOrFail($id);
            $store->delete();
            $this->dispatch('toast', message: 'Cabang berhasil dihapus.', type: 'success');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus cabang: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus cabang.', type: 'error');
        }
    }

    public function render()
    {
        $stores = Store::where('name', 'ilike', '%' . $this->search . '%')
            ->orWhere('city', 'ilike', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.stores.store-index', [
            'stores' => $stores
        ])->layout('layouts.app');
    }
}
