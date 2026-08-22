@props(['title' => null, 'breadcrumbs' => []])

{{--
    Header halaman untuk desktop (>= md). Opt-in dari tiap view lewat
    prop :title dan/atau :breadcrumbs pada <x-layouts.app>, plus slot
    "actions" untuk tombol di kanan (mis. tombol "+ Tambah Produk").

    Contoh pemakaian dari view:

        <x-layouts.app title="Katalog Produk" :breadcrumbs="[
            ['label' => 'Dashboard', 'route' => route('dashboard')],
            ['label' => 'Katalog Produk'],
        ]">
            <x-slot:actions>
                <flux:button variant="primary">+ Tambah Produk</flux:button>
            </x-slot:actions>

            ... konten halaman ...
        </x-layouts.app>
--}}
<div class="hidden md:block sticky top-0 z-20 bg-white border-b border-neutral-200 px-6 md:px-8 py-4">
    <div class="flex items-center justify-between gap-4">
        <div class="flex items-center gap-4 min-w-0">
            <!-- Hamburger Button -->
            <button @click="sidebarOpen = true" x-show="!sidebarOpen" aria-controls="main-sidebar"
                :aria-expanded="sidebarOpen.toString()" aria-label="Buka sidebar"
                class="p-2 -ml-2 rounded-md text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0">
                <flux:icon name="bars-3" variant="outline" class="w-5 h-5 shrink-0 stroke-2" />
            </button>
            
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded bg-neutral-900 flex items-center justify-center shrink-0">
                    <span class="text-white text-[11px] font-bold leading-none">P</span>
                </div>
                <span class="text-[14px] font-bold text-neutral-900 tracking-tight hidden sm:inline">POS System</span>
            </div>

            <div class="min-w-0 ml-4 border-l border-neutral-200 pl-4">
            @if (!empty($breadcrumbs))
                <nav aria-label="Breadcrumb" class="flex items-center gap-1.5 text-body-sm text-neutral-500 mb-1">
                    @foreach ($breadcrumbs as $crumb)
                        @if (!$loop->first)
                            <span class="text-neutral-300" aria-hidden="true">/</span>
                        @endif
                        @if (!$loop->last && !empty($crumb['route']))
                            <a href="{{ $crumb['route'] }}" wire:navigate
                                class="hover:text-neutral-900 transition-colors truncate">{{ $crumb['label'] }}</a>
                        @else
                            <span class="text-neutral-900 font-medium truncate">{{ $crumb['label'] }}</span>
                        @endif
                    @endforeach
                </nav>
            @endif

            @if ($title)
                <h1 class="text-h2 text-neutral-900 truncate">{{ $title }}</h1>
            @endif
            </div>
        </div>

        @isset($actions)
            <div class="flex items-center gap-2 shrink-0">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
