@props(['item'])

@php
    $isActive   = request()->routeIs($item['match']);
    $stateClass = $isActive
        ? 'bg-neutral-100 text-neutral-900 font-medium'
        : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900 font-normal';
@endphp

<a @if ($item['url'] != '#') href="{{ $item['url'] }}" wire:navigate.hover @else href="#" @endif
    @if ($isActive) aria-current="page" @endif
    data-nav-label="{{ $item['label'] }}"
    :title="(sidebarCollapsed && !sidebarOpen) ? $el.dataset.navLabel : ''"
    :class="sidebarCollapsed ? 'lg:justify-center lg:px-0' : 'lg:px-2'"
    class="{{ $stateClass }} flex items-center h-9 w-full rounded-lg px-2 focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-all duration-150 relative">

    {{-- Active indicator: left accent bar —
         disembunyikan saat icon-only (tidak ada ruang untuk accent bar 2px) --}}
    @if ($isActive)
        <span
            class="absolute left-0 top-1 bottom-1 w-0.5 rounded-r bg-neutral-900 transition-opacity duration-200"
            :class="sidebarCollapsed ? 'lg:opacity-0' : 'opacity-100'"
            aria-hidden="true">
        </span>
    @endif

    {{-- Icon: selalu terlihat --}}
    <flux:icon name="{{ $item['icon'] }}" variant="outline"
        class="w-[18px] h-[18px] shrink-0 stroke-2 transition-colors" />

    {{-- Label: hilang saat collapsed di desktop (lg+) --}}
    <span
        class="text-body-sm whitespace-nowrap ml-3 transition-[opacity,max-width] duration-200 overflow-hidden"
        :class="sidebarCollapsed ? 'lg:opacity-0 lg:max-w-0 lg:ml-0' : 'opacity-100 max-w-xs'">
        {{ $item['label'] }}
    </span>
</a>
