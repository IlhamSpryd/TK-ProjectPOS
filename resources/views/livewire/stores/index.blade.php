<x-layouts.app title="Cabang" :breadcrumbs="[['label' => 'Dashboard', 'route' => route('dashboard')], ['label' => 'Cabang']]">
<div class="py-6">
    <x-slot:actions>
        <x-ui.button variant="primary" icon="plus" wire:click="$dispatch('open-store-modal')">
            Tambah Cabang
        </x-ui.button>
    </x-slot:actions>

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
                                <span class="text-body font-medium text-neutral-800 block">{{ $store->name }}</span>
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
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" wire:click="$dispatch('editStore', { id: '{{ $store->id }}' })" class="text-neutral-500 hover:text-primary-600" />
                            <x-ui.button size="sm" variant="ghost" icon="trash" wire:click="deleteStore('{{ $store->id }}')" wire:confirm="Yakin ingin menghapus cabang ini?" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" />
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <x-ui.empty-state icon="building-storefront" title="Tidak ada cabang" description="Belum ada data cabang atau tidak ada yang sesuai dengan pencarian." />
                </x-slot:empty>
            @endforelse
            
            @if($stores->hasPages())
                <x-slot:pagination>
                    {{ $stores->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
    </x-ui.card>

    <x-ui.modal name="store-modal" maxWidth="2xl">
        @livewire('stores.form')
    </x-ui.modal>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-store-modal', (event) => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'store-modal' }));
            });
            Livewire.on('close-store-modal', (event) => {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'store-modal' }));
            });
        });
    </script>
</div>
</x-layouts.app>

