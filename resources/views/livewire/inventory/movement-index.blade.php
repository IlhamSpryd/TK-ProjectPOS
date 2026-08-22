<div>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50 flex items-center justify-between gap-4">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama produk..." class="w-full bg-white shadow-none" />
            </div>
            <div>
                <select wire:model.live="typeFilter" class="bg-white border border-neutral-200 text-neutral-700 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                    <option value="">Semua Tipe</option>
                    <option value="adjustment_in">Barang Masuk (Adjustment IN)</option>
                    <option value="adjustment_out">Barang Keluar (Adjustment OUT)</option>
                    <option value="write_off">Pemusnahan / Hilang (Write Off)</option>
                    <option value="sale">Penjualan (Sale)</option>
                    <option value="sale_return">Retur Penjualan (Sale Return)</option>
                    <option value="po_receive">Penerimaan PO (PO Receive)</option>
                </select>
            </div>
        </div>

        <div class="hidden md:block">
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
                                'adjustment_in', 'po_receive', 'sale_return' => 'success',
                                'adjustment_out', 'write_off', 'sale' => 'danger',
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
                    <x-ui.empty-state icon="arrows-right-left" title="Tidak ada pergerakan" description="Belum ada riwayat pergerakan stok." />
                </x-slot:empty>
            @endforelse
            
            @if($movements->hasPages())
                <x-slot:pagination>
                    {{ $movements->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
        </div>

        <div class="block md:hidden border-t border-neutral-200 divide-y divide-neutral-100">
            @forelse ($movements as $movement)
                <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                    <div class="flex items-center justify-between">
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
                        <div class="flex flex-col items-end">
                            <span class="font-mono font-bold {{ $movement->quantity_change > 0 ? 'text-success-600' : 'text-danger-600' }}">
                                {{ $movement->quantity_change > 0 ? '+' : '' }}{{ number_format($movement->quantity_change, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm mt-1">
                        <div>
                            @php
                                $variant = match($movement->movement_type) {
                                    'adjustment_in', 'po_receive', 'sale_return' => 'success',
                                    'adjustment_out', 'write_off', 'sale' => 'danger',
                                    default => 'neutral'
                                };
                            @endphp
                            <x-ui.badge :variant="$variant">{{ $movement->movement_type }}</x-ui.badge>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-neutral-500">{{ $movement->created_at ? \Carbon\Carbon::parse($movement->created_at)->format('d M Y, H:i') : '-' }}</span>
                        </div>
                    </div>
                    <div class="text-sm mt-1 flex justify-between items-center text-neutral-500">
                        <span>Oleh: <span class="text-neutral-700">{{ $movement->staff->full_name ?? '-' }}</span></span>
                        @if($movement->note)
                            <span class="truncate max-w-[150px]">{{ $movement->note }}</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="p-8">
                    <x-ui.empty-state icon="arrows-right-left" title="Tidak ada pergerakan" description="Belum ada riwayat pergerakan stok." />
                </div>
            @endforelse
            @if($movements->hasPages())
                <div class="p-4 border-t border-neutral-200">
                    {{ $movements->links() }}
                </div>
            @endif
        </div>
    </x-ui.card>
</div>
