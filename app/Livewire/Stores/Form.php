<?php

namespace App\Livewire\Stores;

use Livewire\Component;
use App\Models\Store;
use App\Models\TaxCategory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    public ?string $storeId = null;
    public string $name = '';
    public ?string $business_type = null;
    public bool $is_pkp = false;
    public ?string $npwp = null;
    public ?string $address = null;
    public ?string $city = null;
    public ?string $province = null;
    public ?string $phone = null;
    public ?string $email = null;
    public string $currency = 'IDR';
    public string $timezone = 'Asia/Jakarta';
    public bool $active = true;
    public ?string $default_tax_category_id = null;

    protected $listeners = ['editStore' => 'loadStore'];

    protected $rules = [
        'name' => 'required|string|max:255',
        'business_type' => 'nullable|string|max:100',
        'is_pkp' => 'boolean',
        'npwp' => 'nullable|string|max:50',
        'address' => 'nullable|string',
        'city' => 'nullable|string|max:100',
        'province' => 'nullable|string|max:100',
        'phone' => 'nullable|string|max:20',
        'email' => 'nullable|email|max:255',
        'currency' => 'required|string|max:3',
        'timezone' => 'required|string|max:100',
        'active' => 'boolean',
        'default_tax_category_id' => 'nullable|exists:tax_categories,id'
    ];

    public function loadStore($id = null)
    {
        $this->resetValidation();
        $this->reset([
            'name', 'business_type', 'is_pkp', 'npwp', 'address', 'city', 
            'province', 'phone', 'email', 'currency', 'timezone', 'active', 'default_tax_category_id'
        ]);
        
        $this->currency = 'IDR';
        $this->timezone = 'Asia/Jakarta';
        $this->active = true;
        
        $this->storeId = $id;

        if ($this->storeId) {
            $store = Store::findOrFail($this->storeId);
            $this->name = $store->name;
            $this->business_type = $store->business_type;
            $this->is_pkp = $store->is_pkp;
            $this->npwp = $store->npwp;
            $this->address = $store->address;
            $this->city = $store->city;
            $this->province = $store->province;
            $this->phone = $store->phone;
            $this->email = $store->email;
            $this->currency = $store->currency;
            $this->timezone = $store->timezone;
            $this->active = $store->active;
            $this->default_tax_category_id = $store->default_tax_category_id;
        }

        // Trigger Alpine to open the Flux modal
        $this->dispatch('open-store-modal');
    }

    public function save()
    {
        $this->validate();

        try {
            if ($this->storeId) {
                $store = Store::findOrFail($this->storeId);
                $store->update([
                    'name' => $this->name,
                    'business_type' => $this->business_type,
                    'is_pkp' => $this->is_pkp,
                    'npwp' => $this->npwp,
                    'address' => $this->address,
                    'city' => $this->city,
                    'province' => $this->province,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'currency' => $this->currency,
                    'timezone' => $this->timezone,
                    'active' => $this->active,
                    'default_tax_category_id' => $this->default_tax_category_id,
                ]);
                $message = 'Cabang berhasil diperbarui.';
            } else {
                Store::create([
                    'id' => Str::uuid()->toString(),
                    'name' => $this->name,
                    'business_type' => $this->business_type,
                    'is_pkp' => $this->is_pkp,
                    'npwp' => $this->npwp,
                    'address' => $this->address,
                    'city' => $this->city,
                    'province' => $this->province,
                    'phone' => $this->phone,
                    'email' => $this->email,
                    'currency' => $this->currency,
                    'timezone' => $this->timezone,
                    'active' => $this->active,
                    'default_tax_category_id' => $this->default_tax_category_id,
                ]);
                $message = 'Cabang berhasil ditambahkan.';
            }

            $this->dispatch('storeSaved');
            $this->dispatch('close-store-modal');
            $this->dispatch('toast', message: $message, type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menyimpan cabang: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menyimpan cabang.', type: 'error');
        }
    }

    public function render()
    {
        $taxCategories = TaxCategory::where('active', true)->orderBy('name')->get();
        return view('livewire.stores.form', [
            'taxCategories' => $taxCategories
        ]);
    }
}


