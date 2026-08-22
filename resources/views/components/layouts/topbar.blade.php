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
<div class="hidden md:block sticky top-0 z-20 bg-white/80 backdrop-blur border-b border-neutral-200 px-6 md:px-8 py-4">
    <div class="flex items-center justify-between gap-4">
        <div class="min-w-0">
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

        @isset($actions)
            <div class="flex items-center gap-2 shrink-0">
                {{ $actions }}
            </div>
        @endisset
    </div>
</div>
