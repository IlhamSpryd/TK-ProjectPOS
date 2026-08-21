<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-50 antialiased font-sans">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Login - POS App' }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
    @fluxStyles
</head>
<body class="h-full flex flex-col items-center justify-center p-4">
    
    <div class="mb-8 text-center">
        <h1 class="text-display text-primary-600 font-bold tracking-tight">POS System</h1>
        <p class="text-neutral-500 mt-2">Log in to your account</p>
    </div>

    <x-ui.card class="w-full max-w-md p-8 shadow-lg border-neutral-200">
        {{ $slot }}
    </x-ui.card>
    
    @livewireScripts
    @fluxScripts
</body>
</html>
