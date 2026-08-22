@props(['title' => null])

{{--
    Bilah atas khusus mobile (< md).
    Menggantikan tombol hamburger yang sebelumnya melayang bebas di atas
    konten — sekarang punya latar & tinggi tetap (h-16) selaras dengan
    tinggi header brand di sidebar desktop, jadi tetap terasa satu sistem.
--}}
@if(request()->routeIs('pos'))
<div class="md:hidden fixed top-3 left-3 z-30 flex items-center">
    <button @click="sidebarOpen = true" x-show="!sidebarOpen" aria-controls="main-sidebar"
        :aria-expanded="sidebarOpen.toString()" aria-label="Buka sidebar"
        class="p-2 rounded-md bg-white border border-neutral-200 shadow-sm text-neutral-600 hover:bg-neutral-50 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0">
        <flux:icon name="bars-3" variant="outline" class="w-5 h-5 shrink-0 stroke-2" />
    </button>
</div>
@else
<div class="md:hidden fixed top-0 inset-x-0 h-16 z-20 bg-white border-b border-neutral-200 flex items-center gap-3 px-4">
    <button @click="sidebarOpen = true" x-show="!sidebarOpen" aria-controls="main-sidebar"
        :aria-expanded="sidebarOpen.toString()" aria-label="Buka sidebar"
        class="p-2 -ml-2 rounded-md text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors shrink-0">
        <flux:icon name="bars-3" variant="outline" class="w-5 h-5 shrink-0 stroke-2" />
    </button>

    <span class="text-body font-semibold text-neutral-900 truncate">
        {{ $title ?? 'POS System' }}
    </span>
</div>
@endif
