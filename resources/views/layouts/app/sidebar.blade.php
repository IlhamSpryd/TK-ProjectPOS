<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-zinc-50 dark:bg-zinc-900 font-sans antialiased text-zinc-900 dark:text-zinc-100">
        <flux:sidebar sticky collapsible="mobile" class="border-e border-zinc-200/80 bg-white dark:border-zinc-800/80 dark:bg-zinc-950 shadow-sm shadow-zinc-200/20 dark:shadow-none">
            <flux:sidebar.header class="pt-6 pb-4 px-4 border-b border-zinc-100 dark:border-zinc-800/60 mb-2">
                <x-app-logo :sidebar="true" href="{{ route('dashboard') }}" wire:navigate class="transform hover:scale-105 transition-transform" />
                <flux:sidebar.collapse class="lg:hidden" />
            </flux:sidebar.header>

            <flux:sidebar.nav class="px-3 gap-1.5">
                <flux:sidebar.group :heading="__('Utama')" class="grid mb-2">
                    <flux:sidebar.item icon="layout-grid" :href="route('dashboard')" :current="request()->routeIs('dashboard')" wire:navigate class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Dashboard') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="shopping-bag" :href="route('pos')" :current="request()->routeIs('pos')" wire:navigate class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Kasir (POS)') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Katalog')" class="grid mb-2">
                    <flux:sidebar.item icon="cube" :href="route('catalog.products')" :current="request()->routeIs('catalog.products*')" wire:navigate class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Produk') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="tag" :href="route('catalog.categories')" :current="request()->routeIs('catalog.categories')" wire:navigate class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Kategori') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Inventori')" class="grid mb-2">
                    <flux:sidebar.item icon="archive-box" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Stok Barang') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="truck" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Purchase Order') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="building-storefront" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Supplier') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>

                <flux:sidebar.group :heading="__('Sistem')" class="grid mb-2">
                    <flux:sidebar.item icon="users" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Pelanggan') }}
                    </flux:sidebar.item>
                    <flux:sidebar.item icon="user-group" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                        {{ __('Staf') }}
                    </flux:sidebar.item>
                </flux:sidebar.group>
            </flux:sidebar.nav>

            <flux:spacer />

            <flux:sidebar.nav class="px-3 pb-4">
                <flux:sidebar.item icon="document-chart-bar" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                    {{ __('Laporan') }}
                </flux:sidebar.item>
                <flux:sidebar.item icon="cog-6-tooth" href="#" class="transition-colors hover:bg-zinc-100/80 dark:hover:bg-zinc-800/80 rounded-xl">
                    {{ __('Pengaturan') }}
                </flux:sidebar.item>
            </flux:sidebar.nav>

            <div class="px-4 pb-6 hidden lg:block">
                <div class="p-1.5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-100 dark:border-zinc-800">
                    <x-desktop-user-menu />
                </div>
            </div>
        </flux:sidebar>

        <!-- Mobile User Menu -->
        <flux:header class="lg:hidden bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md border-b border-zinc-200/80 dark:border-zinc-800/80 sticky top-0 z-40">
            <flux:sidebar.toggle class="lg:hidden" icon="bars-2" inset="left" />

            <flux:spacer />

            <flux:dropdown position="top" align="end">
                <flux:profile
                    :name="auth()->user()->full_name ?? auth()->user()->name"
                    :subtitle="auth()->user()->role->name ?? auth()->user()->email ?? 'Staff'"
                    :initials="auth()->user()->initials()"
                    icon-trailing="chevron-down"
                />

                <flux:menu>
                    <flux:menu.radio.group>
                        <div class="p-0 text-sm font-normal">
                            <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
                                <flux:avatar
                                    :name="auth()->user()->full_name ?? auth()->user()->name"
                                    :initials="auth()->user()->initials()"
                                />

                                <div class="grid flex-1 text-start text-sm leading-tight">
                                    <flux:heading class="truncate">{{ auth()->user()->full_name ?? auth()->user()->name }}</flux:heading>
                                    <flux:text class="truncate">{{ auth()->user()->role->name ?? auth()->user()->email ?? 'Staff' }}</flux:text>
                                </div>
                            </div>
                        </div>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <flux:menu.radio.group>
                        <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                            {{ __('Settings') }}
                        </flux:menu.item>
                    </flux:menu.radio.group>

                    <flux:menu.separator />

                    <form method="POST" action="{{ route('logout') }}" class="w-full">
                        @csrf
                        <flux:menu.item
                            as="button"
                            type="submit"
                            icon="arrow-right-start-on-rectangle"
                            class="w-full cursor-pointer"
                            data-test="logout-button"
                        >
                            {{ __('Log out') }}
                        </flux:menu.item>
                    </form>
                </flux:menu>
            </flux:dropdown>
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
