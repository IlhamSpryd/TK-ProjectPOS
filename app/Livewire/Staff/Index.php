<?php

namespace App\Livewire\Staff;

use App\Models\Staff;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\HtmlString;
use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;

    public string $search = '';

    protected $listeners = ['staffSaved' => '$refresh'];

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function deleteStaff(string $id): void
    {
        Gate::authorize('manage_staff');

        // Cegah staff menghapus akunnya sendiri
        if (auth()->id() === $id) {
            $this->dispatch('toast', message: 'Anda tidak dapat menghapus akun Anda sendiri.', type: 'error');

            return;
        }

        try {
            $staff = Staff::findOrFail($id);
            $staff->delete(); // SoftDelete
            $this->dispatch('toast', message: 'Staff berhasil dihapus.', type: 'success');
        } catch (\Exception $e) {
            Log::error('Gagal menghapus staff: '.$e->getMessage());
            $this->dispatch('toast', message: 'Gagal menghapus staff.', type: 'error');
        }
    }

    public function render()
    {
        $staffMembers = Staff::with(['role', 'stores'])
            ->when(auth()->user()->isSuperAdmin(), function ($query) {
                $query->withoutTenantScope();
            })
            ->where(function ($q) {
                $q->where('full_name', 'ilike', '%'.$this->search.'%')
                    ->orWhere('email', 'ilike', '%'.$this->search.'%');
            })
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        $view = 'livewire.staff.index';
        return view($view, [
            'staffMembers' => $staffMembers,
        ])->layout('components.layouts.app', [
            'title' => 'Manajemen Staff',
            'breadcrumbs' => [
                ['label' => 'Dashboard', 'route' => route('dashboard')],
                ['label' => 'Staff'],
            ],
            'actions' => new HtmlString(Blade::render(
                '<x-ui.button variant="primary" icon="plus" href="{{ route(\'staff.create\') }}" wire:navigate>Tambah Staff</x-ui.button>'
            )),
        ]);
    }
}
