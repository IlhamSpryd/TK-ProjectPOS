<x-layouts.app title="Manajemen Staff" :breadcrumbs="[['label' => 'Dashboard', 'route' => route('dashboard')], ['label' => 'Staff']]">
<div class="py-6">
    <x-slot:actions>
        <x-ui.button variant="primary" icon="plus" href="{{ route('staff.create') }}" wire:navigate>
            Tambah Staff
        </x-ui.button>
    </x-slot:actions>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama atau email..." class="w-full bg-white shadow-none" />
            </div>
        </div>

        <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">Nama Lengkap</x-ui.table.th>
                <x-ui.table.th>Role</x-ui.table.th>
                <x-ui.table.th>Email</x-ui.table.th>
                <x-ui.table.th>Status</x-ui.table.th>
                <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($staffMembers as $staff)
                <x-ui.table.tr :key="$staff->id">
                    <x-ui.table.td class="pl-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-600 font-medium">
                                {{ $staff->initials() }}
                            </div>
                            <div>
                                <span class="text-body font-medium text-neutral-800 block">{{ $staff->full_name }}</span>
                                <p class="text-body-sm text-neutral-500">ID: {{ substr($staff->id, 0, 8) }}</p>
                            </div>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/10">
                            {{ $staff->role?->name ?? 'N/A' }}
                        </span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-neutral-600">{{ $staff->email }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @if($staff->active)
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('staff.edit', $staff->id) }}" wire:navigate class="text-neutral-500 hover:text-primary-600" />
                            @if(auth()->id() !== $staff->id)
                                <x-ui.button size="sm" variant="ghost" icon="trash" wire:click="deleteStaff('{{ $staff->id }}')" wire:confirm="Yakin ingin menghapus staff ini?" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" />
                            @endif
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <x-ui.empty-state icon="users" title="Tidak ada staff" description="Belum ada data staff atau tidak ada yang sesuai dengan pencarian." />
                </x-slot:empty>
            @endforelse
            
            @if($staffMembers->hasPages())
                <x-slot:pagination>
                    {{ $staffMembers->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
    </x-ui.card>
</div>
</x-layouts.app>

