@props(['item'])

@php
    $active = request()->routeIs($item['match']);
@endphp

<a href="{{ $item['url'] }}" wire:navigate.hover x-data="{ tipVisible: false, tipStyle: '' }"
    @mouseenter="if (sidebarCollapsed && !sidebarOpen) {
        const r = $el.getBoundingClientRect();
        tipStyle = 'top:' + (r.top + r.height / 2) + 'px; left:' + (r.right + 10) + 'px;';
        tipVisible = true;
    }"
    @mouseleave="tipVisible = false"
    @focus="if (sidebarCollapsed && !sidebarOpen) {
        const r = $el.getBoundingClientRect();
        tipStyle = 'top:' + (r.top + r.height / 2) + 'px; left:' + (r.right + 10) + 'px;';
        tipVisible = true;
    }"
    @blur="tipVisible = false" @if ($active) aria-current="page" @endif
    class="relative flex items-center h-10 rounded-full text-[14px] transition-all duration-200 group focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-neutral-900
        {{ $active ? 'bg-[#e8eaed] text-neutral-900 font-semibold' : 'text-neutral-600 font-medium hover:bg-neutral-100 hover:text-neutral-900' }}"
    :class="(sidebarCollapsed && !sidebarOpen) ? 'w-11 justify-center mx-auto px-0' : 'w-full px-3.5 gap-3'">
    <flux:icon :name="$item['icon']" variant="outline"
        class="w-5 h-5 shrink-0 transition-colors {{ $active ? 'text-neutral-900' : 'text-neutral-500 group-hover:text-neutral-900' }}" />

    <span class="whitespace-nowrap overflow-hidden transition-[opacity,width] duration-200"
        :class="(sidebarCollapsed && !sidebarOpen) ? 'opacity-0 w-0' : 'opacity-100 w-auto'">{{ $item['label'] }}</span>

    {{-- Tooltip: teleported to <body> so the collapsed 72px rail never clips it --}}
    <template x-teleport="body">
        <div x-show="tipVisible" x-transition:enter="transition ease-out duration-150"
            x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="transition ease-in duration-100" x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95" :style="tipStyle"
            style="position: fixed; transform: translateY(-50%);"
            class="z-[60] px-2.5 py-1.5 rounded-md bg-neutral-900 text-white text-[13px] font-medium whitespace-nowrap shadow-lg pointer-events-none"
            x-cloak>{{ $item['label'] }}</div>
    </template>
</a>
