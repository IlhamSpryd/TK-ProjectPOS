@props([
    'variant' => 'neutral', // neutral, primary, success, warning, danger, info
])

@php
    $baseClasses = "inline-flex items-center rounded-full px-2.5 py-0.5 text-caption font-medium";
    
    $variantClasses = match($variant) {
        'primary' => 'bg-primary-50 text-primary-700',
        'success' => 'bg-success-50 text-success-700',
        'warning' => 'bg-warning-50 text-warning-700',
        'danger' => 'bg-danger-50 text-danger-700',
        'info' => 'bg-info-50 text-info-700',
        default => 'bg-neutral-100 text-neutral-600',
    };
@endphp

<span {{ $attributes->merge(['class' => "$baseClasses $variantClasses"]) }}>
    {{ $slot }}
</span>
