<div class="max-w-7xl mx-auto py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white font-black tracking-tight">Produk</flux:heading>
            <flux:subheading class="text-zinc-500 dark:text-zinc-400 mt-1">Kelola daftar produk dan varian yang tersedia di sistem kasir Anda.</flux:subheading>
        </div>
        <div>
            <flux:button variant="primary" icon="plus" href="{{ route('catalog.products.create') }}" wire:navigate class="rounded-xl shadow-lg shadow-blue-500/20 font-semibold px-5">
                Tambah Produk
            </flux:button>
        </div>
    </div>

    <div class="bg-white dark:bg-zinc-900 rounded-3xl shadow-sm border border-zinc-100 dark:border-zinc-800 overflow-hidden">
        <div class="p-6 border-b border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50">
            <div class="flex items-center w-full max-w-md">
                <flux:input wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama atau SKU produk..." class="w-full bg-white dark:bg-zinc-900 shadow-sm" />
            </div>
        </div>

        <div class="p-0">
            <flux:table class="w-full">
                <flux:table.columns>
                    <flux:table.column class="pl-6 text-zinc-500">Nama Produk</flux:table.column>
                    <flux:table.column class="text-zinc-500">SKU / Barcode</flux:table.column>
                    <flux:table.column class="text-zinc-500">Kategori</flux:table.column>
                    <flux:table.column class="text-zinc-500">Status</flux:table.column>
                    <flux:table.column class="text-zinc-500">Aksi</flux:table.column>
                </flux:table.columns>

                <flux:table.rows>
                    @forelse ($products as $product)
                        <flux:table.row :key="$product->id" class="hover:bg-zinc-50/50 dark:hover:bg-zinc-800/30 transition-colors">
                            <flux:table.cell class="pl-6">
                                <div class="flex items-center gap-4">
                                    <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex-shrink-0 flex items-center justify-center overflow-hidden border border-zinc-200 dark:border-zinc-700">
                                        @if($product->image_url)
                                            <img src="{{ Storage::url($product->image_url) }}" alt="{{ $product->name }}" class="object-cover w-full h-full">
                                        @else
                                            <flux:icon.photo class="w-6 h-6 text-zinc-400" />
                                        @endif
                                    </div>
                                    <div>
                                        <span class="font-bold text-zinc-900 dark:text-zinc-100 block">{{ $product->name }}</span>
                                        <span class="text-[11px] font-medium text-zinc-500 uppercase">{{ count($product->variants) }} Varian</span>
                                    </div>
                                </div>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="font-mono text-xs px-2 py-1 bg-zinc-100 dark:bg-zinc-800 rounded-md text-zinc-600 dark:text-zinc-300 font-semibold">{{ $product->sku ?? '-' }}</span>
                            </flux:table.cell>
                            <flux:table.cell>
                                <span class="inline-flex items-center gap-1.5 text-sm text-zinc-600 dark:text-zinc-400 font-medium">
                                    <flux:icon.tag class="w-4 h-4 text-zinc-400" />
                                    {{ $product->category ? $product->category->name : 'Tanpa Kategori' }}
                                </span>
                            </flux:table.cell>
                            <flux:table.cell>
                                @if($product->active)
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-emerald-50 text-emerald-600 dark:bg-emerald-500/10 dark:text-emerald-400">
                                        <div class="w-1.5 h-1.5 rounded-full bg-emerald-500"></div>
                                        Aktif
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-md text-xs font-bold bg-zinc-100 text-zinc-600 dark:bg-zinc-800 dark:text-zinc-400">
                                        <div class="w-1.5 h-1.5 rounded-full bg-zinc-400"></div>
                                        Nonaktif
                                    </span>
                                @endif
                            </flux:table.cell>
                            <flux:table.cell>
                                <div class="flex items-center gap-2">
                                    <flux:button size="sm" variant="ghost" icon="pencil-square" class="text-zinc-500 hover:text-blue-600 dark:hover:text-blue-400" href="{{ route('catalog.products.edit', $product->id) }}" wire:navigate />
                                    <flux:button size="sm" variant="ghost" icon="trash" class="text-zinc-500 hover:text-rose-600 dark:hover:text-rose-400" wire:click="deleteProduct('{{ $product->id }}')" wire:confirm="Yakin ingin menghapus produk ini beserta variannya?" />
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @empty
                        <flux:table.row>
                            <flux:table.cell colspan="5" class="text-center text-zinc-400 py-12">
                                <div class="flex flex-col items-center justify-center space-y-3">
                                    <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center">
                                        <flux:icon.cube class="w-8 h-8 text-zinc-400" />
                                    </div>
                                    <p class="font-medium text-zinc-500">Tidak ada produk ditemukan.</p>
                                </div>
                            </flux:table.cell>
                        </flux:table.row>
                    @endforelse
                </flux:table.rows>
            </flux:table>
        </div>

        @if($products->hasPages())
        <div class="p-6 border-t border-zinc-100 dark:border-zinc-800 bg-zinc-50/50 dark:bg-zinc-950/50">
            {{ $products->links() }}
        </div>
        @endif
    </div>
</div>
