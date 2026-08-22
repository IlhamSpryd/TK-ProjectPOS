@props(['item'])

@php
    $isActive = request()->routeIs($item['match']);
@endphp

<a @if ($item['url'] != '#') href="{{ $item['url'] }}" wire:navigate.hover @else href="#" @endif
    @if($isActive) aria-current="page" @endif
    class="flex items-center h-9 w-full rounded-lg focus-visible:ring-2 focus-visible:ring-neutral-900 focus-visible:outline-none transition-colors px-2 {{ $isActive ? 'bg-neutral-50 text-neutral-900 font-medium' : 'text-neutral-500 hover:bg-neutral-50 hover:text-neutral-900' }}">
    
    <flux:icon name="{{ $item['icon'] }}" variant="outline" class="w-[18px] h-[18px] shrink-0 transition-colors stroke-2" />
    
    <span class="text-body-sm whitespace-nowrap ml-3">
        {{ $item['label'] }}
    </span>
</a>
