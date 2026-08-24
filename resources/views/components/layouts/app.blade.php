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

{{--
    Alpine state:
    - sidebarOpen      : mobile/tablet off-canvas overlay (true = visible)
    - sidebarCollapsed : desktop icon-rail vs full sidebar (true = icon only)
    - ready            : delay transitions on first paint to prevent flash
--}}
<body x-data="{ sidebarOpen: false, sidebarCollapsed: true, ready: false }"
    x-init="$nextTick(() => ready = true)"
    class="flex min-h-screen text-neutral-900 antialiased bg-neutral-50 overflow-x-hidden">

    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:p-4 focus:bg-white focus:text-neutral-900 font-medium rounded-br-lg shadow-sm">Lewati
        ke konten utama</a>

    <x-layouts.sidebar />

    <x-layouts.topbar-mobile :title="$title" />

    {{--
        Main shifts right on desktop (lg+) to account for persistent sidebar.
        - Collapsed (default) → lg:pl-16 (64px = sidebar icon-rail width)
        - Expanded            → lg:pl-64 (256px = sidebar full width)
        Transition is smooth to match sidebar width animation.
    --}}
    <main id="main-content"
        :class="sidebarCollapsed ? 'lg:pl-16' : 'lg:pl-64'"
        class="{{ request()->routeIs('pos') ? 'pt-0' : 'pt-16 md:pt-0' }} flex-1 flex flex-col min-h-screen relative min-w-0 transition-[padding] duration-300 ease-out">

        {{-- Desktop page header. Skipped for POS (full-bleed). --}}
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
