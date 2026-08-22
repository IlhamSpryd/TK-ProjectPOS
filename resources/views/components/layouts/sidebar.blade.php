<div x-show="sidebarOpen" @click="sidebarOpen = false" x-transition.opacity.duration.200ms
    class="fixed inset-0 bg-neutral-900/20 z-30" x-cloak tabindex="-1" aria-hidden="true"></div>

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
}" @keydown.tab="trapFocus($event)"
    @keydown.escape.window="sidebarOpen = false"
    :class="[
        sidebarOpen ? 'translate-x-0' : '-translate-x-full',
        ready ? 'transition-transform duration-300 ease-out' : ''
    ]"
    class="bg-white border-r border-neutral-200 flex flex-col fixed inset-y-0 left-0 w-64 h-full z-40 text-neutral-900 overflow-x-hidden shadow-xl">

    <div class="h-16 flex items-center justify-between px-4 mb-2 mt-2">
        <!-- Logo -->
        <div class="flex items-center">
            <div class="w-8 h-8 rounded bg-neutral-900 flex items-center justify-center shrink-0">
                <span class="text-white text-body-sm font-bold leading-none">P</span>
            </div>
            
            <div class="ml-3 flex items-center whitespace-nowrap">
                <span class="text-body font-bold text-neutral-900 tracking-tight">POS System</span>
            </div>
        </div>

        <!-- Close Button -->
        <button @click="sidebarOpen = false" class="p-1.5 -mr-1.5 rounded-md text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors">
            <flux:icon name="x-mark" variant="outline" class="w-5 h-5 stroke-2" />
        </button>
    </div>

    <nav class="flex-1 py-2 relative px-3">
        <div class="flex flex-col w-full space-y-1">
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

            <div class="mt-4 mb-1 px-3">
                <div class="text-caption font-bold text-neutral-400 uppercase tracking-wider">Manajemen</div>
            </div>

            @foreach ($managementItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach

            <div class="mt-4 mb-1 px-3">
                <div class="text-caption font-bold text-neutral-400 uppercase tracking-wider">Akun</div>
            </div>

            @foreach ($accountItems as $item)
                @if (!isset($item['visible']) || $item['visible'])
                    <x-layouts.nav-link :item="$item" />
                @endif
            @endforeach
        </div>
    </nav>

    <div class="relative border-t border-neutral-100 p-2">
        <x-desktop-user-menu position="top" align="start" />
    </div>
</aside>
