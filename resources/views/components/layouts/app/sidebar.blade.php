{{-- UNUSED: Kept for reference. Actual layout uses components/layouts/sidebar.blade.php --}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-neutral-50 dark:bg-neutral-900 font-sans antialiased text-neutral-900 dark:text-neutral-100">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-neutral-200/80 bg-white dark:border-neutral-800/80 dark:bg-neutral-950 shadow-sm shadow-neutral-200/20 dark:shadow-none">
            <flux:sidebar.header class="pt-6 pb-4 px-4 border-b border-neutral-100 dark:border-neutral-800/60 mb-2">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate class="transform hover:scale-105 transition-transform" />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="px-3 gap-1.5">
                <flux:sidebar.group :heading="__('Utama')" class="grid mb-2">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shopping-bag" :href="route('pos')" :current="request()->routeIs('pos')" wire:navigate class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Kasir (POS)') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Katalog')" class="grid mb-2">
                    <flux:sidebar.item icon="cube" :href="route('catalog.products')" :current="request()->routeIs('catalog.products*')" wire:navigate class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Produk') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="tag" :href="route('catalog.categories')" :current="request()->routeIs('catalog.categories')" wire:navigate class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Kategori') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Inventori')" class="grid mb-2">
                    <flux:sidebar.item icon="archive-box" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Stok Barang') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Purchase Order') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-storefront" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Supplier') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Sistem')" class="grid mb-2">
                    <flux:sidebar.item icon="users" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Pelanggan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                        {{ __('Staf') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav class="px-3 pb-4">
                <flux:sidebar.item icon="document-chart-bar" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                    {{ __('Laporan') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" href="#" class="transition-colors hover:bg-neutral-100/80 dark:hover:bg-neutral-800/80 rounded-xl">
                    {{ __('Pengaturan') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <div class="px-4 pb-6 hidden lg:block">
                <div class="p-1.5 rounded-2xl bg-neutral-50 dark:bg-neutral-900 border border-neutral-100 dark:border-neutral-800">
                    <x-desktop-user-menu />
                </div>
            </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-white/80 dark:bg-neutral-950/80 backdrop-blur-md border-b border-neutral-200/80 dark:border-neutral-800/80 sticky top-0 z-40">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <x-desktop-user-menu position="top" align="end" />
        </flux:header>

        {{ $slot }}

        @persist('toast')
            <flux:toast.group>
                <flux:toast />
            </flux:toast.group>
        @endpersist

        @fluxScripts
    </body>
</html>
