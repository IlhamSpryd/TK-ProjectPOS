<?php

namespace App\Livewire\Staff;

use App\Models\Role;
use App\Models\Staff;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;
use Illuminate\Support\Str;

class StaffForm extends Component
{
    public $full_name = '';
    public $email = '';
    public $role_id = '';
    public $password = '';
    public $password_confirmation = '';
    public $pin = '';
    public $pin_confirmation = '';
    public $active = true;

    protected function rules()
    {
        return [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:staff,email',
            'role_id' => 'required|exists:roles,id',
            'password' => 'required|string|min:8|confirmed',
            'pin' => 'required|string|min:4|max:6|confirmed',
        ];
    }

    public function save()
    {
        $this->validate();

        $staff = Staff::create([
            'id' => (string) Str::uuid(),
            'full_name' => $this->full_name,
            'email' => $this->email,
            'role_id' => $this->role_id,
            'password_hash' => Hash::make($this->password),
            'pin_hash' => Hash::make($this->pin),
            'active' => $this->active,
        ]);

        session()->flash('success', 'Akun staff berhasil dibuat.');
        
        return $this->redirectRoute('staff.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.staff.staff-form', [
            'roles' => Role::all()
        ])->layout('components.layouts.app');
    }
}
