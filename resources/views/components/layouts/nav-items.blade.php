{{-- UNUSED: Kept for reference. Navigation is built-in inside components/layouts/sidebar.blade.php --}}
@php
$navItems = [
    ['name' => 'Dashboard', 'route' => 'dashboard', 'icon' => 'squares-2x2'],
    ['name' => 'Kasir / POS', 'route' => 'pos', 'icon' => 'calculator'],
    ['name' => 'Katalog Produk', 'route' => 'catalog.products', 'icon' => 'cube'],
    ['name' => 'Kategori', 'route' => 'catalog.categories', 'icon' => 'tag'],
];
@endphp

@foreach($navItems as $item)
    @php
        $isActive = request()->routeIs($item['route']);
    @endphp
    <li>
        <a href="{{ route($item['route']) }}" wire:navigate class="{{ $isActive ? 'bg-neutral-100 text-neutral-900 font-semibold relative' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }} group flex gap-x-3 rounded-lg p-2 text-body leading-6" :class="sidebarCollapsed ? 'justify-center' : ''">
            <flux:icon name="{{ $item['icon'] }}" class="h-5 w-5 shrink-0 {{ $isActive ? 'text-neutral-900' : 'text-neutral-500 group-hover:text-neutral-700' }}" />
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300" x-cloak>{{ $item['name'] }}</span>
        </a>
    </li>
@endforeach

<div x-show="!sidebarCollapsed" class="text-caption font-semibold text-neutral-400 uppercase tracking-wide px-3 mt-6 mb-2" x-cloak>Manajemen</div>
<div x-show="sidebarCollapsed" class="mt-6 mb-2 h-px bg-neutral-200 mx-3" x-cloak></div>
@php
$managementItems = [
    ['name' => 'Pelanggan', 'route' => '#', 'icon' => 'users'],
    ['name' => 'Cabang', 'route' => '#', 'icon' => 'building-storefront'],
    ['name' => 'Laporan', 'route' => '#', 'icon' => 'chart-bar'],
];
@endphp

@foreach($managementItems as $item)
    <li>
        <a href="{{ $item['route'] }}" class="text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 group flex gap-x-3 rounded-lg p-2 text-body leading-6" :class="sidebarCollapsed ? 'justify-center' : ''">
            <flux:icon name="{{ $item['icon'] }}" class="h-5 w-5 shrink-0 text-neutral-500 group-hover:text-neutral-700" />
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300" x-cloak>{{ $item['name'] }}</span>
        </a>
    </li>
@endforeach

<div x-show="!sidebarCollapsed" class="text-caption font-semibold text-neutral-400 uppercase tracking-wide px-3 mt-6 mb-2" x-cloak>Akun</div>
<div x-show="sidebarCollapsed" class="mt-6 mb-2 h-px bg-neutral-200 mx-3" x-cloak></div>
@php
$accountItems = [
    ['name' => 'Pengaturan Profil', 'route' => 'profile.edit', 'icon' => 'user'],
    ['name' => 'Keamanan', 'route' => 'security.edit', 'icon' => 'lock-closed'],
];
@endphp

@foreach($accountItems as $item)
    @php
        $isActive = request()->routeIs($item['route']);
    @endphp
    <li>
        <a href="{{ route($item['route'] ?? $item['route']) }}" wire:navigate class="{{ $isActive ? 'bg-neutral-100 text-neutral-900 font-semibold relative' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }} group flex gap-x-3 rounded-lg p-2 text-body leading-6" :class="sidebarCollapsed ? 'justify-center' : ''">
            <flux:icon name="{{ $item['icon'] }}" class="h-5 w-5 shrink-0 {{ $isActive ? 'text-neutral-900' : 'text-neutral-500 group-hover:text-neutral-700' }}" />
            <span x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-300" x-cloak>{{ $item['name'] }}</span>
        </a>
    </li>
@endforeach
