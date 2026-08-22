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
        ]);
    }
}



