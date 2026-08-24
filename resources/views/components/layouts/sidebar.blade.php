{{--
    Sidebar — dual-mode:
    • Mobile / tablet (<lg) : off-canvas overlay, controlled by `sidebarOpen`
    • Desktop (≥lg)         : always visible, width controlled by `sidebarCollapsed`
                              collapsed = w-16 (icon rail), expanded = w-64 (full)
--}}

{{-- Backdrop: overlay di mobile/tablet saat sidebar terbuka --}}
<div x-show="sidebarOpen" @click="sidebarOpen = false"
    x-transition:enter="transition ease-out duration-200"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition ease-in duration-150"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 bg-neutral-900/30 backdrop-blur-[2px] z-30 lg:hidden"
    x-cloak tabindex="-1" aria-hidden="true">
</div>

<aside id="main-sidebar" role="navigation" aria-label="Navigasi utama" x-data="{
    trapFocus(e) {
        if (!sidebarOpen) return;
        const focusable = $el.querySelectorAll('a[href], button:not([disabled]), [tabindex]:not([tabindex=\'-1\'])');
        if (focusable.length === 0) return;
        const first = focusable[0], last = focusable[focusable.length - 1];
        if (e.shiftKey) { if (document.activeElement === first) { last.focus(); e.preventDefault(); } }
        else { if (document.activeElement === last) { first.focus(); e.preventDefault(); } }
    }
}"
    @keydown.tab="trapFocus($event)"
    @keydown.escape.window="sidebarOpen = false"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0',
        sidebarCollapsed ? 'lg:w-16' : 'lg:w-64',
        ready ? 'transition-all duration-300 ease-out' : ''
    ]"
    class="w-64 bg-white border-r border-neutral-200 flex flex-col fixed inset-y-0 left-0 h-full z-40 text-neutral-900 overflow-hidden">

    {{-- ════════════════════════════════════════
        Brand / Logo Header
    ════════════════════════════════════════ --}}
    <div class="h-16 flex items-center px-3 border-b border-neutral-100 shrink-0 gap-3 min-w-0">
        {{-- Logo icon: selalu terlihat --}}
        <div class="w-8 h-8 rounded-lg bg-neutral-900 flex items-center justify-center shrink-0">
            <span class="text-white text-body-sm font-bold leading-none">P</span>
        </div>

        {{-- Brand name: hilang saat collapsed di desktop --}}
        <span
            class="text-body font-bold text-neutral-900 tracking-tight truncate flex-1 min-w-0 transition-[opacity,width] duration-200"
            :class="(sidebarOpen || !sidebarCollapsed) ? 'opacity-100' : 'lg:opacity-0 lg:w-0 lg:pointer-events-none'">
            POS System
        </span>

        {{-- Close button: hanya muncul di mobile --}}
        <button @click="sidebarOpen = false" aria-label="Tutup sidebar"
            class="p-1.5 -mr-1 rounded-md text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0 lg:hidden">
            <flux:icon name="x-mark" variant="outline" class="w-5 h-5 stroke-2" />
        </button>
    </div>

    {{-- ════════════════════════════════════════
        Navigation (scrollable)
    ════════════════════════════════════════ --}}
    <nav class="sidebar-nav-scroll flex-1 min-h-0 overflow-y-auto py-3 px-2 overflow-x-hidden">
        <div class="relative flex flex-col w-full space-y-0.5">
            @php
                $user = auth('web')->user();
                $isSuperAdmin = $user && method_exists($user, 'isSuperAdmin') ? $user->isSuperAdmin() : false;

                $navItems = [
                    [
                        'url'     => route('dashboard'),
                        'match'   => 'dashboard',
                        'label'   => 'Dashboard',
                        'icon'    => 'squares-2x2',
                        'visible' => true,
                    ],
                    [
                        'url'     => route('pos'),
                        'match'   => 'pos',
                        'label'   => 'Kasir / POS',
                        'icon'    => 'calculator',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('pos_access')),
                    ],
                    [
                        'url'     => route('catalog.products'),
                        'match'   => 'catalog.products',
                        'label'   => 'Katalog Produk',
                        'icon'    => 'archive-box',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_catalog')),
                    ],
                    [
                        'url'     => route('catalog.categories'),
                        'match'   => 'catalog.categories',
                        'label'   => 'Kategori',
                        'icon'    => 'tag',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_catalog')),
                    ],
                ];
                $managementItems = [
                    [
                        'url'     => route('inventory.movements'),
                        'match'   => 'inventory.*',
                        'label'   => 'Inventaris',
                        'icon'    => 'clipboard-document-check',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_inventory')),
                    ],
                    [
                        'url'     => route('customers'),
                        'match'   => 'customers',
                        'label'   => 'Pelanggan',
                        'icon'    => 'users',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_customers')),
                    ],
                    [
                        'url'     => route('stores'),
                        'match'   => 'stores',
                        'label'   => 'Cabang',
                        'icon'    => 'building-storefront',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_stores')),
                    ],
                    [
                        'url'     => route('reports'),
                        'match'   => 'reports',
                        'label'   => 'Laporan',
                        'icon'    => 'chart-bar',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('view_reports')),
                    ],
                ];
                $accountItems = [
                    [
                        'url'     => route('staff.index'),
                        'match'   => 'staff.*',
                        'label'   => 'Daftar Staff',
                        'icon'    => 'identification',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('manage_staff')),
                    ],
                    [
                        'url'     => route('profile.edit'),
                        'match'   => 'profile.edit',
                        'label'   => 'Profil',
                        'icon'    => 'user-circle',
                        'visible' => true,
                    ],
                    [
                        'url'     => route('security.edit'),
                        'match'   => 'security.edit',
                        'label'   => 'Keamanan',
                        'icon'    => 'shield-check',
                        'visible' => true,
                    ],
                ];
            @endphp

            {{-- Navigasi Utama --}}
            @foreach ($navItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            {{-- Separator: Manajemen --}}
            <div class="mt-5 mb-1 px-2 h-5 flex items-center overflow-hidden">
                <div class="text-caption font-semibold text-neutral-400 uppercase tracking-wider whitespace-nowrap transition-[opacity] duration-200"
                    :class="(sidebarOpen || !sidebarCollapsed) ? 'opacity-100' : 'lg:opacity-0'">
                    Manajemen
                </div>
                {{-- Garis divider saat collapsed --}}
                <div class="h-px flex-1 bg-neutral-100 transition-[opacity] duration-200"
                    :class="(sidebarOpen || !sidebarCollapsed) ? 'lg:hidden' : 'lg:block hidden'">
                </div>
            </div>

            @foreach ($managementItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            {{-- Separator: Akun --}}
            <div class="mt-5 mb-1 px-2 h-5 flex items-center overflow-hidden">
                <div class="text-caption font-semibold text-neutral-400 uppercase tracking-wider whitespace-nowrap transition-[opacity] duration-200"
                    :class="(sidebarOpen || !sidebarCollapsed) ? 'opacity-100' : 'lg:opacity-0'">
                    Akun
                </div>
                <div class="h-px flex-1 bg-neutral-100 transition-[opacity] duration-200"
                    :class="(sidebarOpen || !sidebarCollapsed) ? 'lg:hidden' : 'lg:block hidden'">
                </div>
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
    <div class="border-t border-neutral-100 p-2 shrink-0">
        <x-desktop-user-menu position="top" align="start" />
    </div>

    {{-- ════════════════════════════════════════
        Collapse Toggle (desktop only)
    ════════════════════════════════════════ --}}
    <div class="hidden lg:flex border-t border-neutral-100 shrink-0">
        <button @click="sidebarCollapsed = !sidebarCollapsed"
            class="w-full flex items-center p-3 text-neutral-400 hover:text-neutral-700 hover:bg-neutral-50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-inset focus-visible:ring-neutral-400 transition-colors"
            :class="sidebarCollapsed ? 'justify-center' : 'justify-end'"
            :aria-label="sidebarCollapsed ? 'Perluas sidebar' : 'Perkecil sidebar'">
            {{-- Inline SVG (chevron-right) — tidak pakai flux:icon karena :class pada Flux component
                 dikompilasi sebagai PHP prop, bukan Alpine attribute --}}
            <svg
                class="w-4 h-4 stroke-2 transition-transform duration-300"
                :class="sidebarCollapsed ? 'rotate-0' : 'rotate-180'"
                xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                stroke="currentColor" stroke-width="2" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </button>
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
