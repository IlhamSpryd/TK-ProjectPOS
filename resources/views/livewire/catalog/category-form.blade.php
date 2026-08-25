<form wire:submit="save">
    <div class="mb-6 pb-5 border-b border-neutral-100">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-neutral-100 flex items-center justify-center text-neutral-600 shrink-0">
                <flux:icon.tag class="w-5 h-5" />
            </div>
            <div class="min-w-0">
                <h3 class="text-h3 font-semibold text-neutral-900 tracking-tight leading-tight">
                    {{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}
                </h3>
                <p class="text-body-sm text-neutral-500 mt-0.5">
                    {{ $categoryId ? 'Perbarui detail kategori ini.' : 'Buat kategori baru untuk mengelompokkan produk.' }}
                </p>
            </div>
        </div>
    </div>

    <div class="space-y-5">
        <div>
            <x-ui.input name="name" wire:model="name" label="Nama Kategori" placeholder="Cth: Minuman Dingin" required
                autofocus />
            <flux:error name="name" />
        </div>

        <div>
            <x-ui.select name="parent_id" wire:model="parent_id" label="Induk Kategori">
                <option value="">— Tanpa Induk —</option>
                @foreach ($parentCategories as $parent)
                    <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                @endforeach
            </x-ui.select>
            <p class="text-caption text-neutral-500 mt-1.5">Opsional — pilih jika kategori ini merupakan bagian dari
                kategori lain.</p>
            <flux:error name="parent_id" />
        </div>

        <div
            class="flex items-center justify-between gap-4 rounded-xl border border-neutral-200 bg-neutral-50/60 px-4 py-3.5">
            <div class="min-w-0">
                <label for="active" class="text-body font-medium text-neutral-800 cursor-pointer">Kategori
                    Aktif</label>
                <p class="text-caption text-neutral-500 mt-0.5">Kategori nonaktif disembunyikan saat memilih produk.</p>
            </div>
            <label class="relative inline-flex items-center cursor-pointer shrink-0">
                <input wire:model="active" id="active" name="active" type="checkbox" class="sr-only peer">
                <div
                    class="w-10 h-6 bg-neutral-300 rounded-full peer transition-colors duration-200 peer-checked:bg-primary-600 peer-focus-visible:ring-2 peer-focus-visible:ring-primary-500 peer-focus-visible:ring-offset-2 after:content-[''] after:absolute after:top-0.5 after:left-0.5 after:h-5 after:w-5 after:rounded-full after:bg-white after:shadow-sm after:transition-transform after:duration-200 peer-checked:after:translate-x-4">
                </div>
            </label>
        </div>
    </div>

    <x-slot:footer>
        <div class="flex justify-end gap-3 w-full">
            <x-ui.button type="button" variant="ghost"
                x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'category-modal' }))">Batal</x-ui.button>
            <x-ui.button type="submit" variant="primary" icon="check">Simpan Kategori</x-ui.button>
        </div>
    </x-slot:footer>
</form>
