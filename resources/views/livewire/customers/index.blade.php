<div class="py-6">

    {{-- Page Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-h2 text-neutral-900">Pelanggan</h2>
            <p class="text-body-sm text-neutral-500 mt-1">Kelola data pelanggan dan program loyalitas.</p>
        </div>
        <x-ui.button variant="primary" icon="plus" wire:click="$dispatch('open-customer-modal')" class="shrink-0">
            Tambah Pelanggan
        </x-ui.button>
    </div>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        {{-- Filter Bar --}}
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="relative flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari nama atau telepon..." class="w-full bg-white shadow-none" />
                @if ($search)
                    <button type="button" wire:click="$set('search', '')" aria-label="Hapus pencarian"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-full text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400">
                        <flux:icon.x-mark class="w-4 h-4" />
                    </button>
                @endif
            </div>
            <p class="text-caption text-neutral-500 shrink-0" aria-live="polite">
                {{ $customers->total() }} pelanggan{{ $search ? ' ditemukan' : '' }}
            </p>
        </div>

        <div wire:loading.class="opacity-60" wire:target="search" class="transition-opacity duration-150">

            {{-- Desktop Table --}}
            <div class="hidden md:block">
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
                                    <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200 shrink-0">
                                        <flux:icon.user class="w-5 h-5" />
                                    </div>
                                    <div>
                                        <span class="text-body font-medium text-neutral-800 block">{{ $customer->name }}</span>
                                        <span class="text-caption text-neutral-500">{{ $customer->npwp ? 'NPWP: '.$customer->npwp : 'Personal' }}</span>
                                    </div>
                                </div>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                <div class="flex flex-col">
                                    <span class="text-body-sm text-neutral-700 font-medium">{{ $customer->phone ?? '—' }}</span>
                                    <span class="text-caption text-neutral-500">{{ $customer->email ?? '—' }}</span>
                                </div>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                <span class="font-mono text-body-sm font-bold text-neutral-800">{{ number_format($customer->loyalty_points) }} <span class="text-caption font-normal text-neutral-500">pts</span></span>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                @if($customer->active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                                @endif
                            </x-ui.table.td>
                            <x-ui.table.td class="text-right pr-6">
                                <div class="flex items-center justify-end gap-1">
                                    <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                        wire:click="$dispatch('editCustomer', { id: '{{ $customer->id }}' })"
                                        class="text-neutral-500 hover:text-neutral-900"
                                        aria-label="Edit {{ $customer->name }}" />
                                    <x-ui.button size="sm" variant="ghost" icon="trash"
                                        wire:click="deleteCustomer('{{ $customer->id }}')"
                                        wire:confirm="Yakin ingin menghapus pelanggan &quot;{{ $customer->name }}&quot;?"
                                        class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50"
                                        aria-label="Hapus {{ $customer->name }}" />
                                </div>
                            </x-ui.table.td>
                        </x-ui.table.tr>
                    @empty
                        <x-slot:empty>
                            <x-ui.empty-state icon="users" title="Tidak ada pelanggan"
                                description="Belum ada data pelanggan atau tidak ada yang sesuai dengan pencarian." />
                        </x-slot:empty>
                    @endforelse

                    @if($customers->hasPages())
                        <x-slot:pagination>
                            {{ $customers->links() }}
                        </x-slot:pagination>
                    @endif
                </x-ui.table>
            </div>

            {{-- Mobile Card List --}}
            <div class="block md:hidden border-t border-neutral-200 divide-y divide-neutral-100">
                @forelse ($customers as $customer)
                    <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div class="w-10 h-10 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200 shrink-0">
                                    <flux:icon.user class="w-5 h-5" />
                                </div>
                                <div class="min-w-0">
                                    <span class="text-body font-medium text-neutral-800 block truncate">{{ $customer->name }}</span>
                                    <span class="text-caption text-neutral-500">{{ $customer->npwp ? 'NPWP: '.$customer->npwp : 'Personal' }}</span>
                                </div>
                            </div>
                            <div>
                                @if($customer->active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2">
                            <div>
                                <span class="text-neutral-500 block text-caption">Kontak</span>
                                <span class="text-neutral-700 font-medium block text-body-sm">{{ $customer->phone ?? '—' }}</span>
                                <span class="text-neutral-500 text-caption">{{ $customer->email ?? '—' }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-500 block text-caption">Loyalty Points</span>
                                <span class="font-mono font-bold text-neutral-800 text-body-sm">{{ number_format($customer->loyalty_points) }} <span class="text-caption font-normal text-neutral-500">pts</span></span>
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                wire:click="$dispatch('editCustomer', { id: '{{ $customer->id }}' })"
                                class="text-neutral-500 hover:text-neutral-900"
                                aria-label="Edit {{ $customer->name }}" />
                            <x-ui.button size="sm" variant="ghost" icon="trash"
                                wire:click="deleteCustomer('{{ $customer->id }}')"
                                wire:confirm="Yakin ingin menghapus pelanggan &quot;{{ $customer->name }}&quot;?"
                                class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50"
                                aria-label="Hapus {{ $customer->name }}" />
                        </div>
                    </div>
                @empty
                    <div class="p-8">
                        <x-ui.empty-state icon="users" title="Tidak ada pelanggan"
                            description="Belum ada data pelanggan atau tidak ada yang sesuai dengan pencarian." />
                    </div>
                @endforelse
                @if($customers->hasPages())
                    <div class="p-4 border-t border-neutral-200">
                        {{ $customers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-ui.card>

    <x-ui.modal name="customer-modal" maxWidth="2xl">
        @livewire('customers.form')
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
