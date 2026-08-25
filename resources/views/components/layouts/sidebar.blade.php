{{--
    Sidebar — dual-mode:
    • Mobile / tablet (<lg) : off-canvas overlay, controlled by `sidebarOpen`
    • Desktop (≥lg)         : always visible, width controlled by `sidebarCollapsed`
                              collapsed = w-[72px] (icon rail), expanded = w-64 (full)

    Icon-rail behaviour lives mostly in <x-layouts.nav-link>: each item hides its
    label (opacity+width, not display:none, so it stays screen-reader accessible)
    and shows a floating tooltip — teleported to <body> so it is never clipped by
    the sidebar's own overflow.
--}}

{{-- Backdrop: overlay on mobile/tablet while the sidebar is open --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0" class="fixed inset-0 bg-neutral-900/30 backdrop-blur-[2px] z-30 md:hidden" x-cloak
    tabindex="-1" aria-hidden="true">
</div>

<aside id="main-sidebar" role="navigation" aria-label="Navigasi utama" x-data="{
    trapFocus(e) {
        if (!sidebarOpen) return;
        const focusable = $el.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex=\'-1\'])');
        if (focusable.length === 0) return;
        const first = focusable[0],
            last = focusable[focusable.length - 1];
        if (e.shiftKey) {
            if (document.activeElement === first) {
                last.focus();
                e.preventDefault();
            }
        } else {
            if (document.activeElement === last) {
                first.focus();
                e.preventDefault();
            }
        }
    }
}"
    @keydown.tab="trapFocus($event)" @keydown.escape.window="sidebarOpen = false"
    :class="[
        sidebarOpen ? 'translate-x-0 shadow-2xl' : '-translate-x-full md:translate-x-0',
        sidebarCollapsed ? 'md:w-[72px]' : 'md:w-64',
        ready ? 'transition-all duration-300 ease-out' : ''
    ]"
    class="w-64 md:w-[72px] bg-white flex flex-col fixed inset-y-0 left-0 h-full z-40 text-neutral-900 border-r border-neutral-200/80 overflow-hidden">

    <style>
        /* Custom Scrollbar Sidebar Tipis */
        .sidebar-nav-scroll::-webkit-scrollbar {
            width: 4px;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-thumb {
            background: transparent;
            border-radius: 9999px;
        }

        .sidebar-nav-scroll:hover::-webkit-scrollbar-thumb {
            background: #d4d4d4;
        }

        .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover {
            background: #a3a3a3;
        }
    </style>

    {{-- ════════════════════════════════════════
        Brand / Logo Header
    ════════════════════════════════════════ --}}
    <div class="h-16 flex items-center shrink-0 min-w-0 transition-all duration-300"
        :class="(sidebarCollapsed && !sidebarOpen) ? 'justify-center px-2 gap-0' : 'px-3 gap-3'">

        {{--
            Toggle collapse/expand: desktop (Gemini-style, di kiri).
            Same box model as <x-layouts.nav-link> (w-11 h-10, mx-auto, inside a
            px-2 wrapper) so the icon sits on the exact same vertical axis as the
            nav icons below it when collapsed — not just centered independently.
        --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed"
            :title="sidebarCollapsed ? 'Perluas sidebar' : 'Perkecil sidebar'"
            :aria-label="sidebarCollapsed ? 'Perluas sidebar' : 'Perkecil sidebar'" aria-controls="main-sidebar"
            :aria-expanded="(!sidebarCollapsed).toString()"
            :class="(sidebarCollapsed && !sidebarOpen) ? 'w-11 mx-auto' : 'w-10'"
            class="h-10 flex items-center justify-center rounded-full text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 active:bg-neutral-200 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0 hidden md:flex">
            <flux:icon name="bars-3" variant="outline" class="w-5 h-5 stroke-2" />
        </button>

        {{-- Wrapper Logo & Brand name --}}
        <div class="flex items-center gap-3 overflow-hidden transition-[opacity,width] duration-300 min-w-0"
            :class="(sidebarOpen || !sidebarCollapsed) ? 'opacity-100 w-full' : 'md:opacity-0 md:w-0'">
            {{-- Logo icon --}}
            <div class="w-8 h-8 rounded-lg bg-neutral-900 flex items-center justify-center shrink-0">
                <span class="text-white text-body-sm font-bold leading-none">P</span>
            </div>

            {{-- Brand name --}}
            <span class="text-base font-bold text-neutral-900 tracking-tight flex-1 min-w-0 truncate">POS System</span>
        </div>

        {{-- Close button: hanya muncul di mobile --}}
        <button @click="sidebarOpen = false" aria-label="Tutup sidebar"
            class="p-1.5 -mr-1 rounded-md text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0 md:hidden">
            <flux:icon name="x-mark" variant="outline" class="w-5 h-5 stroke-2" />
        </button>
    </div>

    {{-- ════════════════════════════════════════
        Navigation (scrollable)
    ════════════════════════════════════════ --}}
    <nav class="sidebar-nav-scroll flex-1 min-h-0 overflow-y-auto py-3 px-2 overflow-x-hidden">
        <div class="relative flex flex-col w-full space-y-1">
            @php
                $user = auth('web')->user();
                $isSuperAdmin = $user && method_exists($user, 'isSuperAdmin') ? $user->isSuperAdmin() : false;

                $navItems = [
                    [
                        'url' => route('dashboard'),
                        'match' => 'dashboard',
                        'label' => 'Dashboard',
                        'icon' => 'squares-2x2',
                        'visible' => true,
                    ],
                    [
                        'url' => route('catalog.products'),
                        'match' => 'catalog.products',
                        'label' => 'Katalog Produk',
                        'icon' => 'archive-box',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_catalog')),
                    ],
                    [
                        'url' => route('catalog.categories'),
                        'match' => 'catalog.categories',
                        'label' => 'Kategori',
                        'icon' => 'tag',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_catalog')),
                    ],
                ];
                $managementItems = [
                    [
                        'url' => route('inventory.movements'),
                        'match' => 'inventory.*',
                        'label' => 'Inventaris',
                        'icon' => 'clipboard-document-check',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_inventory')),
                    ],
                    [
                        'url' => route('customers'),
                        'match' => 'customers',
                        'label' => 'Pelanggan',
                        'icon' => 'users',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_customers')),
                    ],
                    [
                        'url' => route('stores'),
                        'match' => 'stores',
                        'label' => 'Cabang',
                        'icon' => 'building-storefront',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_stores')),
                    ],
                    [
                        'url' => route('reports'),
                        'match' => 'reports',
                        'label' => 'Laporan',
                        'icon' => 'chart-bar',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('view_reports')),
                    ],
                ];
                $accountItems = [
                    [
                        'url' => route('staff.index'),
                        'match' => 'staff.*',
                        'label' => 'Daftar Staff',
                        'icon' => 'identification',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_staff')),
                    ],
                    [
                        'url' => route('profile.edit'),
                        'match' => 'profile.edit',
                        'label' => 'Profil',
                        'icon' => 'user-circle',
                        'visible' => true,
                    ],
                    [
                        'url' => route('security.edit'),
                        'match' => 'security.edit',
                        'label' => 'Keamanan',
                        'icon' => 'shield-check',
                        'visible' => true,
                    ],
                ];
            @endphp

            {{-- Quick Action: Transaksi Baru (Ala Gemini "Percakapan baru") --}}
            @if ($isSuperAdmin || ($user && $user->hasPermission('pos_access')))
                <div class="px-3 mb-5 mt-1">
                    <a href="{{ route('pos') }}" wire:navigate.hover
                        :title="(sidebarCollapsed && !sidebarOpen) ? 'Transaksi Baru' : ''"
                        class="flex items-center h-11 bg-[#e8eaed] hover:bg-[#d9dce0] active:bg-[#cfd3d8] text-neutral-900 rounded-full transition-all duration-200 overflow-hidden group focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none shadow-sm"
                        :class="(sidebarCollapsed && !sidebarOpen) ? 'w-11 justify-center mx-auto p-0' : 'w-full px-3.5'">
                        <flux:icon name="plus"
                            class="w-[22px] h-[22px] shrink-0 text-neutral-600 group-hover:text-neutral-900 transition-colors" />
                        <span
                            class="text-[14px] font-semibold whitespace-nowrap transition-[opacity,width,margin] duration-200"
                            :class="(sidebarCollapsed && !sidebarOpen) ? 'opacity-0 w-0 ml-0' : 'opacity-100 ml-3'">
                            Transaksi Baru
                        </span>
                    </a>
                </div>
            @endif

            {{-- Navigasi Utama --}}
            @foreach ($navItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            {{-- Separator: Manajemen --}}
            <div class="px-3 mt-6 mb-2 h-4 flex items-center overflow-hidden">
                <span
                    class="text-[11px] font-semibold uppercase tracking-wider text-neutral-400 whitespace-nowrap transition-opacity duration-200"
                    :class="(sidebarCollapsed && !sidebarOpen) ? 'opacity-0' : 'opacity-100'">
                    Manajemen
                </span>
            </div>

            @foreach ($managementItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            {{-- Separator: Akun --}}
            <div class="px-3 mt-6 mb-2 h-4 flex items-center overflow-hidden">
                <span
                    class="text-[11px] font-semibold uppercase tracking-wider text-neutral-400 whitespace-nowrap transition-opacity duration-200"
                    :class="(sidebarCollapsed && !sidebarOpen) ? 'opacity-0' : 'opacity-100'">
                    Akun
                </span>
            </div>

            @foreach ($accountItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach
        </div>
    </nav>

    {{-- ════════════════════════════════════════
        User Menu (footer)
    ════════════════════════════════════════ --}}
    <div class="p-2 shrink-0 border-t transition-colors duration-300"
        :class="(sidebarCollapsed && !sidebarOpen) ? 'border-transparent' : 'border-neutral-200'">
        <x-desktop-user-menu position="top" align="start" />
    </div>
</aside>

<style>
    .sidebar-nav-scroll {
        scrollbar-width: thin;
        scrollbar-color: #d4d4d4 transparent;
    }

    .sidebar-nav-scroll::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-nav-scroll::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav-scroll::-webkit-scrollbar-thumb {
        background-color: #d4d4d4;
        border-radius: 9999px;
    }

    .sidebar-nav-scroll::-webkit-scrollbar-thumb:hover {
        background-color: #a3a3a3;
    }
</style>
