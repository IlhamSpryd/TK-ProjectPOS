<?php

namespace App\Livewire\Stores;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Store;

class StoreIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
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
