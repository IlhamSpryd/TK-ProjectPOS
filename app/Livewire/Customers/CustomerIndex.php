<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class CustomerIndex extends Component
{
    use WithPagination;

    public $search = '';

    protected $listeners = ['customerSaved' => '$refresh'];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function deleteCustomer($id)
    {
        try {
            $customer = Customer::findOrFail($id);
            $customer->delete();
            $this->dispatch('toast', message: 'Pelanggan berhasil dihapus.', type: 'success');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Gagal menghapus pelanggan: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus pelanggan.', type: 'error');
        }
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
