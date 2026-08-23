<div x-on:trigger-save-product.window="$wire.save()">

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="col-span-2 space-y-6">
            <!-- Informasi Umum -->
            <x-ui.card class="relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary-500 to-indigo-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-primary-50 flex items-center justify-center text-primary-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h2 class="text-h3 font-semibold text-neutral-800">Informasi Umum</h2>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <x-ui.input name="name" wire:model="name" label="Nama Produk" placeholder="Cth: Kopi Arabica" required class="bg-neutral-50" />
                        @error('name') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div>
                            <x-ui.input name="sku" wire:model="sku" label="SKU Produk (Opsional)" placeholder="Cth: PRD-001" class="bg-neutral-50" />
                            @error('sku') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                        <div>
                            <x-ui.select name="category_id" wire:model="category_id" label="Kategori">
                                <option value="">-- Tanpa Kategori --</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </x-ui.select>
                            @error('category_id') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    
                    <div>
                        <x-ui.textarea name="description" wire:model="description" label="Deskripsi" placeholder="Penjelasan singkat tentang produk..." rows="4" class="bg-neutral-50 resize-none" />
                        @error('description') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </x-ui.card>

            <!-- Varian / Harga -->
            <x-ui.card class="relative overflow-hidden">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-success-500 to-teal-500"></div>
                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl bg-success-50 flex items-center justify-center text-success-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                                    <button type="button" aria-label="Hapus Varian" class="text-neutral-400 hover:text-danger-600 hover:bg-danger-50 transition-colors p-1.5 rounded-md" wire:click="removeVariant({{ $index }})">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    </button>
                                @endif
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.sku" wire:model="variants.{{ $index }}.sku" label="SKU Varian (Opsional)" class="bg-white" />
                                    @error('variants.'.$index.'.sku') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.barcode" wire:model="variants.{{ $index }}.barcode" label="Barcode (Opsional)" class="bg-white" />
                                    @error('variants.'.$index.'.barcode') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.cost_price" wire:model="variants.{{ $index }}.cost_price" label="Harga Modal" type="number" min="0" step="0.01" class="bg-white" />
                                    @error('variants.'.$index.'.cost_price') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                                <div>
                                    <x-ui.input name="variants.{{ $index }}.selling_price" wire:model="variants.{{ $index }}.selling_price" label="Harga Jual" type="number" min="0" step="0.01" required class="bg-white border-success-200 focus:ring-success-500" />
                                    @error('variants.'.$index.'.selling_price') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                                </div>
                            </div>
                            
                            <!-- Variant Attributes -->
                            <div class="mt-4 border-t border-neutral-100 pt-3">
                                <label class="block text-[12px] font-medium text-neutral-700 mb-2">Atribut Varian (Opsional)</label>
                                <div class="space-y-2">
                                    @foreach($variant['attributes_list'] ?? [] as $attrIndex => $attr)
                                        <div class="flex items-center gap-2">
                                            <input type="text" wire:model.blur="variants.{{ $index }}.attributes_list.{{ $attrIndex }}.key" placeholder="Key (Cth: Warna)" class="w-1/2 px-2 py-1 bg-white border border-neutral-200 rounded text-[12px] focus:outline-none focus:border-primary-400">
                                            <input type="text" wire:model.blur="variants.{{ $index }}.attributes_list.{{ $attrIndex }}.value" placeholder="Value (Cth: Merah)" class="w-1/2 px-2 py-1 bg-white border border-neutral-200 rounded text-[12px] focus:outline-none focus:border-primary-400">
                                            <button type="button" wire:click="removeVariantAttribute({{ $index }}, {{ $attrIndex }})" class="text-neutral-400 hover:text-danger-500 transition-colors p-1">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    @endforeach
                                </div>
                                <button type="button" wire:click="addVariantAttribute({{ $index }})" class="mt-2 text-[11px] font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                    Tambah Atribut
                                </button>
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
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <h2 class="text-h3 font-semibold text-neutral-800">Foto Produk</h2>
                </div>
                
                <div class="space-y-4" x-data="{ isDropping: false }" x-on:drop.prevent="isDropping = false" x-on:dragover.prevent="isDropping = true" x-on:dragleave.prevent="isDropping = false">
                    @if ($image || $existingImage)
                        <div class="relative w-full h-48 bg-neutral-100 rounded-xl overflow-hidden border border-neutral-200 group">
                            @if($image)
                                <img src="{{ $image->temporaryUrl() }}" class="w-full h-full object-cover">
                            @else
                                <img src="{{ Storage::url($existingImage) }}" class="w-full h-full object-cover">
                            @endif
                            <div class="absolute inset-0 bg-neutral-900/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-3">
                                <label for="product_image" tabindex="0" aria-label="Ganti Foto Produk" class="cursor-pointer bg-white/90 text-neutral-800 hover:bg-white px-3 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center gap-2 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-500 focus-visible:ring-offset-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg> Ganti
                                </label>
                                <button type="button" aria-label="Hapus Foto Produk" wire:click="$set('image', null); $set('existingImage', null)" class="bg-danger-50 text-danger-600 hover:bg-danger-100 px-3 py-2 rounded-lg text-sm font-medium shadow-sm transition-colors flex items-center gap-2 focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-danger-500 focus-visible:ring-offset-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg> Hapus
                                </button>
                            </div>
                            <div wire:loading wire:target="image" class="absolute inset-0 bg-white/90 flex flex-col items-center justify-center z-10">
                                <svg class="animate-spin h-8 w-8 text-primary-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-sm font-medium text-primary-700">Mengunggah...</span>
                            </div>
                        </div>
                    @else
                        <label for="product_image" 
                               class="relative flex flex-col items-center justify-center w-full h-48 border-2 border-dashed rounded-xl cursor-pointer transition-colors"
                               :class="isDropping ? 'border-primary-500 bg-primary-50' : 'border-neutral-300 bg-neutral-50 hover:bg-neutral-100 hover:border-primary-400'"
                               x-on:drop="document.getElementById('product_image').files = $event.dataTransfer.files; document.getElementById('product_image').dispatchEvent(new Event('change', { bubbles: true }));">
                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                <svg class="w-10 h-10 mb-3" x-bind:class="isDropping ? 'text-primary-500' : 'text-neutral-400'" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                <p class="mb-1 text-sm text-neutral-600"><span class="font-semibold text-primary-600">Klik untuk memilih</span> atau seret & lepas gambar</p>
                                <p class="text-xs text-neutral-500">PNG, JPG, JPEG (Maks. 2MB)</p>
                            </div>
                            <div wire:loading wire:target="image" class="absolute inset-0 bg-white/90 rounded-xl flex flex-col items-center justify-center z-10">
                                <svg class="animate-spin h-8 w-8 text-primary-600 mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span class="text-sm font-medium text-primary-700">Mengunggah...</span>
                            </div>
                        </label>
                    @endif

                    <div class="hidden">
                        <input type="file" wire:model="image" id="product_image" accept="image/png, image/jpeg, image/jpg" class="hidden">
                    </div>
                    @error('image') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </x-ui.card>

            <!-- Pengaturan Lanjutan -->
            <x-ui.card class="relative overflow-hidden sticky top-24">
                <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-purple-500 to-pink-500"></div>
                <div class="flex items-center gap-3 mb-6">
                    <div class="w-10 h-10 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <h2 class="text-h3 font-semibold text-neutral-800">Pengaturan Lanjutan</h2>
                </div>
                
                <div class="space-y-5">
                    <div>
                        <x-ui.input name="unit" wire:model="unit" label="Satuan" placeholder="Cth: pcs, kg, liter" class="bg-neutral-50" />
                        @error('unit') <span class="text-[11px] text-danger-500 mt-1 block">{{ $message }}</span> @enderror
                    </div>

                    <!-- Atribut Tambahan -->
                    <div class="mt-4">
                        <label class="block text-[13px] font-medium text-neutral-800 mb-2">Atribut Tambahan (Opsional)</label>
                        <div class="space-y-2">
                            @foreach($product_attributes_list as $index => $attr)
                                <div class="flex items-center gap-2">
                                    <input type="text" wire:model.blur="product_attributes_list.{{ $index }}.key" placeholder="Key (Cth: Merek)" class="w-1/2 px-2.5 py-1.5 bg-neutral-50 border border-neutral-200 rounded-md text-[13px] focus:outline-none focus:border-primary-400">
                                    <input type="text" wire:model.blur="product_attributes_list.{{ $index }}.value" placeholder="Value (Cth: Nestle)" class="w-1/2 px-2.5 py-1.5 bg-neutral-50 border border-neutral-200 rounded-md text-[13px] focus:outline-none focus:border-primary-400">
                                    <button type="button" wire:click="removeProductAttribute({{ $index }})" class="p-1.5 text-neutral-400 hover:text-danger-500 transition-colors bg-white border border-neutral-200 rounded-md">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </button>
                                </div>
                            @endforeach
                        </div>
                        <button type="button" wire:click="addProductAttribute" class="mt-2 text-[12px] font-medium text-primary-600 hover:text-primary-700 flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                            Tambah Atribut
                        </button>
                    </div>
                    
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
