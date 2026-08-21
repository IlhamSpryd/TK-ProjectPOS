<form wire:submit="save">
    <flux:modal.header>
        <flux:heading size="lg">{{ $categoryId ? 'Edit Kategori' : 'Tambah Kategori' }}</flux:heading>
        <flux:subheading>Isi detail kategori di bawah ini.</flux:subheading>
    </flux:modal.header>

    <div class="space-y-4 my-4">
        <flux:input wire:model="name" label="Nama Kategori" placeholder="Cth: Minuman Dingin" required />
        
        <flux:select wire:model="parent_id" label="Induk Kategori (Opsional)" placeholder="Pilih Kategori Induk...">
            <flux:select.option value="">-- Tanpa Induk --</flux:select.option>
            @foreach($parentCategories as $parent)
                <flux:select.option value="{{ $parent->id }}">{{ $parent->name }}</flux:select.option>
            @endforeach
        </flux:select>

        <flux:checkbox wire:model="active" label="Kategori Aktif" description="Kategori yang tidak aktif tidak akan ditampilkan saat memilih produk." />
    </div>

    <flux:modal.footer>
        <flux:button variant="ghost" x-on:click="$flux.modal('category-modal').close()">Batal</flux:button>
        <flux:button type="submit" variant="primary">Simpan Kategori</flux:button>
    </flux:modal.footer>
</form>
