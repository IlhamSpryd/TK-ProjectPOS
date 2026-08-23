<div class="py-6">

    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-h2 text-neutral-900">Kategori Produk</h2>
            <p class="text-body-sm text-neutral-500 mt-1">Kelola pengelompokan produk agar lebih mudah ditemukan di POS.
            </p>
        </div>
        <x-ui.button variant="primary" icon="plus" wire:click="$dispatch('open-category-modal')" class="shrink-0">
            Tambah Kategori
        </x-ui.button>
    </div>

    <x-ui.card class="p-0 overflow-hidden border-neutral-200">
        <div
            class="p-4 border-b border-neutral-200 bg-neutral-50/50 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div class="relative flex items-center w-full max-w-md">
                <x-ui.input name="search" wire:model.live.debounce.300ms="search" icon="magnifying-glass"
                    placeholder="Cari nama kategori..." class="w-full bg-white shadow-none pr-9" />
                @if ($search)
                    <button type="button" wire:click="$set('search', '')" aria-label="Hapus pencarian"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 p-1 rounded-full text-neutral-400 hover:text-neutral-700 hover:bg-neutral-100 transition-colors focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-500">
                        <flux:icon.x-mark class="w-4 h-4" />
                    </button>
                @endif
            </div>
            <p class="text-caption text-neutral-500 shrink-0" aria-live="polite">
                {{ $categories->total() }} kategori{{ $search ? ' ditemukan' : '' }}
            </p>
        </div>

        <div wire:loading.class="opacity-60" wire:target="search" class="transition-opacity duration-150">
            <div class="hidden md:block">
                <x-ui.table>
                    <x-slot:head>
                        <x-ui.table.th class="pl-6">Nama Kategori</x-ui.table.th>
                        <x-ui.table.th>Status</x-ui.table.th>
                        <x-ui.table.th class="text-right pr-6">Aksi</x-ui.table.th>
                    </x-slot:head>

                    @forelse ($categories as $category)
                        <x-ui.table.tr :key="$category->id">
                            <x-ui.table.td class="pl-6">
                                <div
                                    class="flex items-center gap-3 {{ !empty($category->parent) ? 'pl-5 border-l-2 border-neutral-100 ml-1' : '' }}">
                                    <div
                                        class="w-9 h-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200 shrink-0">
                                        <flux:icon.tag class="w-4 h-4" />
                                    </div>
                                    <div class="min-w-0">
                                        <span
                                            class="text-body font-medium text-neutral-800 block truncate">{{ $category->name }}</span>
                                        @if (!empty($category->parent))
                                            <span class="text-caption text-neutral-400">di dalam
                                                {{ $category->parent->name }}</span>
                                        @endif
                                    </div>
                                </div>
                            </x-ui.table.td>
                            <x-ui.table.td>
                                @if ($category->active)
                                    <x-ui.badge variant="success">Aktif</x-ui.badge>
                                @else
                                    <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                                @endif
                            </x-ui.table.td>
                            <x-ui.table.td class="text-right pr-6">
                                <div class="flex items-center justify-end gap-1">
                                    <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                        wire:click="$dispatch('editCategory', { id: '{{ $category->id }}' })"
                                        class="text-neutral-500 hover:text-primary-600"
                                        aria-label="Edit {{ $category->name }}" />
                                    <x-ui.button size="sm" variant="ghost" icon="trash"
                                        wire:click="deleteCategory('{{ $category->id }}')"
                                        wire:confirm="Yakin ingin menghapus kategori &quot;{{ $category->name }}&quot;?"
                                        class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50"
                                        aria-label="Hapus {{ $category->name }}" />
                                </div>
                            </x-ui.table.td>
                        </x-ui.table.tr>
                    @empty
                        <x-slot:empty>
                            @if ($search)
                                <x-ui.empty-state icon="magnifying-glass" title="Tidak ditemukan"
                                    description="Tidak ada kategori yang cocok dengan &quot;{{ $search }}&quot;." />
                            @else
                                <x-ui.empty-state icon="tag" title="Belum ada kategori"
                                    description="Mulai kelompokkan produk Anda dengan menambah kategori pertama." />
                            @endif
                        </x-slot:empty>
                    @endforelse

                    @if ($categories->hasPages())
                        <x-slot:pagination>
                            {{ $categories->links() }}
                        </x-slot:pagination>
                    @endif
                </x-ui.table>
            </div>

            <div class="block md:hidden border-t border-neutral-200 divide-y divide-neutral-100">
                @forelse ($categories as $category)
                    <div class="p-4 flex flex-col gap-3 hover:bg-neutral-50 transition-colors">
                        <div class="flex items-center justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <div
                                    class="w-9 h-9 rounded-lg bg-neutral-100 flex items-center justify-center text-neutral-500 border border-neutral-200 shrink-0">
                                    <flux:icon.tag class="w-4 h-4" />
                                </div>
                                <div class="min-w-0">
                                    <span
                                        class="text-body font-medium text-neutral-800 block truncate">{{ $category->name }}</span>
                                    @if (!empty($category->parent))
                                        <span class="text-caption text-neutral-400">di dalam
                                            {{ $category->parent->name }}</span>
                                    @endif
                                </div>
                            </div>
                            @if ($category->active)
                                <x-ui.badge variant="success">Aktif</x-ui.badge>
                            @else
                                <x-ui.badge variant="neutral">Nonaktif</x-ui.badge>
                            @endif
                        </div>
                        <div class="flex items-center justify-end gap-2 pt-2 border-t border-neutral-100">
                            <x-ui.button size="sm" variant="ghost" icon="pencil-square"
                                wire:click="$dispatch('editCategory', { id: '{{ $category->id }}' })"
                                class="text-neutral-500 hover:text-primary-600"
                                aria-label="Edit {{ $category->name }}" />
                            <x-ui.button size="sm" variant="ghost" icon="trash"
                                wire:click="deleteCategory('{{ $category->id }}')"
                                wire:confirm="Yakin ingin menghapus kategori &quot;{{ $category->name }}&quot;?"
                                class="text-neutral-500 hover:text-danger-600 hover:bg-danger-50"
                                aria-label="Hapus {{ $category->name }}" />
                        </div>
                    </div>
                @empty
                    <div class="p-8">
                        @if ($search)
                            <x-ui.empty-state icon="magnifying-glass" title="Tidak ditemukan"
                                description="Tidak ada kategori yang cocok dengan &quot;{{ $search }}&quot;." />
                        @else
                            <x-ui.empty-state icon="tag" title="Belum ada kategori"
                                description="Mulai kelompokkan produk Anda dengan menambah kategori pertama." />
                        @endif
                    </div>
                @endforelse
                @if ($categories->hasPages())
                    <div class="p-4 border-t border-neutral-200">
                        {{ $categories->links() }}
                    </div>
                @endif
            </div>
        </div>
    </x-ui.card>

    <x-ui.modal name="category-modal" maxWidth="md">
        @livewire('catalog.category-form')
    </x-ui.modal>

    <script>
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('open-category-modal', (event) => {
                window.dispatchEvent(new CustomEvent('open-modal', {
                    detail: 'category-modal'
                }));
            });
            Livewire.on('close-category-modal', (event) => {
                window.dispatchEvent(new CustomEvent('close-modal', {
                    detail: 'category-modal'
                }));
            });
        });
    </script>
</div>
