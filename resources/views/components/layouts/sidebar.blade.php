<div x-show="sidebarOpen" @click="sidebarOpen = false"
     x-transition.opacity.duration.200ms
     class="fixed inset-0 bg-neutral-900/20 z-40 md:hidden" x-cloak tabindex="-1" aria-hidden="true"></div>

<button x-show="!sidebarOpen" @click="sidebarOpen = true"
        x-transition.opacity.duration.200ms
        aria-controls="main-sidebar"
        :aria-expanded="sidebarOpen.toString()"
        aria-label="Open sidebar"
        class="md:hidden fixed top-4 left-4 z-40 p-2 bg-white border border-neutral-200 rounded-md text-neutral-600 shadow-sm focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors">
    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
</button>

<aside id="main-sidebar"
       x-data="{
           trapFocus(e) {
               if (window.innerWidth >= 768 || !sidebarOpen) return;
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
       @keydown.tab="trapFocus($event)"
       @keydown.escape.window="if(window.innerWidth < 768) sidebarOpen = false"
       :class="[
           sidebarOpen ? 'translate-x-0 w-64' : '-translate-x-full md:translate-x-0 md:w-[72px]',
           ready ? 'transition-all duration-200 ease-out' : ''
       ]"
       class="bg-white border-r border-neutral-200 flex flex-col fixed h-full z-40 text-neutral-900 overflow-x-hidden md:overflow-visible shadow-none">

    <div class="h-16 flex items-center px-4 md:px-[20px] mb-2 mt-2">
        <button @click="sidebarOpen = !sidebarOpen"
                aria-label="Toggle sidebar"
                :aria-expanded="sidebarOpen.toString()"
                class="hidden md:flex p-1.5 rounded-md text-neutral-500 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors items-center justify-center shrink-0 w-8 h-8">
            <svg class="w-5 h-5 shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round">
                <line x1="3" y1="12" x2="21" y2="12" :class="sidebarOpen ? 'opacity-0' : 'opacity-100'"></line>
                <line x1="3" y1="6" x2="21" y2="6" :class="sidebarOpen ? 'translate-y-[6px] rotate-45 origin-center' : ''" class="transition-all"></line>
                <line x1="3" y1="18" x2="21" y2="18" :class="sidebarOpen ? '-translate-y-[6px] -rotate-45 origin-center' : ''" class="transition-all"></line>
            </svg>
        </button>

        <div :class="sidebarOpen ? 'opacity-100 translate-x-0 ml-3 flex items-center gap-2.5' : 'opacity-0 -translate-x-2 w-0 hidden'"
             class="transition-all duration-200 overflow-hidden whitespace-nowrap">
            <div class="w-6 h-6 rounded bg-neutral-900 flex items-center justify-center shrink-0">
                <span class="text-white text-[10px] font-bold leading-none">P</span>
            </div>
            <span class="text-[15px] font-semibold text-neutral-900 tracking-tight">POS System</span>
        </div>
    </div>

    <nav class="flex-1 py-2 relative px-3"
         x-data="{ currentPath: window.location.pathname }">
        <div class="flex flex-col w-full space-y-1">
            @php
                $navItems = [
                    ['url' => route('dashboard'), 'match' => 'dashboard', 'label' => 'Dashboard', 'icon' => '<rect x="3" y="3" width="7" height="9" rx="1"></rect><rect x="14" y="3" width="7" height="5" rx="1"></rect><rect x="14" y="12" width="7" height="9" rx="1"></rect><rect x="3" y="16" width="7" height="5" rx="1"></rect>', 'visible' => true],
                    ['url' => route('pos'), 'match' => 'pos', 'label' => 'Kasir / POS', 'icon' => '<path d="M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2"></path><rect x="8" y="2" width="8" height="4" rx="1" ry="1"></rect><line x1="9" y1="10" x2="15" y2="10"></line><line x1="9" y1="14" x2="15" y2="14"></line><line x1="9" y1="18" x2="13" y2="18"></line>', 'visible' => true],
                    ['url' => route('catalog.products'), 'match' => 'catalog/products', 'label' => 'Katalog Produk', 'icon' => '<path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"></path><polyline points="3.27 6.96 12 12.01 20.73 6.96"></polyline><line x1="12" y1="22.08" x2="12" y2="12"></line>', 'visible' => true],
                    ['url' => route('catalog.categories'), 'match' => 'catalog/categories', 'label' => 'Kategori', 'icon' => '<path d="M20 6H4l2 12h12l2-12z"></path><path d="M10 12l2-2 2 2"></path>', 'visible' => true],
                ];
                $managementItems = [
                    ['url' => route('customers'), 'match' => 'customers', 'label' => 'Pelanggan', 'icon' => '<path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><path d="M23 21v-2a4 4 0 0 0-3-3.87"></path><path d="M16 3.13a4 4 0 0 1 0 7.75"></path>', 'visible' => true],
                    ['url' => route('stores'), 'match' => 'stores', 'label' => 'Cabang', 'icon' => '<rect x="4" y="2" width="16" height="20" rx="2" ry="2"></rect><path d="M9 22v-4h6v4"></path><path d="M8 6h.01"></path><path d="M16 6h.01"></path><path d="M12 6h.01"></path><path d="M12 10h.01"></path><path d="M12 14h.01"></path><path d="M16 10h.01"></path><path d="M16 14h.01"></path><path d="M8 10h.01"></path><path d="M8 14h.01"></path>', 'visible' => true],
                    ['url' => route('reports'), 'match' => 'reports', 'label' => 'Laporan', 'icon' => '<line x1="18" y1="20" x2="18" y2="10"></line><line x1="12" y1="20" x2="12" y2="4"></line><line x1="6" y1="20" x2="6" y2="14"></line>', 'visible' => true],
                ];
                $accountItems = [
                    ['url' => route('staff.index'), 'match' => 'staff', 'label' => 'Daftar Staff', 'icon' => '<path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2"></path><circle cx="9" cy="7" r="4"></circle><line x1="19" y1="8" x2="19" y2="14"></line><line x1="22" y1="11" x2="16" y2="11"></line>', 'visible' => true],
                    ['url' => route('profile.edit'), 'match' => 'profile', 'label' => 'Pengaturan Profil', 'icon' => '<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>', 'visible' => true],
                    ['url' => route('security.edit'), 'match' => 'security', 'label' => 'Keamanan', 'icon' => '<rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect><path d="M7 11V7a5 5 0 0 1 10 0v4"></path>', 'visible' => true],
                ];
            @endphp

            @foreach($navItems as $item)
                @if(!isset($item['visible']) || $item['visible'])
                    @php
                        $itemPath = parse_url($item['url'], PHP_URL_PATH) ?? '/';
                    @endphp
                    <a @if($item['url'] != '#') @click="currentPath = '{{ $itemPath }}'" href="{{ $item['url'] }}" wire:navigate @else href="#" @endif
                       :aria-current="currentPath === '{{ $itemPath }}' ? 'page' : null"
                       class="flex items-center h-10 group relative z-10 w-full rounded-lg focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors overflow-visible px-2.5"
                       :class="currentPath === '{{ $itemPath }}' ? 'bg-neutral-100 text-neutral-900 font-medium' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900'">
                        <svg class="w-[18px] h-[18px] shrink-0 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            {!! $item['icon'] !!}
                        </svg>
                        <span :class="sidebarOpen ? 'opacity-100 w-auto ml-3' : 'opacity-0 w-0 ml-0'"
                              class="text-[14px] transition-all overflow-hidden whitespace-nowrap">{{ $item['label'] }}</span>

                        <div x-show="!sidebarOpen" aria-hidden="true"
                             class="invisible opacity-0 md:group-hover:visible md:group-hover:opacity-100 md:group-focus-visible:visible md:group-focus-visible:opacity-100 absolute left-full ml-2 translate-x-0 bg-neutral-900 text-white text-[12px] font-medium px-2 py-1 rounded whitespace-nowrap z-50 shadow-sm transition-opacity">
                             {{ $item['label'] }}
                        </div>
                    </a>
                @endif
            @endforeach

            <div :class="sidebarOpen ? 'opacity-100 mt-6 mb-2 px-3' : 'opacity-0 h-0 overflow-hidden'" class="transition-all duration-300">
                <div class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Manajemen</div>
            </div>
            <div x-show="!sidebarOpen" class="mt-4 mb-2 h-px bg-neutral-200 mx-3"></div>

            @foreach($managementItems as $item)
                @if(!isset($item['visible']) || $item['visible'])
                    @php
                        $itemPath = parse_url($item['url'], PHP_URL_PATH) ?? '/';
                    @endphp
                    <a @if($item['url'] != '#') @click="currentPath = '{{ $itemPath }}'" href="{{ $item['url'] }}" wire:navigate @else href="#" @endif
                       :aria-current="currentPath === '{{ $itemPath }}' ? 'page' : null"
                       class="flex items-center h-10 group relative z-10 w-full rounded-lg focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors overflow-visible px-2.5"
                       :class="currentPath === '{{ $itemPath }}' ? 'bg-neutral-100 text-neutral-900 font-medium' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900'">
                        <svg class="w-[18px] h-[18px] shrink-0 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            {!! $item['icon'] !!}
                        </svg>
                        <span :class="sidebarOpen ? 'opacity-100 w-auto ml-3' : 'opacity-0 w-0 ml-0'"
                              class="text-[14px] transition-all overflow-hidden whitespace-nowrap">{{ $item['label'] }}</span>

                        <div x-show="!sidebarOpen" aria-hidden="true"
                             class="invisible opacity-0 md:group-hover:visible md:group-hover:opacity-100 md:group-focus-visible:visible md:group-focus-visible:opacity-100 absolute left-full ml-2 translate-x-0 bg-neutral-900 text-white text-[12px] font-medium px-2 py-1 rounded whitespace-nowrap z-50 shadow-sm transition-opacity">
                             {{ $item['label'] }}
                        </div>
                    </a>
                @endif
            @endforeach

            <div :class="sidebarOpen ? 'opacity-100 mt-6 mb-2 px-3' : 'opacity-0 h-0 overflow-hidden'" class="transition-all duration-300">
                <div class="text-[11px] font-bold text-neutral-400 uppercase tracking-wider">Akun</div>
            </div>
            <div x-show="!sidebarOpen" class="mt-4 mb-2 h-px bg-neutral-200 mx-3"></div>

            @foreach($accountItems as $item)
                @if(!isset($item['visible']) || $item['visible'])
                    @php
                        $itemPath = parse_url($item['url'], PHP_URL_PATH) ?? '/';
                    @endphp
                    <a @if($item['url'] != '#') @click="currentPath = '{{ $itemPath }}'" href="{{ $item['url'] }}" wire:navigate @else href="#" @endif
                       :aria-current="currentPath === '{{ $itemPath }}' ? 'page' : null"
                       class="flex items-center h-10 group relative z-10 w-full rounded-lg focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors overflow-visible px-2.5"
                       :class="currentPath === '{{ $itemPath }}' ? 'bg-neutral-100 text-neutral-900 font-medium' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900'">
                        <svg class="w-[18px] h-[18px] shrink-0 transition-colors" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                            {!! $item['icon'] !!}
                        </svg>
                        <span :class="sidebarOpen ? 'opacity-100 w-auto ml-3' : 'opacity-0 w-0 ml-0'"
                              class="text-[14px] transition-all overflow-hidden whitespace-nowrap">{{ $item['label'] }}</span>

                        <div x-show="!sidebarOpen" aria-hidden="true"
                             class="invisible opacity-0 md:group-hover:visible md:group-hover:opacity-100 md:group-focus-visible:visible md:group-focus-visible:opacity-100 absolute left-full ml-2 translate-x-0 bg-neutral-900 text-white text-[12px] font-medium px-2 py-1 rounded whitespace-nowrap z-50 shadow-sm transition-opacity">
                             {{ $item['label'] }}
                        </div>
                    </a>
                @endif
            @endforeach
        </div>
    </nav>

    <div x-data="{ profileMenuOpen: false }"
         @click.outside="profileMenuOpen = false"
         @keydown.escape.window="profileMenuOpen = false"
         class="relative border-t border-neutral-100">

         <div id="profile-menu"
              role="menu"
              x-show="profileMenuOpen"
              x-transition:enter="transition ease-out duration-200"
              x-transition:enter-start="opacity-0 scale-95"
              x-transition:enter-end="opacity-100 scale-100"
              x-transition:leave="transition ease-in duration-150"
              x-transition:leave-start="opacity-100 scale-100"
              x-transition:leave-end="opacity-0 scale-95"
              x-cloak
              class="absolute bottom-full mb-2 bg-white border border-neutral-200 rounded-xl shadow-lg overflow-hidden z-50"
              :class="sidebarOpen ? 'left-3 right-3' : 'left-14 ml-2 w-56 md:left-full md:ml-2'">

             @php
                 $user = auth('web')->user();
                 $userName = $user ? $user->full_name : 'Admin';
                 $userEmail = $user ? $user->email : '';
                 $initial = $user ? $user->initials() : 'A';
             @endphp

             <div class="px-3 py-2.5 border-b border-neutral-100">
                 <p class="text-[12px] text-neutral-500 truncate">{{ $userEmail }}</p>
             </div>

             <div class="py-1">
                 <a href="{{ route('profile.edit') }}" role="menuitem" class="flex items-center gap-2.5 px-3 py-2 text-[13px] text-neutral-700 hover:bg-neutral-50 transition-colors" wire:navigate>
                     <svg class="w-[16px] h-[16px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                         <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path><circle cx="12" cy="7" r="4"></circle>
                     </svg>
                     Pengaturan Profil
                 </a>

                 <div class="border-t border-neutral-100 my-1"></div>

                 <form method="POST" action="{{ route('logout') }}" class="m-0">
                     @csrf
                     <button type="submit" role="menuitem" class="w-full flex items-center gap-2.5 px-3 py-2 text-[13px] text-neutral-700 hover:bg-neutral-50 transition-colors text-left">
                         <svg class="w-[16px] h-[16px] shrink-0" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                             <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4" /><polyline points="16 17 21 12 16 7" /><line x1="21" y1="12" x2="9" y2="12" />
                         </svg>
                         Keluar
                     </button>
                 </form>
             </div>
         </div>

         <button type="button"
                 @click="profileMenuOpen = !profileMenuOpen"
                 aria-haspopup="true"
                 aria-controls="profile-menu"
                 :aria-expanded="profileMenuOpen.toString()"
                 class="w-full p-3 flex items-center text-left group transition-colors duration-200 hover:bg-neutral-50 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none">
             <div class="flex items-center">
             <div class="w-8 h-8 rounded-md bg-neutral-100 text-neutral-700 border border-neutral-200 flex items-center justify-center font-medium text-[13px] shrink-0 mx-auto md:mx-0">
                {{ $initial }}
             </div>
             <div :class="sidebarOpen ? 'opacity-100 w-auto ml-3' : 'opacity-0 w-0 ml-0'" class="transition-all duration-200 overflow-hidden whitespace-nowrap hidden md:block">
                 <p class="text-[13px] font-medium text-neutral-900 leading-tight">{{ $userName }}</p>
                 <p class="text-[11px] text-neutral-500 leading-tight mt-0.5 capitalize">Staff</p>
             </div>
         </div>
         </button>
    </div>
</aside>
