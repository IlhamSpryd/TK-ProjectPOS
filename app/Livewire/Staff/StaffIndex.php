<?php

namespace App\Livewire\Staff;

use App\Models\Staff;
use Livewire\Component;
use Livewire\WithPagination;

class StaffIndex extends Component
{
    use WithPagination;

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $staffMembers = Staff::with('role')
            ->where('full_name', 'ilike', '%' . $this->search . '%')
            ->orWhere('email', 'ilike', '%' . $this->search . '%')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.staff.staff-index', [
            'staffMembers' => $staffMembers
        ])->layout('components.layouts.app');
    }
}
