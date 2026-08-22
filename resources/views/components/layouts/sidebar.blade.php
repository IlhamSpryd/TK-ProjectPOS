<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity.duration.200ms
    class="fixed inset-0 bg-neutral-900/30 backdrop-blur-[2px] z-30" x-cloak tabindex="-1" aria-hidden="true"></div>

<aside id="main-sidebar" role="navigation" aria-label="Navigasi utama" x-data="{
    trapFocus(e) {
        if (!sidebarOpen) return;
        const focusable = $el.querySelectorAll('a[href], button:not([disabled]), input:not([disabled]), [tabindex]:not([tabindex=\'-1\'])');
        if (focusable.length === 0) return;
        const first = focusable[0];
        const last = focusable[focusable.length - 1];
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
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ready ? 'transition-transform duration-300 ease-out' : ''
    ]"
    class="bg-white border-r border-neutral-200 flex flex-col fixed inset-y-0 left-0 w-64 h-full z-40 text-neutral-900 overflow-x-hidden shadow-lg">

    <!-- Brand -->
    <div class="h-16 flex items-center justify-between px-4 border-b border-neutral-100 shrink-0">
        <div class="flex items-center min-w-0">
            <div class="w-8 h-8 rounded-lg bg-neutral-900 flex items-center justify-center shrink-0">
                <span class="text-white text-body-sm font-bold leading-none">P</span>
            </div>
            <span class="ml-3 text-body font-bold text-neutral-900 tracking-tight truncate">POS System</span>
        </div>

        <button @click="sidebarOpen = false" aria-label="Tutup sidebar"
            class="p-1.5 -mr-1.5 rounded-md text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0">
            <flux:icon name="x-mark" variant="outline" class="w-5 h-5 stroke-2" />
        </button>
    </div>

    <!-- Navigation (scrollable) -->
    <nav class="sidebar-nav-scroll flex-1 min-h-0 overflow-y-auto py-3 px-3">
        <div class="flex flex-col w-full space-y-0.5">
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
                        'url' => route('pos'),
                        'match' => 'pos',
                        'label' => 'Kasir / POS',
                        'icon' => 'calculator',
                        'visible' => $isSuperAdmin || ($user && $user->hasPermission('pos_access')),
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
                        'label' => 'Pengaturan Profil',
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

            @foreach ($navItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            <div class="mt-5 mb-1.5 px-3">
                <div class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">Manajemen</div>
            </div>

            @foreach ($managementItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            <div class="mt-5 mb-1.5 px-3">
                <div class="text-[11px] font-semibold text-neutral-400 uppercase tracking-wider">Akun</div>
            </div>

            @foreach ($accountItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach
        </div>
    </nav>

    <!-- User menu (pinned) -->
    <div class="border-t border-neutral-100 p-2 shrink-0">
        <x-desktop-user-menu position="top" align="start" />
    </div>
</aside>

<style>
    /* Scrollbar tipis & senyap untuk area navigasi — menghindari scrollbar
       tebal bawaan browser yang terasa berat di dalam drawer selebar 256px. */
    .sidebar-nav-scroll {
        scrollbar-width: thin;
        scrollbar-color: #d4d4d4 transparent;
    }

    .sidebar-nav-scroll::-webkit-scrollbar {
        width: 6px;
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
