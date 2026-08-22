<div class="py-6">
    <x-ui.page-header title="Pergerakan Inventaris" description="Kelola dan pantau arus masuk/keluar stok produk di cabang Anda.">
        <x-ui.button variant="primary" icon="plus" href="{{ route('inventory.movements.create') }}" wire:navigate>
            Tambah Pergerakan
        </x-ui.button>
    </x-ui.page-header>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50 flex items-center justify-between gap-4">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama produk..." class="w-full bg-white shadow-none" />
            </div>
            <div>
                <select wire:model.live="typeFilter" class="bg-white border border-neutral-200 text-neutral-700 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                    <option value="">Semua Tipe</option>
                    <option value="IN">Masuk (IN)</option>
                    <option value="OUT">Keluar (OUT)</option>
                    <option value="SALE">Penjualan (SALE)</option>
                    <option value="RETURN">Retur (RETURN)</option>
                    <option value="ADJUSTMENT">Penyesuaian (ADJUSTMENT)</option>
                </select>
            </div>
        </div>

        <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">Produk</x-ui.table.th>
                <x-ui.table.th>Tipe</x-ui.table.th>
                <x-ui.table.th>Perubahan Qty</x-ui.table.th>
                <x-ui.table.th>Keterangan</x-ui.table.th>
                <x-ui.table.th>Staff</x-ui.table.th>
                <x-ui.table.th>Tanggal</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($movements as $movement)
                <x-ui.table.tr :key="$movement->id">
                    <x-ui.table.td class="pl-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-neutral-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-neutral-200">
                                @if($movement->variant->product && $movement->variant->product->image_url)
                                    <img src="{{ Storage::url($movement->variant->product->image_url) }}" alt="{{ $movement->variant->product->name }}" class="object-cover w-full h-full">
                                @else
                                    <flux:icon.photo class="w-5 h-5 text-neutral-400" />
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-neutral-800 block">{{ $movement->variant->product->name ?? 'Produk Tidak Ditemukan' }}</span>
                                <span class="text-[11px] font-medium text-neutral-500">{{ $movement->variant->sku ?? '-' }}</span>
                            </div>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @php
                            $variant = match($movement->movement_type) {
                                'IN' => 'success',
                                'OUT', 'SALE' => 'danger',
                                'RETURN', 'ADJUSTMENT' => 'warning',
                                default => 'neutral'
                            };
                        @endphp
                        <x-ui.badge :variant="$variant">{{ $movement->movement_type }}</x-ui.badge>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="font-mono font-bold {{ $movement->quantity_change > 0 ? 'text-success-600' : 'text-danger-600' }}">
                            {{ $movement->quantity_change > 0 ? '+' : '' }}{{ number_format($movement->quantity_change, 0, ',', '.') }}
                        </span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-sm text-neutral-600">{{ $movement->note ?? '-' }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-sm text-neutral-600">{{ $movement->staff->full_name ?? '-' }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-sm text-neutral-600">{{ $movement->created_at ? \Carbon\Carbon::parse($movement->created_at)->format('d M Y, H:i') : '-' }}</span>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-3 border border-neutral-200 mx-auto">
                        <svg class="w-8 h-8 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line></svg>
                    </div>
                    <p class="font-medium text-neutral-500">Tidak ada riwayat pergerakan stok.</p>
                </x-slot:empty>
            @endforelse
            
            @if($movements->hasPages())
                <x-slot:pagination>
                    {{ $movements->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
    </x-ui.card>
</div>
