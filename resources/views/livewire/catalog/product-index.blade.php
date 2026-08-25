<div>
    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div
            class="p-4 border-b border-neutral-200 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="relative flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari nama atau SKU produk..." class="w-full bg-white shadow-none pr-9" />
                @if ($search)
                    <button type="button" wire:click="$set('search', '')" aria-label="Hapus pencarian"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-full text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-500">
                        <flux:icon.x-mark class="w-4 h-4" />
                    </button>
                @endif
            </div>
            <p class="text-caption text-neutral-500 shrink-0" aria-live="polite">
                {{ $products->total() }} produk{{ $search ? ' ditemukan' : '' }}
            </p>
        </div>

        <div wire:loading.class="opacity-60" wire:target="search" class="transition-opacity duration-150">
            <div class="hidden md:block">
                <x-ui.table>
                    <x-slot:head>
                        <x-ui.table.th class="pl-6">Nama Produk</x-ui.table.th>
                        <x-ui.table.th>SKU / Barcode</x-ui.table.th>
                        <x-ui.table.th>Kategori</x-ui.table.th>
                        <x-ui.table.th>Harga</x-ui.table.th>
                        <x-ui.table.th>Status</x-ui.table.th>
                        <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
                    </x-slot:head>

                    @forelse ($products as $product)
                        @php
                            $sellingPrices = collect($product->variants)
                                ->pluck('selling_price')
                                ->filter(fn($p) => $p !== null);
                            $minPrice = $sellingPrices->min();
                            $maxPrice = $sellingPrices->max();
                        @endphp
                        <x-ui.table.tr :key="$product->id">
                            <x-ui.table.td class="pl-6">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="w-12 h-12 rounded-xl bg-neutral-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-neutral-200">
                                        @if ($product->image_url)
                                            <img src="{{ Storage::url($product->image_url) }}"
                                                alt="{{ $product->name }}" class="object-cover w-full h-full">
                                        @else
                                            <span
                                                class="text-body-sm font-semibold text-neutral-400">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <span
                                            class="font-medium text-neutral-800 block truncate">{{ $product->name }}</span>
                                        <span
                                            class="text-[11px] font-medium text-neutral-500 uppercase">{{ count($product->variants) }}
                                            Varian</span>
                                    </div>
                                </div>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                <span
                                    class="font-mono text-xs px-2 py-1 bg-neutral-100 rounded-md text-neutral-600 font-medium">{{ $product->sku ?? '-' }}</span>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                <span
                                    class="inline-flex items-center gap-1.5 text-body-sm text-neutral-600 font-medium">
                                    <flux:icon.tag class="w-4 h-4 text-neutral-400" />
                                    {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                                </span>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                @if ($sellingPrices->isEmpty())
                                    <span class="text-body-sm text-neutral-400">—</span>
                                @else
                                    <span class="text-body-sm font-semibold text-neutral-800">Rp
                                        {{ number_format($minPrice, 0, ',', '.') }}</span>
                                    @if ($minPrice != $maxPrice)
                                        <span class="text-caption text-neutral-400 block">s/d Rp
                                            {{ number_format($maxPrice, 0, ',', '.') }}</span>
                                    @endif
                                @endif
                            </x-ui.table.td>
                            <x-ui.table.td>
                                @if ($product->active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                                @endif
                            </x-ui.table.td>
                            <x-ui.table.td class="text-right pr-6">
                                <div class="flex items-center justify-end gap-1">
                                    <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                        href="{{ route('catalog.products.edit', $product->id) }}" wire:navigate
                                        class="text-neutral-500 hover:text-primary-600"
                                        aria-label="Edit {{ $product->name }}" />
                                    <x-ui.button size="sm" variant="ghost" icon="trash"
                                        wire:click="deleteProduct('{{ $product->id }}')"
                                        wire:confirm="Yakin ingin menghapus produk &quot;{{ $product->name }}&quot; beserta variannya?"
                                        class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50"
                                        aria-label="Hapus {{ $product->name }}" />
                                </div>
                            </x-ui.table.td>
                        </x-ui.table.tr>
                    @empty
                        <x-slot:empty>
                            @if ($search)
                                <x-ui.empty-state icon="magnifying-glass" title="Tidak ditemukan"
                                    description="Coba kata kunci lain atau periksa ejaan pencarian." />
                            @else
                                <x-ui.empty-state icon="cube" title="Belum ada produk"
                                    description="Tambahkan produk pertama Anda untuk mulai berjualan." />
                            @endif
                        </x-slot:empty>
                    @endforelse

                    @if ($products->hasPages())
                        <x-slot:pagination>
                            {{ $products->links() }}
                        </x-slot:pagination>
                    @endif
                </x-ui.table>
            </div>

            <div class="block md:hidden border-t border-neutral-200 divide-y divide-neutral-100">
                @forelse ($products as $product)
                    @php
                        $sellingPrices = collect($product->variants)
                            ->pluck('selling_price')
                            ->filter(fn($p) => $p !== null);
                        $minPrice = $sellingPrices->min();
                        $maxPrice = $sellingPrices->max();
                    @endphp
                    <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-12 h-12 rounded-xl bg-neutral-100 flex-shrink-0 flex items-center justify-center overflow-hidden border border-neutral-200">
                                    @if ($product->image_url)
                                        <img src="{{ Storage::url($product->image_url) }}" alt="{{ $product->name }}"
                                            class="object-cover w-full h-full">
                                    @else
                                        <span
                                            class="text-body-sm font-semibold text-neutral-400">{{ strtoupper(mb_substr($product->name, 0, 1)) }}</span>
                                    @endif
                                </div>
                                <div class="min-w-0">
                                    <span
                                        class="font-medium text-neutral-800 block truncate">{{ $product->name }}</span>
                                    <span
                                        class="text-[11px] font-medium text-neutral-500 uppercase">{{ count($product->variants) }}
                                        Varian</span>
                                </div>
                            </div>
                            <div>
                                @if ($product->active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                                @endif
                            </div>
                        </div>
                        <div class="grid grid-cols-2 gap-2 text-sm">
                            <div>
                                <span class="text-neutral-500 block text-xs">SKU</span>
                                <span
                                    class="font-mono text-xs px-2 py-1 bg-neutral-100 rounded-md text-neutral-600 font-medium inline-block">{{ $product->sku ?? '-' }}</span>
                            </div>
                            <div>
                                <span class="text-neutral-500 block text-xs">Kategori</span>
                                <span class="inline-flex items-center gap-1 text-body-sm text-neutral-600 font-medium">
                                    <flux:icon.tag class="w-3 h-3 text-neutral-400" />
                                    {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                                </span>
                            </div>
                            <div class="col-span-2">
                                <span class="text-neutral-500 block text-xs">Harga</span>
                                @if ($sellingPrices->isEmpty())
                                    <span class="text-body-sm text-neutral-400">—</span>
                                @else
                                    <span class="text-body-sm font-semibold text-neutral-800">Rp
                                        {{ number_format($minPrice, 0, ',', '.') }}{{ $minPrice != $maxPrice ? ' – Rp ' . number_format($maxPrice, 0, ',', '.') : '' }}</span>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                href="{{ route('catalog.products.edit', $product->id) }}" wire:navigate
                                class="text-neutral-500 hover:text-primary-600"
                                aria-label="Edit {{ $product->name }}" />
                            <x-ui.button size="sm" variant="ghost" icon="trash"
                                wire:click="deleteProduct('{{ $product->id }}')"
                                wire:confirm="Yakin ingin menghapus produk &quot;{{ $product->name }}&quot; beserta variannya?"
                                class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50"
                                aria-label="Hapus {{ $product->name }}" />
                        </div>
                    </div>
                @empty
                    <div class="p-8">
                        @if ($search)
                            <x-ui.empty-state icon="magnifying-glass" title="Tidak ditemukan"
                                description="Coba kata kunci lain atau periksa ejaan pencarian." />
                        @else
                            <x-ui.empty-state icon="archive-box" title="Belum ada produk"
                                description="Tambahkan produk pertama Anda untuk mulai berjualan." />
                        @endif
                    </div>
                @endforelse
                @if ($products->hasPages())
                    <div class="p-4 border-t border-neutral-200">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-ui.card>
</div>
