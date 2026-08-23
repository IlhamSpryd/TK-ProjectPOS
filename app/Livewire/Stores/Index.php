<?php

namespace App\Livewire\Stores;

use App\Models\Store;
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
        $stores = Store::where('name', 'ilike', '%'.$this->search.'%')
            ->orWhere('city', 'ilike', '%'.$this->search.'%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.stores.index', [
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
