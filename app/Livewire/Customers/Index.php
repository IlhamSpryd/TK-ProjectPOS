<?php

namespace App\Livewire\Customers;

use App\Models\Customer;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\WithPagination;

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
            Log::error('Gagal menghapus pelanggan: '.$e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus pelanggan.', type: 'error');
        }
    }

    public function render()
    {
        $customers = Customer::where('name', 'ilike', '%'.$this->search.'%')
            ->orWhere('phone', 'ilike', '%'.$this->search.'%')
            ->orderBy('name')
            ->paginate(10);

        return view('livewire.customers.index', [
            'customers' => $customers,
        ])->layout('components.layouts.app', [
            'title' => 'Pelanggan',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Pelanggan'],
            ],
            'actions' => new HtmlString(Blade::render(
                '<x-ui.button variant="primary" icon="plus" x-data x-on:click="$dispatch(\'open-customer-modal\')">Tambah Pelanggan</x-ui.button>'
            )),
        ]);
    }
}
