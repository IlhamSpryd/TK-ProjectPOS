<form wire:submit="save">
    <div class="mb-6 border-b border-neutral-100 pb-4">
        <h3 class="text-h3 font-semibold text-neutral-800 tracking-tight">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}</h3>
        <p class="text-body text-neutral-500 mt-1">Isi detail kategori di bawah ini.</p>
    </div>

    <div class="space-y-5 my-4">
        <div>
            <x-ui.input name="name" wire:model="name" label="Nama Kategori" placeholder="Cth: Minuman Dingin" required />
        </div>
        
        <div>
            <x-ui.select name="parent_id" wire:model="parent_id" label="Induk Kategori (Opsional)">
                <option value="">-- Tanpa Induk --</option>
                @foreach($parentCategories as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </x-ui.select>
        </div>

        <div class="flex items-start mt-2">
            <div class="flex items-center h-5">
                <input wire:model="active" id="active" name="active" type="checkbox" class="w-4 h-4 rounded-sm border-neutral-300 text-primary-600 focus:ring-primary-500">
            </div>
            <div class="ml-3">
                <label for="active" class="text-body font-medium text-neutral-800">Kategori Aktif</label>
                <p class="text-caption text-neutral-500">Kategori yang tidak aktif tidak akan ditampilkan saat memilih produk.</p>
            </div>
        </div>
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-3 w-full">
            <x-ui.button variant="ghost" x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'category-modal' }))">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary">Simpan Kategori</x-ui.button>
        </div>
    </x-slot:footer>
</form>
