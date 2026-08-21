<div class="max-w-7xl mx-auto py-6">
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
        <div>
            <flux:heading size="xl" class="text-zinc-900 dark:text-white font-black tracking-tight">{{ $productId ? 'Edit Produk' : 'Tambah Produk Baru' }}</flux:heading>
            <flux:subheading class="text-zinc-500 dark:text-zinc-400 mt-1">Masukkan informasi produk dan kelola variannya.</flux:subheading>
        </div>
        <div class="flex items-center gap-3">
            <flux:button variant="ghost" href="{{ route('catalog.products') }}" wire:navigate class="rounded-xl px-5">Batal</flux:button>
            <flux:button variant="primary" wire:click="save" class="rounded-xl shadow-lg shadow-blue-500/20 font-semibold px-6">
                Simpan Produk
            </flux:button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-2 space-y-6">
            <!-- Informasi Umum -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 md:p-8 shadow-sm border border-zinc-100 dark:border-zinc-800 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-indigo-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-500/10 flex items-center justify-center text-blue-600 dark:text-blue-400">
                        <flux:icon.information-circle class="w-5 h-5" />
                    </div>
                    <flux:heading size="lg" class="font-bold">Informasi Umum</flux:heading>
                </div>
                
                <div class="space-y-5">
                    <flux:input wire:model="name" label="Nama Produk" placeholder="Cth: Kopi Arabica" required class="bg-zinc-50 dark:bg-zinc-800/50" />
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <flux:input wire:model="sku" label="SKU Produk (Opsional)" placeholder="Cth: PRD-001" class="bg-zinc-50 dark:bg-zinc-800/50" />
                        <flux:select wire:model="category_id" label="Kategori" placeholder="Pilih Kategori...">
                            <flux:select.option value="">-- Tanpa Kategori --</flux:select.option>
                            @foreach($categories as $category)
                                <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                            @endforeach
                        </flux:select>
                    </div>
                    
                    <flux:textarea wire:model="description" label="Deskripsi" placeholder="Penjelasan singkat tentang produk..." rows="4" class="bg-zinc-50 dark:bg-zinc-800/50 resize-none" />
                </div>
            </div>

            <!-- Varian / Harga -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 md:p-8 shadow-sm border border-zinc-100 dark:border-zinc-800 relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-emerald-500 to-teal-500"></div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 flex items-center justify-center text-emerald-600 dark:text-emerald-400">
                            <flux:icon.currency-dollar class="w-5 h-5" />
                        </div>
                        <flux:heading size="lg" class="font-bold">Varian & Harga</flux:heading>
                    </div>
                    <flux:button size="sm" variant="ghost" icon="plus" wire:click="addVariant" class="text-blue-600 dark:text-blue-400 bg-blue-50 dark:bg-blue-500/10 hover:bg-blue-100 dark:hover:bg-blue-500/20 rounded-xl">
                        Tambah Varian
                    </flux:button>
                </div>
                
                <div class="space-y-4">
                    @foreach($variants as $index => $variant)
                        @if(!$variant['is_deleted'])
                        <div class="relative group p-5 border rounded-2xl border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-800/30 hover:border-blue-200 dark:hover:border-blue-500/30 transition-colors duration-300 overflow-hidden">
                            
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-md bg-white dark:bg-zinc-700 text-xs font-bold text-zinc-500 border border-zinc-200 dark:border-zinc-600 shadow-xs">{{ $index + 1 }}</span>
                                    <span class="font-bold text-sm text-zinc-900 dark:text-white">Detail Varian</span>
                                </div>
                                @if(collect($variants)->where('is_deleted', false)->count() > 1)
                                    <flux:button size="sm" variant="ghost" icon="trash" class="text-zinc-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-500/10 transition-colors" wire:click="removeVariant({{ $index }})" />
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <flux:input wire:model="variants.{{ $index }}.sku" label="SKU Varian (Opsional)" class="bg-white dark:bg-zinc-900" />
                                <flux:input wire:model="variants.{{ $index }}.barcode" label="Barcode (Opsional)" class="bg-white dark:bg-zinc-900" />
                                
                                <flux:input wire:model="variants.{{ $index }}.cost_price" label="Harga Modal" type="number" min="0" step="0.01" class="bg-white dark:bg-zinc-900" />
                                <div class="relative">
                                    <flux:input wire:model="variants.{{ $index }}.selling_price" label="Harga Jual" type="number" min="0" step="0.01" required class="bg-white dark:bg-zinc-900 border-emerald-200 focus:ring-emerald-500" />
                                </div>
                            </div>
                            
                            <div class="mt-5 pt-4 border-t border-zinc-200 dark:border-zinc-700/50">
                                <flux:checkbox wire:model="variants.{{ $index }}.active" label="Varian Aktif" />
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

        <div class="space-y-6">
            <!-- Pengaturan Lanjutan -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 md:p-8 shadow-sm border border-zinc-100 dark:border-zinc-800 relative overflow-hidden sticky top-24">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-500/10 flex items-center justify-center text-purple-600 dark:text-purple-400">
                        <flux:icon.cog-6-tooth class="w-5 h-5" />
                    </div>
                    <flux:heading size="lg" class="font-bold">Pengaturan Lanjutan</flux:heading>
                </div>
                
                <div class="space-y-5">
                    <flux:input wire:model="unit" label="Satuan" placeholder="Cth: pcs, kg, liter" class="bg-zinc-50 dark:bg-zinc-800/50" />
                    
                    <div class="space-y-4 pt-4 border-t border-zinc-100 dark:border-zinc-800">
                        <flux:checkbox wire:model="track_stock" label="Lacak Stok" description="Pilih jika Anda ingin sistem mencatat pergerakan stok barang ini." />
                        <flux:checkbox wire:model="is_service" label="Barang Jasa" description="Pilih jika ini adalah layanan/jasa yang tidak berbentuk fisik." />
                        <flux:checkbox wire:model="active" label="Produk Aktif" description="Produk tidak akan muncul di sistem POS jika dimatikan." />
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
