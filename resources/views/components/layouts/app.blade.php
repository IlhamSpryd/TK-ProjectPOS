<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'POS App' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxStyles
</head>
<body x-data="{ sidebarOpen: window.innerWidth >= 768, ready: false }" x-init="$nextTick(() => ready = true)"
      class="flex min-h-screen text-neutral-900 antialiased bg-neutral-50 overflow-x-hidden">

    <x-layouts.sidebar />

    <main id="main-content" :class="[sidebarOpen ? 'md:ml-64' : 'md:ml-16', ready ? 'transition-all duration-300 ease-out' : '']"
          class="flex-1 flex flex-col min-h-screen pt-16 md:pt-0 relative min-w-0">

        <div class="flex-1 w-full @if(request()->routeIs('pos')) p-0 flex flex-col h-[calc(100vh-64px)] md:h-screen @else p-4 sm:p-6 md:p-8 flex items-start justify-center @endif">
            <div class="w-full @if(request()->routeIs('pos')) flex-1 flex flex-col h-full @else max-w-7xl mx-auto @endif">
                {{ $slot }}
            </div>
        </div>

    </main>

    @if(session('success'))
        <x-ui.toast :message="session('success')" type="success" />
    @endif
    @if(session('error'))
        <x-ui.toast :message="session('error')" type="error" />
    @endif
    @fluxScripts
    @livewireScripts
</body>
</html>
