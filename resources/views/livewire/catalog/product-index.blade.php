<div>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama atau SKU produk..." class="w-full bg-white shadow-none" />
            </div>
        </div>

        <div class="hidden md:block">
            <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">Nama Produk</x-ui.table.th>
                <x-ui.table.th>SKU / Barcode</x-ui.table.th>
                <x-ui.table.th>Kategori</x-ui.table.th>
                <x-ui.table.th>Status</x-ui.table.th>
                <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($products as $product)
                <x-ui.table.tr :key="$product->id">
                    <x-ui.table.td class="pl-6">
                        <div class="flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-neutral-200">
                                @if($product->image_url)
                                    <img src="{{ Storage::url($product->image_url) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                @else
                                    <flux:icon.photo class="w-6 h-6 text-neutral-400" />
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-neutral-800 block">{{ $product->name }}</span>
                                <span class="text-[11px] font-medium text-neutral-500 uppercase">{{ count($product->variants) }} Varian</span>
                            </div>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="font-mono text-xs px-2 py-1 bg-neutral-100 rounded-md text-neutral-600 font-medium">{{ $product->sku ?? '-' }}</span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        <span class="inline-flex items-center gap-1.5 text-body-sm text-neutral-600 font-medium">
                            <flux:icon.tag class="w-4 h-4 text-neutral-400" />
                            {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                        </span>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @if($product->active)
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('catalog.products.edit', $product->id) }}" wire:navigate class="text-neutral-500 hover:text-primary-600" aria-label="Edit Produk" />
                            <x-ui.button size="sm" variant="ghost" icon="trash" wire:click="deleteProduct('{{ $product->id }}')" wire:confirm="Yakin ingin menghapus produk ini beserta variannya?" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" aria-label="Hapus Produk" />
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <x-ui.empty-state icon="cube" title="Tidak ada produk" description="Coba ubah kata kunci pencarian atau tambah produk baru." />
                </x-slot:empty>
            @endforelse
            
            @if($products->hasPages())
                <x-slot:pagination>
                    {{ $products->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
        </div>

        <div class="block md:hidden border-t border-neutral-200 divide-y divide-neutral-100">
            @forelse ($products as $product)
                <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="w-12 h-12 rounded-xl bg-neutral-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-neutral-200">
                                @if($product->image_url)
                                    <img src="{{ Storage::url($product->image_url) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                @else
                                    <flux:icon.photo class="w-6 h-6 text-neutral-400" />
                                @endif
                            </div>
                            <div>
                                <span class="font-medium text-neutral-800 block">{{ $product->name }}</span>
                                <span class="text-[11px] font-medium text-neutral-500 uppercase">{{ count($product->variants) }} Varian</span>
                            </div>
                        </div>
                        <div>
                            @if($product->active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                            @endif
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-2 text-sm">
                        <div>
                            <span class="text-neutral-500 block text-xs">SKU</span>
                            <span class="font-mono text-xs px-2 py-1 bg-neutral-100 rounded-md text-neutral-600 font-medium inline-block">{{ $product->sku ?? '-' }}</span>
                        </div>
                        <div>
                            <span class="text-neutral-500 block text-xs">Kategori</span>
                            <span class="inline-flex items-center gap-1 text-body-sm text-neutral-600 font-medium">
                                <flux:icon.tag class="w-3 h-3 text-neutral-400" />
                                {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                            </span>
                        </div>
                    </div>
                    <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                        <x-ui.button size="sm" variant="ghost" icon="pencil-square" href="{{ route('catalog.products.edit', $product->id) }}" wire:navigate class="text-neutral-500 hover:text-primary-600" aria-label="Edit Produk" />
                        <x-ui.button size="sm" variant="ghost" icon="trash" wire:click="deleteProduct('{{ $product->id }}')" wire:confirm="Yakin ingin menghapus produk ini beserta variannya?" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" aria-label="Hapus Produk" />
                    </div>
                </div>
            @empty
                <div class="p-8">
                    <x-ui.empty-state icon="archive-box" title="Tidak ada produk" description="Belum ada data produk atau tidak ada yang sesuai dengan pencarian." />
                </div>
            @endforelse
            @if($products->hasPages())
                <div class="p-4 border-t border-neutral-200">
                    {{ $products->links() }}
                </div>
            @endif
        </div>
    </x-ui.card>
</div>
