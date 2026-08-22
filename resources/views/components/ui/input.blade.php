{{-- 
  Example usage:
  <x-ui.input wire:model="name" name="name" label="Nama Produk" required />
--}}
@props([
    'name',
    'label' => null,
    'required' => false,
    'type' => 'text',
    'icon' => null,
    'helper' => null,
])

@php
    $hasError = $errors->has($name);
    $borderClass = $hasError 
        ? 'border-danger-500 focus:border-danger-500 focus:ring-danger-100' 
        : 'border-neutral-300 focus:border-primary-500 focus:ring-primary-100';
@endphp

<div class="w-full">
    @if($label)
        <label for="{{ $name }}" class="block text-body-sm font-medium text-neutral-700 mb-1.5">
            {{ $label }}
            @if($required)
                <span class="text-danger-500">*</span>
            @endif
        </label>
    @endif
    
    <div class="relative">
        @if($icon)
            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                <flux:icon name="{{ $icon }}" class="w-4 h-4 text-neutral-400" />
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            name="{{ $name }}" 
            id="{{ $name }}" 
            {{ $attributes->merge([
                'class' => "block w-full h-9 bg-white rounded-md border $borderClass shadow-sm text-body-sm text-neutral-900 placeholder-neutral-400 focus:outline-hidden focus:ring-2 transition-shadow disabled:bg-neutral-50 disabled:text-neutral-500 disabled:border-neutral-200" . ($icon ? ' pl-9' : ' px-3')
            ]) }}
        />
        
        {{ $slot }}
    </div>

    @error($name)
        <div class="flex items-start text-danger-600 text-caption mt-1">
            <flux:icon.exclamation-triangle class="w-4 h-4 mr-1 shrink-0" />
            <span>{{ $message }}</span>
        </div>
    @enderror

    @if($helper && !$hasError)
        <p class="text-caption text-neutral-500 mt-1">{{ $helper }}</p>
    @endif
</div>
