@props(['title' => null])

{{--
    Bilah atas khusus mobile (< md).
    Menggantikan tombol hamburger yang sebelumnya melayang bebas di atas
    konten — sekarang punya latar & tinggi tetap (h-16) selaras dengan
    tinggi header brand di sidebar desktop, jadi tetap terasa satu sistem.
--}}
<div class="md:hidden fixed top-0 inset-x-0 h-16 z-30 bg-white border-b border-neutral-200 flex items-center gap-3 px-4">
    <button @click="sidebarOpen = true" x-show="!sidebarOpen" aria-controls="main-sidebar"
        :aria-expanded="sidebarOpen.toString()" aria-label="Buka sidebar"
        class="p-2 -ml-2 rounded-md text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
            stroke-linecap="round" aria-hidden="true">
            <line x1="3" y1="12" x2="21" y2="12" />
            <line x1="3" y1="6" x2="21" y2="6" />
            <line x1="3" y1="18" x2="21" y2="18" />
        </svg>
    </button>

    <span class="text-body font-semibold text-neutral-900 truncate">
        {{ $title ?? 'POS System' }}
    </span>
</div>
