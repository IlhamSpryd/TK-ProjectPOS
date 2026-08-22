<x-layouts.app title="Kategori Produk" :breadcrumbs="[['label' => 'Dashboard', 'route' => route('dashboard')], ['label' => 'Kategori Produk']]">
<div class="py-6">
    <x-slot:actions>
        <x-ui.button variant="primary" icon="plus" wire:click="$dispatch('editCategory')">
            Tambah Kategori
        </x-ui.button>
    </x-slot:actions>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div class="p-4 border-b border-neutral-200 bg-neutral-50/50">
            <div class="flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass" placeholder="Cari nama kategori..." class="w-full bg-white shadow-none" />
            </div>
        </div>

        <x-ui.table>
            <x-slot:head>
                <x-ui.table.th class="pl-6">Nama Kategori</x-ui.table.th>
                <x-ui.table.th>Status</x-ui.table.th>
                <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
            </x-slot:head>
            
            @forelse ($categories as $category)
                <x-ui.table.tr :key="$category->id">
                    <x-ui.table.td class="pl-6">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200">
                                <flux:icon.tag class="w-5 h-5" />
                            </div>
                            <span class="text-body font-medium text-neutral-800">{{ $category->name }}</span>
                        </div>
                    </x-ui.table.td>
                    <x-ui.table.td>
                        @if($category->active)
                            <x-ui.badge variant="success">Aktif</x-ui.badge>
                        @else
                            <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                        @endif
                    </x-ui.table.td>
                    <x-ui.table.td class="text-right pr-6">
                        <div class="flex items-center justify-end gap-2">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square" wire:click="$dispatch('editCategory', { id: '{{ $category->id }}' })" class="text-neutral-500 hover:text-primary-600" />
                            <x-ui.button size="sm" variant="ghost" icon="trash" wire:click="deleteCategory('{{ $category->id }}')" wire:confirm="Yakin ingin menghapus kategori ini?" class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50" />
                        </div>
                    </x-ui.table.td>
                </x-ui.table.tr>
            @empty
                <x-slot:empty>
                    <x-ui.empty-state icon="tag" title="Tidak ada kategori" description="Belum ada kategori yang dibuat atau sesuai pencarian." />
                </x-slot:empty>
            @endforelse
            
            @if($categories->hasPages())
                <x-slot:pagination>
                    {{ $categories->links() }}
                </x-slot:pagination>
            @endif
        </x-ui.table>
    </x-ui.card>
    
    <x-ui.modal name="category-modal" maxWidth="md">
        @livewire('catalog.category-form')
    </x-ui.modal>
    
    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-category-modal', (event) => {
                window.dispatchEvent(new CustomEvent('open-modal', { detail: 'category-modal' }));
            });
            Livewire.on('close-category-modal', (event) => {
                window.dispatchEvent(new CustomEvent('close-modal', { detail: 'category-modal' }));
            });
        });
    </script>
</div>
</x-layouts.app>

