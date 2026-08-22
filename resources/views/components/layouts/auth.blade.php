<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-neutral-50 antialiased font-sans">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Login - POS App' }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700&display=swap"
        rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="h-full flex flex-col items-center justify-center p-4">

    <div class="mb-8 flex flex-col items-center text-center">
        <div class="w-9 h-9 rounded-md bg-neutral-900 flex items-center justify-center mb-4">
            <span class="text-white text-body font-bold leading-none">P</span>
        </div>
        <h1 class="text-display text-neutral-900">POS System</h1>
        <p class="text-body text-neutral-500 mt-1.5">{{ $subtitle ?? 'Masuk ke akun Anda' }}</p>
    </div>

    <x-ui.card class="w-full max-w-md p-8 border-neutral-200 shadow-sm">
        {{ $slot }}
    </x-ui.card>

    <p class="text-caption text-neutral-400 mt-8">&copy; {{ date('Y') }} POS System</p>

    @livewireScripts
</body>

</html>
