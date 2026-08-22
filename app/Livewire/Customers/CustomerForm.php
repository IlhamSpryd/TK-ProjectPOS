<?php

namespace App\Livewire\Customers;

use Livewire\Component;
use App\Models\Customer;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class CustomerForm extends Component
{
    public ?string $customerId = null;
    public string $name = '';
    public ?string $email = null;
    public ?string $phone = null;
    public ?string $address = null;
    public ?string $npwp = null;
    public int $loyalty_points = 0;
    public bool $active = true;

    protected $listeners = ['editCustomer' => 'loadCustomer'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'email' => 'nullable|email|max:255',
        'phone' => 'nullable|string|max:20',
        'address' => 'nullable|string',
        'npwp' => 'nullable|string|max:50',
        'loyalty_points' => 'integer|min:0',
        'active' => 'boolean',
    ];

    public function loadCustomer($id = null)
    {
        $this->resetValidation();
        $this->reset(['name', 'email', 'phone', 'address', 'npwp', 'loyalty_points', 'active']);
        
        $this->customerId = $id;

        if ($this->customerId) {
            $customer = Customer::findOrFail($this->customerId);
            $this->name = $customer->name;
            $this->email = $customer->email;
            $this->phone = $customer->phone;
            $this->address = $customer->address;
            $this->npwp = $customer->npwp;
            $this->loyalty_points = $customer->loyalty_points;
            $this->active = $customer->active;
        }

        // Trigger Alpine to open the Flux modal
        $this->dispatch('open-customer-modal');
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->customerId) {
                $customer = Customer::findOrFail($this->customerId);
                $customer->update([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'npwp' => $this->npwp,
                    'loyalty_points' => $this->loyalty_points,
                    'active' => $this->active,
                ]);
                $message = 'Pelanggan berhasil diperbarui.';
            } else {
                Customer::create([
                    'id' => Str::uuid()->toString(),
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'address' => $this->address,
                    'npwp' => $this->npwp,
                    'loyalty_points' => $this->loyalty_points,
                    'active' => $this->active,
                ]);
                $message = 'Pelanggan berhasil ditambahkan.';
            }

            $this->dispatch('customerSaved');
            $this->dispatch('close-customer-modal');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan pelanggan: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menyimpan pelanggan.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.customers.customer-form');
    }
}
