<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Customer;

class Index extends Component
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

        return view('livewire.customers.index', [
            'customers' => $customers
        ])->layout('components.layouts.app', [
            'title' => 'Pelanggan',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Pelanggan']
            ],
            'actions' => new \Illuminate\Support\HtmlString(\Illuminate\Support\Facades\Blade::render(
                '<x-ui.button variant="primary" icon="plus" wire:click="$dispatch(\'open-customer-modal\')">Tambah Pelanggan</x-ui.button>'
            ))
        ]);
    }
}



