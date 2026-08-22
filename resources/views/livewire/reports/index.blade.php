<div class="py-6">

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
        <x-ui.card class="p-6 border-neutral-200">
            <h3 class="text-body font-medium text-neutral-500 mb-2">Total Pendapatan</h3>
            <div class="text-3xl font-bold text-neutral-800 tracking-tight">Rp {{ number_format($totalRevenue, 0, ',', '.') }}</div>
        </x-ui.card>
        
        <x-ui.card class="p-6 border-neutral-200">
            <h3 class="text-body font-medium text-neutral-500 mb-2">Jumlah Transaksi</h3>
            <div class="text-3xl font-bold text-neutral-800 tracking-tight">{{ number_format($totalTransactions) }}</div>
        </x-ui.card>

        <x-ui.card class="p-6 border-neutral-200">
            <h3 class="text-body font-medium text-neutral-500 mb-2">Item Terjual</h3>
            <div class="text-3xl font-bold text-neutral-800 tracking-tight">{{ number_format($totalItemsSold) }}</div>
        </x-ui.card>
    </div>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50 flex justify-between items-center">
            <h3 class="font-semibold text-neutral-800">Riwayat Transaksi</h3>
        </div>

        <div class="hidden md:block">
            <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">No. Invoice</x-ui.table.th>
                <x-ui.table.th>Waktu</x-ui.table.th>
                <x-ui.table.th>Pelanggan</x-ui.table.th>
                <x-ui.table.th>Total</x-ui.table.th>
                <x-ui.table.th>Status</x-ui.table.th>
                <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($sales as $sale)
                <x-ui.table.tr :key="$sale->id">
                    <x-ui.table.td class="pl-6">
                        <span class="font-mono text-xs px-2 py-1 bg-neutral-100 rounded-md text-neutral-600 font-semibold">{{ $sale->sale_number ?? $sale->id }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-body-sm text-neutral-600">{{ $sale->created_at->format('d M Y, H:i') }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <flux:icon.user class="w-3 h-3" />
                            </div>
                            <span class="text-body-sm font-medium text-neutral-700">{{ $sale->customer ? $sale->customer->name : 'Umum' }}</span>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="text-body-sm font-semibold text-neutral-800">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @if($sale->status === 'completed')
                            <x-ui.badge variant="success">Selesai</x-ui.badge>
                        @elseif($sale->status === 'pending')
                            <x-ui.badge variant="warning">Tertunda</x-ui.badge>
                        @else
                            <x-ui.badge variant="danger">Batal</x-ui.badge>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-right pr-6">
                        <x-ui.button size="sm" variant="ghost" class="text-neutral-500 hover:text-primary-600" aria-label="Detail Transaksi">Detail</x-ui.button>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <x-ui.empty-state icon="document-text" title="Tidak ada transaksi" description="Belum ada transaksi pada periode ini." />
                </x-slot:empty>
            @endforelse
        </x-ui.table>
        </div>

        <div class="block md:hidden border-t border-neutral-200 divide-y divide-neutral-100">
            @forelse ($sales as $sale)
                <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <span class="font-mono text-xs px-2 py-1 bg-neutral-100 rounded-md text-neutral-600 font-semibold">{{ $sale->sale_number ?? $sale->id }}</span>
                            <span class="text-xs text-neutral-500">{{ $sale->created_at->format('d M Y, H:i') }}</span>
                        </div>
                        <div>
                            @if($sale->status === 'completed')
                                <x-ui.badge variant="success">Selesai</x-ui.badge>
                            @elseif($sale->status === 'pending')
                                <x-ui.badge variant="warning">Tertunda</x-ui.badge>
                            @else
                                <x-ui.badge variant="danger">Batal</x-ui.badge>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center justify-between mt-1">
                        <div class="flex items-center gap-2">
                            <div class="w-6 h-6 rounded-full bg-neutral-100 flex items-center justify-center text-neutral-500">
                                <flux:icon.user class="w-3 h-3" />
                            </div>
                            <span class="text-body-sm font-medium text-neutral-700">{{ $sale->customer ? $sale->customer->name : 'Umum' }}</span>
                        </div>
                        <span class="text-body-sm font-semibold text-neutral-800">Rp {{ number_format($sale->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                        <x-ui.button size="sm" variant="ghost" class="text-neutral-500 hover:text-primary-600" aria-label="Detail Transaksi">Detail</x-ui.button>
                    </div>
                </div>
            @empty
                <div class="p-8">
                    <x-ui.empty-state icon="document-text" title="Tidak ada transaksi" description="Belum ada transaksi pada periode ini." />
                </div>
            @endforelse
        </div>
        
        @if($sales->hasPages())
            <div class="p-4 border-t border-neutral-200">
                {{ $sales->links() }}
            </div>
        @endif
    </x-ui.card>
</div>
