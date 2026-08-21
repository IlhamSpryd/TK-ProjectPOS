<div>
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-title-2 font-semibold text-neutral-900">Manajemen Staff</h1>
            <p class="text-body text-neutral-500 mt-1">Kelola akun dan akses staf aplikasi Anda.</p>
        </div>
        <div class="flex items-center gap-3">
            <div class="w-64">
                <x-ui.input 
                    name="search"
                    type="search" 
                    wire:model.live="search" 
                    placeholder="Cari nama atau email..." 
                    icon="<circle cx='11' cy='11' r='8'></circle><path d='m21 21-4.3-4.3'></path>"
                />
            </div>
            <a href="{{ route('staff.create') }}" wire:navigate>
                <x-ui.button variant="primary">
                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 -ml-1"><path d="M5 12h14"></path><path d="M12 5v14"></path></svg>
                    Tambah Staff
                </x-ui.button>
            </a>
        </div>
    </div>

    <x-ui.card>
        <x-ui.table.index>
            <x-slot name="thead">
                <tr>
                    <x-ui.table.th>Nama Lengkap</x-ui.table.th>
                    <x-ui.table.th>Role</x-ui.table.th>
                    <x-ui.table.th>Email</x-ui.table.th>
                    <x-ui.table.th>Status</x-ui.table.th>
                    <x-ui.table.th class="text-right">Aksi</x-ui.table.th>
                </tr>
            </x-slot>

            <x-slot name="tbody">
                @forelse($staffMembers as $staff)
                    <tr>
                        <x-ui.table.td>
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-600 font-medium">
                                    {{ $staff->initials() }}
                                </div>
                                <div>
                                    <p class="font-medium text-neutral-900">{{ $staff->full_name }}</p>
                                    <p class="text-body-sm text-neutral-500">ID: {{ substr($staff->id, 0, 8) }}</p>
                                </div>
                            </div>
                        </x-ui.table.td>
                        <x-ui.table.td>
                            <span class="inline-flex items-center rounded-full bg-primary-50 px-2 py-1 text-xs font-medium text-primary-700 ring-1 ring-inset ring-primary-600/10">
                                {{ $staff->role ? $staff->role->name : 'N/A' }}
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
                        <x-ui.table.td class="text-right">
                            <x-ui.button variant="secondary" class="!px-2 !py-1" title="Edit Staff">
                                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"></path><path d="m15 5 4 4"></path></svg>
                            </x-ui.button>
                        </x-ui.table.td>
                    </tr>
                @empty
                    <tr>
                        <x-ui.table.td colspan="5" class="text-center py-8">
                            <div class="flex flex-col items-center justify-center text-neutral-500">
                                <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round" class="mb-4 text-neutral-400"><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M22 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path></svg>
                                <p class="text-body font-medium">Tidak ada staff ditemukan.</p>
                                <p class="text-body-sm mt-1">Coba sesuaikan kata kunci pencarian Anda.</p>
                            </div>
                        </x-ui.table.td>
                    </tr>
                @endforelse
            </x-slot>
        </x-ui.table.index>

        @if($staffMembers->hasPages())
            <div class="px-6 py-4 border-t border-neutral-200">
                {{ $staffMembers->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
