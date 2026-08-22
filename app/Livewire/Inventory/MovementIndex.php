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

        if ($store) {
            $query->where('store_id', $store->id);
        }

        if ($this->search) {
            $query->whereHas('variant.product', function ($q) {
                $q->where('name', 'ilike', '%' . $this->search . '%');
            });
        }

        if ($this->typeFilter) {
            $query->where('movement_type', $this->typeFilter);
        }

        $movements = $query->orderBy('created_at', 'desc')->paginate(15);


        return view('livewire.inventory.movement-index', [
            'movements' => $movements,
            'store' => $store
        ])
            ->layout('components.layouts.app', [
                'title' => 'Pergerakan Stok',
                'breadcrumbs' => [
                    ['label' => 'Dashboard', 'route' => route('dashboard')],
                    ['label' => 'Pergerakan Stok']
                ],
                'actions' => new \Illuminate\Support\HtmlString(\Illuminate\Support\Facades\Blade::render(
                    '<x-ui.button variant="primary" icon="plus" href="{{ route(\'inventory.movements.create\') }}" wire:navigate>Tambah Pergerakan</x-ui.button>'
                ))
            ]);
    }
}
