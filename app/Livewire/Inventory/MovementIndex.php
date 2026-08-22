<?php

namespace App\Livewire\Inventory;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\InventoryMovement;
use Illuminate\Support\Facades\Auth;

class MovementIndex extends Component
{
    use WithPagination;

    public $search = '';
    public $typeFilter = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTypeFilter()
    {
        $this->resetPage();
    }

    public function render()
    {
        $staff = Auth::user();
        $store = ($staff && method_exists($staff, 'getActiveStore')) ? $staff->getActiveStore() : null;
        
        $query = InventoryMovement::with(['variant.product', 'staff']);
            // Assuming there is a created_at column even if timestamps=false. If not we'll check it later.
            // Let's remove orderBy created_at for now just in case. Or I can check the sql schema.
            
        if ($store) {
            $query->where('store_id', $store->id);
        }

        if ($this->search) {
            $query->whereHas('variant.product', function($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%');
            });
        }
        
        if ($this->typeFilter) {
            $query->where('movement_type', $this->typeFilter);
        }

        $movements = $query->paginate(15);

        return view('livewire.inventory.movement-index', [
            'movements' => $movements,
            'store' => $store
        ])->layout('layouts.app');
    }
}
