<div class="py-6">
    <x-ui.page-header title="Pelanggan" description="Kelola daftar pelanggan dan riwayat transaksi mereka.">
        <x-ui.button variant="primary" icon="plus" wire:click="$dispatch('open-customer-modal')">
            Tambah Pelanggan
        </x-ui.button>
    </x-ui.page-header>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama atau telepon..." class="w-full bg-white shadow-none" />
            </div>
        </div>

        <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">Pelanggan</x-ui.table.th>
                <x-ui.table.th>Kontak</x-ui.table.th>
                <x-ui.table.th>Loyalty Points</x-ui.table.th>
                <x-ui.table.th>Status</x-ui.table.th>
                <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($customers as $customer)
                <x-ui.table.tr :key="$customer->id">
                    <x-ui.table.td class="pl-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200">
                                <flux:icon.user class="w-5 h-5" />
                            </div>
                            <div>
                                <span class="text-body font-medium text-neutral-800 block">{{ $customer->name }}</span>
                                <span class="text-xs text-neutral-500">{{ $customer->npwp ? 'NPWP: '.$customer->npwp : '-' }}</span>
                            </div>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <div class="flex flex-col">
                            <span class="text-body-sm text-neutral-700 font-medium">{{ $customer->phone ?? '-' }}</span>
                            <span class="text-xs text-neutral-500">{{ $customer->email ?? '-' }}</span>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-body-sm font-bold text-primary-600">{{ number_format($customer->loyalty_points) }} pts</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @if($customer->active)
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" wire:click="$dispatch('editCustomer', { id: '{{ $customer->id }}' })" class="text-neutral-500 hover:text-primary-600" />
                            <x-ui.button size="sm" variant="ghost" icon="trash" wire:click="deleteCustomer('{{ $customer->id }}')" wire:confirm="Yakin ingin menghapus pelanggan ini?" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" />
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-3 border border-neutral-200 mx-auto">
                        <flux:icon.users class="w-8 h-8 text-neutral-400" />
                    </div>
                    <p class="font-medium text-neutral-500">Tidak ada pelanggan ditemukan.</p>
                </x-slot:empty>
            @endforelse
            
            @if($customers->hasPages())
                <x-slot:pagination>
                    {{ $customers->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
    </x-ui.card>

    <x-ui.modal name="customer-modal" maxWidth="2xl">
        @livewire('customers.customer-form')
    </x-ui.modal>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-customer-modal', (event) => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'customer-modal' }));
            });
            Livewire.on('close-customer-modal', (event) => {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'customer-modal' }));
            });
        });
    </script>
</div>
