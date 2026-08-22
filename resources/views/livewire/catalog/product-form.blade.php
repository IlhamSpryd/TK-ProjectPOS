<x-layouts.app :title="$productId ? 'Edit Produk' : 'Tambah Produk Baru'" :breadcrumbs="[['label' => 'Dashboard', 'route' => route('dashboard')], ['label' => 'Katalog Produk', 'route' => route('catalog.products')], ['label' => $productId ? 'Edit' : 'Tambah']]">
<div class="py-6">
    <x-slot:actions>
        <x-ui.button variant="ghost" href="{{ route('catalog.products') }}" wire:navigate class="px-5">Batal</x-ui.button>
        <x-ui.button variant="primary" wire:click="save" class="px-6 shadow-xs">
            Simpan Produk
        </x-ui.button>
    </x-slot:actions>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-2 space-y-6">
            <!-- Informasi Umum -->
            <x-ui.card class="relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 to-indigo-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center text-primary-600">
                        <flux:icon.information-circle class="w-5 h-5" />
                    </div>
                    <h2 class="text-h3 font-semibold text-neutral-800">Informasi Umum</h2>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <x-ui.input name="name" wire:model="name" label="Nama Produk" placeholder="Cth: Kopi Arabica" required class="bg-neutral-50" />
                        <flux:error name="name" />
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-ui.input name="sku" wire:model="sku" label="SKU Produk (Opsional)" placeholder="Cth: PRD-001" class="bg-neutral-50" />
                            <flux:error name="sku" />
                        </div>
                        <div>
                            <x-ui.select name="category_id" wire:model="category_id" label="Kategori">
                                <option value="">-- Tanpa Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-ui.select>
                            <flux:error name="category_id" />
                        </div>
                    </div>
                    
                    <div>
                        <x-ui.textarea name="description" wire:model="description" label="Deskripsi" placeholder="Penjelasan singkat tentang produk..." rows="4" class="bg-neutral-50 resize-none" />
                        <flux:error name="description" />
                    </div>
                </div>
            </x-ui.card>

            <!-- Varian / Harga -->
            <x-ui.card class="relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-success-500 to-teal-500"></div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-success-50 flex items-center justify-center text-success-600">
                            <flux:icon.currency-dollar class="w-5 h-5" />
                        </div>
                        <h2 class="text-h3 font-semibold text-neutral-800">Varian & Harga</h2>
                    </div>
                    <x-ui.button size="sm" variant="ghost" icon="plus" wire:click="addVariant" class="text-primary-600 bg-primary-50 hover:bg-primary-100 rounded-xl">
                        Tambah Varian
                    </x-ui.button>
                </div>
                
                <div class="space-y-4">
                    @foreach($variants as $index => $variant)
                        @if(!$variant['is_deleted'])
                        <div class="relative group p-5 border rounded-2xl border-neutral-200 bg-neutral-50 hover:border-primary-200 transition-colors duration-300 overflow-hidden">
                            
                            <div class="flex justify-between items-center mb-4">
                                <div class="flex items-center gap-2">
                                    <span class="flex items-center justify-center w-6 h-6 rounded-md bg-white text-xs font-semibold text-neutral-500 border border-neutral-200 shadow-xs">{{ $index + 1 }}</span>
                                    <span class="font-semibold text-body-sm text-neutral-800">Detail Varian</span>
                                </div>
                                @if(collect($variants)->where('is_deleted', false)->count() > 1)
                                    <x-ui.button size="sm" variant="ghost" icon="trash" class="text-neutral-400 hover:text-danger-600 hover:bg-danger-50 transition-colors" wire:click="removeVariant({{ $index }})" />
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.sku" wire:model="variants.{{ $index }}.sku" label="SKU Varian (Opsional)" class="bg-white" />
                                    <flux:error name="variants.{{ $index }}.sku" />
                                </div>
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.barcode" wire:model="variants.{{ $index }}.barcode" label="Barcode (Opsional)" class="bg-white" />
                                    <flux:error name="variants.{{ $index }}.barcode" />
                                </div>
                                
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.cost_price" wire:model="variants.{{ $index }}.cost_price" label="Harga Modal" type="number" min="0" step="0.01" class="bg-white" />
                                    <flux:error name="variants.{{ $index }}.cost_price" />
                                </div>
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.selling_price" wire:model="variants.{{ $index }}.selling_price" label="Harga Jual" type="number" min="0" step="0.01" required class="bg-white border-success-200 focus:ring-success-500" />
                                    <flux:error name="variants.{{ $index }}.selling_price" />
                                </div>
                            </div>
                            
                            <div class="mt-5 pt-4 border-t border-neutral-200 flex items-start">
                                <div class="flex items-center h-5">
                                    <input wire:model="variants.{{ $index }}.active" id="variants.{{ $index }}.active" type="checkbox" class="w-4 h-4 rounded-sm border-neutral-300 text-primary-600 focus:ring-primary-500">
                                </div>
                                <div class="ml-3">
                                    <label for="variants.{{ $index }}.active" class="text-body font-medium text-neutral-800">Varian Aktif</label>
                                </div>
                            </div>
                        </div>
                        @endif
                    @endforeach
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <!-- Foto Produk -->
            <x-ui.card class="relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-blue-500 to-cyan-500"></div>
                <div class="flex items-center gap-3 mb-4">
                    <div class="w-10 h-10 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600">
                        <flux:icon.photo class="w-5 h-5" />
                    </div>
                    <h2 class="text-h3 font-semibold text-neutral-800">Foto Produk</h2>
                </div>
                
                <div class="space-y-4">
                    @if ($image)
                        <div class="w-full h-48 bg-neutral-100 rounded-xl overflow-hidden border border-neutral-200">
                            <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                        </div>
                    @elseif ($existingImage)
                        <div class="w-full h-48 bg-neutral-100 rounded-xl overflow-hidden border border-neutral-200">
                            <img src="{{ Storage::url($existingImage) }}" class="w-full h-full object-cover">
                        </div>
                    @else
                        <div class="w-full h-48 bg-neutral-50 rounded-xl border border-dashed border-neutral-300 flex flex-col items-center justify-center text-neutral-400">
                            <flux:icon.photo class="w-10 h-10 mb-2 opacity-50" />
                            <span class="text-sm">Belum ada foto</span>
                        </div>
                    @endif

                    <div>
                        <input type="file" wire:model="image" id="product_image" class="block w-full text-sm text-neutral-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer border border-neutral-200 rounded-md bg-white">
                        <div wire:loading wire:target="image" class="text-xs text-primary-600 mt-2 font-medium">Mengunggah foto...</div>
                        <flux:error name="image" />
                    </div>
                </div>
            </x-ui.card>

            <!-- Pengaturan Lanjutan -->
            <x-ui.card class="relative overflow-hidden sticky top-24">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <flux:icon.cog-6-tooth class="w-5 h-5" />
                    </div>
                    <h2 class="text-h3 font-semibold text-neutral-800">Pengaturan Lanjutan</h2>
                </div>
                
                <div class="space-y-5">
                    <x-ui.input name="unit" wire:model="unit" label="Satuan" placeholder="Cth: pcs, kg, liter" class="bg-neutral-50" />
                    
                    <div class="space-y-4 pt-4 border-t border-neutral-100">
                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="track_stock" id="track_stock" type="checkbox" class="w-4 h-4 rounded-sm border-neutral-300 text-primary-600 focus:ring-primary-500">
                            </div>
                            <div class="ml-3">
                                <label for="track_stock" class="text-body font-medium text-neutral-800">Lacak Stok</label>
                                <p class="text-caption text-neutral-500">Pilih jika Anda ingin sistem mencatat pergerakan stok barang ini.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="is_service" id="is_service" type="checkbox" class="w-4 h-4 rounded-sm border-neutral-300 text-primary-600 focus:ring-primary-500">
                            </div>
                            <div class="ml-3">
                                <label for="is_service" class="text-body font-medium text-neutral-800">Barang Jasa</label>
                                <p class="text-caption text-neutral-500">Pilih jika ini adalah layanan/jasa yang tidak berbentuk fisik.</p>
                            </div>
                        </div>

                        <div class="flex items-start">
                            <div class="flex items-center h-5">
                                <input wire:model="active" id="active" type="checkbox" class="w-4 h-4 rounded-sm border-neutral-300 text-primary-600 focus:ring-primary-500">
                            </div>
                            <div class="ml-3">
                                <label for="active" class="text-body font-medium text-neutral-800">Produk Aktif</label>
                                <p class="text-caption text-neutral-500">Produk tidak akan muncul di sistem POS jika dimatikan.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>
    </div>
</div>
</x-layouts.app>

