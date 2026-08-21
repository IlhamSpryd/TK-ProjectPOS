<div class="py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <h1 class="text-h2 font-black text-neutral-900 tracking-tight">Cabang (Store)</h1>
            <p class="text-body text-neutral-500 mt-1">Kelola daftar cabang toko dan pengaturannya.</p>
        </div>
        <div>
            <x-ui.button variant="primary" icon="plus" wire:click="$dispatch('open-store-modal')">
                Tambah Cabang
            </x-ui.button>
        </div>
    </div>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama cabang atau kota..." class="w-full bg-white shadow-none" />
            </div>
        </div>

        <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">Nama Cabang</x-ui.table.th>
                <x-ui.table.th>Tipe Bisnis</x-ui.table.th>
                <x-ui.table.th>Kota / Wilayah</x-ui.table.th>
                <x-ui.table.th>Status</x-ui.table.th>
                <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($stores as $store)
                <x-ui.table.tr :key="$store->id">
                    <x-ui.table.td class="pl-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200">
                                <flux:icon.building-storefront class="w-5 h-5" />
                            </div>
                            <div>
                                <span class="text-body font-bold text-neutral-900 block">{{ $store->name }}</span>
                                <span class="text-xs text-neutral-500">{{ $store->phone ?? 'Tidak ada nomor telepon' }}</span>
                            </div>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-body-sm text-neutral-600 font-medium">{{ $store->business_type ?? '-' }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-body-sm text-neutral-600">{{ $store->city ?? '-' }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @if($store->active)
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" class="text-neutral-500 hover:text-primary-600" />
                            <x-ui.button size="sm" variant="ghost" icon="trash" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" />
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-3 border border-neutral-200 mx-auto">
                        <flux:icon.building-storefront class="w-8 h-8 text-neutral-400" />
                    </div>
                    <p class="font-medium text-neutral-500">Tidak ada cabang ditemukan.</p>
                </x-slot:empty>
            @endforelse
            
            @if($stores->hasPages())
                <x-slot:pagination>
                    {{ $stores->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
    </x-ui.card>
</div>
