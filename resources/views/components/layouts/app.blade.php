@props(['title' => null, 'breadcrumbs' => []])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'POS App' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxAppearance
</head>

<body x-data="{ sidebarOpen: window.innerWidth >= 768, ready: false }" x-init="$nextTick(() => ready = true)"
    class="flex min-h-screen text-neutral-900 antialiased bg-neutral-50 overflow-x-hidden">

    <x-layouts.sidebar />

    <x-layouts.topbar-mobile :title="$title" />

    <main id="main-content"
        :class="[sidebarOpen ? 'md:ml-64' : 'md:ml-[72px]', ready ? 'transition-all duration-300 ease-out' : '']"
        class="flex-1 flex flex-col min-h-screen pt-16 md:pt-0 relative min-w-0">

        {{-- Header halaman desktop: dilewati untuk POS (layar kasir full-bleed)
             dan hanya muncul kalau view memang mengirim title/breadcrumbs,
             supaya tidak dobel dengan judul yang sudah ada di dalam slot. --}}
        @unless (request()->routeIs('pos'))
            @if ($title || !empty($breadcrumbs))
                <x-layouts.topbar :title="$title" :breadcrumbs="$breadcrumbs">
                    @isset($actions)
                        <x-slot:actions>{{ $actions }}</x-slot:actions>
                    @endisset
                </x-layouts.topbar>
            @endif
        @endunless

        <div
            class="flex-1 w-full @if (request()->routeIs('pos')) p-0 flex flex-col h-[calc(100vh-64px)] md:h-screen @else p-4 sm:p-6 md:p-8 flex items-start @endif">
            <div class="w-full @if (request()->routeIs('pos')) flex-1 flex flex-col h-full @endif">
                {{ $slot }}
            </div>
        </div>

    </main>

    @if (session('success'))
        <x-ui.toast :message="session('success')" type="success" />
    @endif
    @if (session('error'))
        <x-ui.toast :message="session('error')" type="error" />
    @endif
    @livewireScripts
    @fluxScripts
</body>

</html>
