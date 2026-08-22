<?php

namespace App\Livewire\Staff;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;

class Form extends Component
{
    public ?string $staffId = null;
    
    public string $full_name = '';
    public string $email = '';
    public ?string $role_id = null;
    
    // Opsional saat edit
    public string $password = '';
    public string $password_confirmation = '';
    public string $pin = '';
    public string $pin_confirmation = '';
    
    public bool $active = true;

    public function mount($id = null): void
    {
        $this->staffId = $id;
        
        if ($this->staffId) {
            $staff = Staff::findOrFail($this->staffId);
            $this->full_name = $staff->full_name;
            $this->email = $staff->email;
            $this->role_id = $staff->role_id;
            $this->active = $staff->active;
        }
    }

    protected function rules(): array
    {
        $rules = [
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255|unique:staff,email,' . $this->staffId,
            'role_id'   => 'required|exists:roles,id',
            'active'    => 'boolean',
        ];

        // Password & PIN required saat create, opsional saat edit
        if (!$this->staffId) {
            $rules['password'] = 'required|string|min:8|confirmed';
            $rules['pin']      = 'required|string|min:4|max:6|confirmed';
        } else {
            $rules['password'] = 'nullable|string|min:8|confirmed';
            $rules['pin']      = 'nullable|string|min:4|max:6|confirmed';
        }

        return $rules;
    }

    public function save(): void
    {
        $this->validate();

        try {
            if ($this->staffId) {
                $staff = Staff::findOrFail($this->staffId);
                
                $updateData = [
                    'full_name' => $this->full_name,
                    'email'     => $this->email,
                    'role_id'   => $this->role_id,
                    'active'    => $this->active,
                ];

                if (!empty($this->password)) {
                    $updateData['password_hash'] = Hash::make($this->password);
                }

                if (!empty($this->pin)) {
                    $updateData['pin_hash'] = Hash::make($this->pin);
                }

                $staff->update($updateData);
                $message = 'Data staff berhasil diperbarui.';
            } else {
                Staff::create([
                    'id'            => Str::uuid()->toString(),
                    'full_name'     => $this->full_name,
                    'email'         => $this->email,
                    'role_id'       => $this->role_id,
                    'password_hash' => Hash::make($this->password),
                    'pin_hash'      => Hash::make($this->pin),
                    'active'        => $this->active,
                ]);
                $message = 'Akun staff berhasil dibuat.';
            }

            $this->dispatch('toast', message: $message, type: 'success');
            $this->redirectRoute('staff.index', navigate: true);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan data staff: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Gagal menyimpan data staff.', type: 'error');
        }
    }

    public function render()
    {
        return view('livewire.staff.form', [
            'roles' => Role::all(),
        ])
            ->layout('components.layouts.app', [
                'title' => $this->staffId ? 'Edit Staff' : 'Tambah Staff Baru',
                'breadcrumbs' => [
                    ['label' => 'Dashboard', 'route' => route('dashboard')],
                    ['label' => 'Staff', 'route' => route('staff.index')],
                    ['label' => $this->staffId ? 'Edit' : 'Tambah']
                ],
                'actions' => new \Illuminate\Support\HtmlString(\Illuminate\Support\Facades\Blade::render(
                    '<x-ui.button variant="ghost" href="{{ route(\'staff.index\') }}" wire:navigate class="px-5">Batal</x-ui.button>
                     <x-ui.button variant="primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
                         <span wire:loading.remove wire:target="save"><flux:icon name="check" class="mr-2 -ml-1 w-5 h-5"/>Simpan Akun</span>
                         <span wire:loading wire:target="save" class="flex items-center"><svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>Menyimpan...</span>
                     </x-ui.button>'
                ))
            ]);
    }
}
