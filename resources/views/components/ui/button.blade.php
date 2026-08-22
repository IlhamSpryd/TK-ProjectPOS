{{-- 
  Example usage:
  <x-ui.button variant="primary" type="submit" target="save">Simpan</x-ui.button>
  <x-ui.button variant="secondary" icon="arrow-left">Kembali</x-ui.button>
  <x-ui.button variant="icon" icon="trash" aria-label="Hapus" />
--}}
@props([
    'variant' => 'primary',
    'size' => 'md',
    'type' => 'button',
    'target' => null,
    'icon' => null,
])

@php
    $baseClasses = "inline-flex items-center justify-center font-medium transition-colors duration-150 rounded-lg focus-visible:outline-hidden focus-visible:ring-2 focus-visible:ring-primary-300 focus-visible:ring-offset-2 disabled:opacity-50 disabled:cursor-not-allowed";
    
    $sizeClasses = match($size) {
        'sm' => 'h-8 px-2.5 text-xs',
        'md' => 'h-9 px-3.5 text-sm',
        'lg' => 'h-10 px-5 text-sm',
        default => 'h-9 px-3.5 text-sm',
    };

    if ($variant === 'icon') {
        $sizeClasses = 'w-9 h-9 p-0 flex items-center justify-center';
    }

    $variantClasses = match($variant) {
        'primary' => 'bg-primary-600 text-white hover:bg-primary-700 active:bg-black disabled:bg-neutral-100 disabled:text-neutral-400 disabled:border-neutral-200 border border-transparent shadow-sm',
        'secondary' => 'bg-white border border-neutral-300 text-neutral-700 hover:bg-neutral-50 active:bg-neutral-100 shadow-sm',
        'ghost' => 'text-neutral-600 hover:bg-neutral-100 active:bg-neutral-200 border border-transparent',
        'danger' => 'bg-danger-600 text-white hover:bg-danger-700 active:bg-danger-800 border border-transparent shadow-sm',
        'danger-ghost' => 'text-danger-600 border border-transparent hover:border-danger-200 hover:bg-danger-50',
        'icon' => 'text-neutral-500 hover:text-neutral-900 hover:bg-neutral-100 border border-transparent',
        default => 'bg-primary-600 text-white hover:bg-primary-700 border border-transparent shadow-sm',
    };
@endphp

<button 
    type="{{ $type }}" 
    {{ $attributes->merge(['class' => "$baseClasses $sizeClasses $variantClasses"]) }}
    @if($target)
        wire:loading.attr="disabled"
        wire:target="{{ $target }}"
    @endif
>
    @if($target)
        <svg wire:loading wire:target="{{ $target }}" class="w-4 h-4 {{ trim($slot) ? 'mr-2' : '' }} animate-spin text-current" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    @endif

    @if($icon)
        @if($target)
            <flux:icon name="{{ $icon }}" class="w-5 h-5 {{ trim($slot) ? 'mr-2' : '' }}" wire:loading.remove wire:target="{{ $target }}" />
        @else
            <flux:icon name="{{ $icon }}" class="w-5 h-5 {{ trim($slot) ? 'mr-2' : '' }}" />
        @endif
    @endif

    @if(trim($slot))
        <span>{{ $slot }}</span>
    @endif
</button>
