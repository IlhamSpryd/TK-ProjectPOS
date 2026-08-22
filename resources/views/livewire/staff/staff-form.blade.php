<div>
    <x-ui.page-header title="Tambah Staff" description="Buat akun baru untuk memberikan akses sistem ke staf.">
        <x-ui.button variant="ghost" href="{{ route('staff.index') }}" wire:navigate class="px-5">Batal</x-ui.button>
        <x-ui.button variant="primary" wire:click="save" wire:loading.attr="disabled" wire:target="save">
            <span wire:loading.remove wire:target="save">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-2 -ml-1"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"></path><polyline points="17 21 17 13 7 13 7 21"></polyline><polyline points="7 3 7 8 15 8"></polyline></svg>
                Simpan Akun
            </span>
            <span wire:loading wire:target="save" class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                Menyimpan...
            </span>
        </x-ui.button>
    </x-ui.page-header>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div class="lg:col-span-2 space-y-6">
            <x-ui.card>
                <div class="p-4 border-b border-neutral-100 flex items-center justify-between">
                    <div>
                        <h2 class="text-body-lg font-semibold text-neutral-800">Informasi Pribadi & Akun</h2>
                        <p class="text-body-sm text-neutral-500 mt-1">Isi data diri dan kredensial akses untuk staf ini.</p>
                    </div>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="col-span-2">
                            <x-ui.input 
                                wire:model="full_name" 
                                name="full_name"
                                label="Nama Lengkap" 
                                placeholder="Masukkan nama lengkap staf" 
                                required 
                            />
                        </div>
                        
                        <div>
                            <x-ui.input 
                                wire:model="email" 
                                name="email"
                                type="email"
                                label="Alamat Email" 
                                placeholder="nama@perusahaan.com" 
                                required 
                            />
                        </div>

                        <div>
                            <x-ui.select name="role_id" wire:model="role_id" label="Role (Peran)" required>
                                <option value="" disabled selected>Pilih role untuk staf</option>
                                @foreach($roles as $role)
                                    <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </x-ui.select>
                        </div>
                    </div>
                </div>
            </x-ui.card>

            <x-ui.card>
                <div class="p-4 border-b border-neutral-100">
                    <h2 class="text-body-lg font-semibold text-neutral-800">Keamanan Akses</h2>
                    <p class="text-body-sm text-neutral-500 mt-1">Konfigurasi password untuk masuk ke dasbor dan PIN kasir.</p>
                </div>
                
                <div class="p-6 space-y-6">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <x-ui.input 
                                wire:model="password" 
                                name="password"
                                type="password"
                                label="Password" 
                                placeholder="Minimal 8 karakter" 
                                required 
                            />
                        </div>
                        <div>
                            <x-ui.input 
                                wire:model="password_confirmation" 
                                name="password_confirmation"
                                type="password"
                                label="Konfirmasi Password" 
                                placeholder="Ketik ulang password" 
                                required 
                            />
                        </div>
                        
                        <div class="col-span-2 border-t border-neutral-100 pt-6"></div>
                        
                        <div>
                            <x-ui.input 
                                wire:model="pin" 
                                name="pin"
                                type="password"
                                label="PIN Kasir" 
                                placeholder="4-6 digit angka" 
                                required 
                            />
                        </div>
                        <div>
                            <x-ui.input 
                                wire:model="pin_confirmation" 
                                name="pin_confirmation"
                                type="password"
                                label="Konfirmasi PIN" 
                                placeholder="Ketik ulang PIN" 
                                required 
                            />
                        </div>
                    </div>
                </div>
            </x-ui.card>
        </div>

        <div class="space-y-6">
            <x-ui.card>
                <div class="p-4 border-b border-neutral-100">
                    <h2 class="text-body-lg font-semibold text-neutral-800">Status Akun</h2>
                </div>
                
                <div class="p-6">
                    <label class="flex items-start cursor-pointer group">
                        <div class="flex items-center h-5 mt-0.5">
                            <input type="checkbox" wire:model="active" class="w-4 h-4 text-primary-600 bg-neutral-100 border-neutral-300 rounded focus:ring-primary-500 focus:ring-2">
                        </div>
                        <div class="ml-3 text-body">
                            <span class="font-medium text-neutral-800 group-hover:text-primary-700 transition-colors">Aktifkan Akun Ini</span>
                            <p class="text-neutral-500 text-sm mt-1">Jika dimatikan, staf tidak akan bisa mengakses sistem.</p>
                        </div>
                    </label>
                </div>
            </x-ui.card>

            <div class="bg-primary-50 text-primary-800 rounded-xl p-4 flex gap-3 text-body-sm items-start ring-1 ring-inset ring-primary-600/10">
                <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="shrink-0 mt-0.5"><circle cx="12" cy="12" r="10"></circle><path d="M12 16v-4"></path><path d="M12 8h.01"></path></svg>
                <div>
                    <span class="font-medium block mb-1">Informasi Hak Akses</span>
                    Pastikan Anda memberikan role yang sesuai. Role "Kasir" hanya memiliki akses ke layar POS, sedangkan "Admin" memiliki akses penuh.
                </div>
            </div>
        </div>
    </div>
</div>
