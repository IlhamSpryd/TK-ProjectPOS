<div>
    <div class="px-6 py-4 border-b border-neutral-200">
        <h3 class="text-h3 font-semibold text-neutral-800">
            {{ $customerId ? 'Edit Pelanggan' : 'Tambah Pelanggan Baru' }}
        </h3>
        <p class="text-body-sm text-neutral-500 mt-1">
            {{ $customerId ? 'Ubah informasi pelanggan di bawah ini.' : 'Isi form di bawah untuk mendaftarkan pelanggan baru.' }}
        </p>
    </div>

    <form wire:submit="save">
        <div class="px-6 py-6 space-y-4">
            <x-ui.input 
                name="name" 
                label="Nama Pelanggan" 
                wire:model="name" 
                placeholder="Misal: Budi Santoso"
                required
            />
            
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <x-ui.input 
                    name="phone" 
                    label="Nomor Telepon" 
                    wire:model="phone" 
                    placeholder="Misal: 0812xxxxxx"
                />
                
                <x-ui.input 
                    name="email" 
                    type="email"
                    label="Email" 
                    wire:model="email" 
                    placeholder="Misal: budi@email.com"
                />
            </div>
            
            <x-ui.input 
                name="npwp" 
                label="NPWP" 
                wire:model="npwp" 
                placeholder="Nomor Pokok Wajib Pajak (jika ada)"
            />

            <div class="w-full">
                <label class="block text-body-sm font-medium text-neutral-700 mb-1.5">Alamat</label>
                <textarea 
                    wire:model="address" 
                    rows="3"
                    class="block w-full bg-white rounded-lg border border-neutral-300 focus:border-primary-500 focus:ring-primary-100 text-body text-neutral-900 placeholder-neutral-400 focus:outline-hidden focus:ring-2 transition-shadow px-3.5 py-2.5"
                    placeholder="Alamat lengkap pelanggan"
                ></textarea>
                @error('address') <span class="text-danger-600 text-caption mt-1">{{ $message }}</span> @enderror
            </div>

            @if($customerId)
            <x-ui.input 
                name="loyalty_points" 
                type="number"
                label="Loyalty Points" 
                wire:model="loyalty_points" 
            />
            @endif

            <label class="flex items-start cursor-pointer group mt-4">
                <div class="flex items-center h-5 mt-0.5">
                    <input type="checkbox" wire:model="active" class="w-4 h-4 text-primary-600 bg-neutral-100 border-neutral-300 rounded focus:ring-primary-500 focus:ring-2">
                </div>
                <div class="ml-3 text-body">
                    <span class="font-medium text-neutral-800 group-hover:text-primary-700 transition-colors">Pelanggan Aktif</span>
                </div>
            </label>
        </div>

        <div class="px-6 py-4 bg-neutral-50 border-t border-neutral-200 flex items-center justify-end gap-3 rounded-b-xl">
            <x-ui.button type="button" variant="ghost" wire:click="$dispatch('close-customer-modal')">
                Batal
            </x-ui.button>
            <x-ui.button type="submit" variant="primary">
                <span wire:loading.remove wire:target="save">Simpan</span>
                <span wire:loading wire:target="save">Menyimpan...</span>
            </x-ui.button>
        </div>
    </form>
</div>
