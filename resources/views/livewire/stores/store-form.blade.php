<div>
    <div class="px-6 py-4 border-b border-neutral-200">
        <h3 class="text-h3 font-semibold text-neutral-800">
            {{ $storeId ? 'Edit Cabang' : 'Tambah Cabang Baru' }}
        </h3>
        <p class="text-body-sm text-neutral-500 mt-1">
            {{ $storeId ? 'Ubah informasi dan pengaturan cabang di bawah ini.' : 'Isi form di bawah untuk mendaftarkan cabang baru.' }}
        </p>
    </div>

    <form wire:submit="save">
        <div class="px-6 py-6 space-y-6">
            <div class="space-y-4">
                <h4 class="text-body font-medium text-neutral-800 border-b border-neutral-200 pb-2">Informasi Umum</h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.input 
                        name="name" 
                        label="Nama Cabang" 
                        wire:model="name" 
                        placeholder="Misal: Cabang Jakarta Pusat"
                        required
                    />
                    <x-ui.input 
                        name="business_type" 
                        label="Tipe Bisnis" 
                        wire:model="business_type" 
                        placeholder="Misal: Retail, F&B"
                    />
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.input 
                        name="phone" 
                        label="Nomor Telepon" 
                        wire:model="phone" 
                        placeholder="Misal: 021-xxxxxx"
                    />
                    <x-ui.input 
                        name="email" 
                        type="email"
                        label="Email Cabang" 
                        wire:model="email" 
                        placeholder="Misal: jktpusat@toko.com"
                    />
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="text-body font-medium text-neutral-800 border-b border-neutral-200 pb-2">Lokasi</h4>
                <div class="w-full">
                    <label class="block text-body-sm font-medium text-neutral-700 mb-1.5">Alamat Lengkap</label>
                    <textarea 
                        wire:model="address" 
                        rows="2"
                        class="block w-full bg-white rounded-lg border border-neutral-300 focus:border-primary-500 focus:ring-primary-100 text-body text-neutral-900 placeholder-neutral-400 focus:outline-hidden focus:ring-2 transition-shadow px-3.5 py-2.5"
                        placeholder="Alamat cabang"
                    ></textarea>
                    @error('address') <span class="text-danger-600 text-caption mt-1">{{ $message }}</span> @enderror
                </div>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <x-ui.input 
                        name="city" 
                        label="Kota/Kabupaten" 
                        wire:model="city" 
                        placeholder="Misal: Jakarta Pusat"
                    />
                    <x-ui.input 
                        name="province" 
                        label="Provinsi" 
                        wire:model="province" 
                        placeholder="Misal: DKI Jakarta"
                    />
                </div>
            </div>

            <div class="space-y-4">
                <h4 class="text-body font-medium text-neutral-800 border-b border-neutral-200 pb-2">Pengaturan & Pajak</h4>
                
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="w-full">
                        <label class="block text-body-sm font-medium text-neutral-700 mb-1.5">Mata Uang</label>
                        <select wire:model="currency" class="block w-full h-10 bg-white rounded-lg border border-neutral-300 focus:border-primary-500 focus:ring-primary-100 text-body text-neutral-900 px-3.5">
                            <option value="IDR">IDR - Rupiah</option>
                            <option value="USD">USD - US Dollar</option>
                        </select>
                        @error('currency') <span class="text-danger-600 text-caption mt-1">{{ $message }}</span> @enderror
                    </div>
                    
                    <div class="w-full">
                        <label class="block text-body-sm font-medium text-neutral-700 mb-1.5">Zona Waktu</label>
                        <select wire:model="timezone" class="block w-full h-10 bg-white rounded-lg border border-neutral-300 focus:border-primary-500 focus:ring-primary-100 text-body text-neutral-900 px-3.5">
                            <option value="Asia/Jakarta">Asia/Jakarta (WIB)</option>
                            <option value="Asia/Makassar">Asia/Makassar (WITA)</option>
                            <option value="Asia/Jayapura">Asia/Jayapura (WIT)</option>
                        </select>
                        @error('timezone') <span class="text-danger-600 text-caption mt-1">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="w-full">
                    <label class="block text-body-sm font-medium text-neutral-700 mb-1.5">Kategori Pajak Default</label>
                    <select wire:model="default_tax_category_id" class="block w-full h-10 bg-white rounded-lg border border-neutral-300 focus:border-primary-500 focus:ring-primary-100 text-body text-neutral-900 px-3.5">
                        <option value="">-- Tidak ada (Bebas Pajak) --</option>
                        @foreach($taxCategories as $tax)
                            <option value="{{ $tax->id }}">{{ $tax->name }} ({{ floatval($tax->rate) }}%)</option>
                        @endforeach
                    </select>
                    @error('default_tax_category_id') <span class="text-danger-600 text-caption mt-1">{{ $message }}</span> @enderror
                </div>

                <div class="flex flex-col gap-3 pt-2">
                    <label class="flex items-start cursor-pointer group">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" wire:model.live="is_pkp" class="w-4 h-4 text-primary-600 bg-neutral-100 border-neutral-300 rounded focus:ring-primary-500 focus:ring-2">
                        </div>
                        <div class="ml-3 text-body">
                            <span class="font-medium text-neutral-800 group-hover:text-primary-700 transition-colors">Cabang adalah PKP (Pengusaha Kena Pajak)</span>
                        </div>
                    </label>
                    
                    @if($is_pkp)
                    <x-ui.input 
                        name="npwp" 
                        label="NPWP Cabang" 
                        wire:model="npwp" 
                        placeholder="Misal: 12.345.678.9-012.000"
                    />
                    @endif

                    <label class="flex items-start cursor-pointer group mt-2">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" wire:model="active" class="w-4 h-4 text-primary-600 bg-neutral-100 border-neutral-300 rounded focus:ring-primary-500 focus:ring-2">
                        </div>
                        <div class="ml-3 text-body">
                            <span class="font-medium text-neutral-800 group-hover:text-primary-700 transition-colors">Cabang Aktif</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-200 flex items-center justify-end gap-3 rounded-b-xl">
            <x-ui.button type="button" variant="ghost" wire:click="$dispatch('close-store-modal')">
                Batal
            </x-ui.button>
            <x-ui.button type="submit" variant="primary">
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </x-ui.button>
        </div>
    </form>
</div>
