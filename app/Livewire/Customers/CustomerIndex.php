<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class CustomerIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $customers = Customer::where('name', 'ilike', '%' . $this->search . '%')
            ->orWhere('phone', 'ilike', '%' . $this->search . '%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.customers.customer-index', [
            'customers' => $customers
        ])->layout('layouts.app');
    }
}
