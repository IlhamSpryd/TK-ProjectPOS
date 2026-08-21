@props(['message', 'type' => 'success'])

@php
$borderClass = match($type) {
    'success' => 'border-success-500',
    'error' => 'border-danger-500',
    'info' => 'border-info-500',
    'warning' => 'border-warning-500',
    default => 'border-info-500',
};
$icon = match($type) {
    'success' => 'check-circle',
    'error' => 'x-circle',
    'info' => 'information-circle',
    'warning' => 'exclamation-triangle',
    default => 'information-circle',
};
$iconColor = match($type) {
    'success' => 'text-success-500',
    'error' => 'text-danger-500',
    'info' => 'text-info-500',
    'warning' => 'text-warning-500',
    default => 'text-info-500',
};
@endphp

<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-x-8"
    x-transition:enter-end="opacity-100 translate-x-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-x-0"
    x-transition:leave-end="opacity-0 translate-x-8"
    class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md border-l-4 {{ $borderClass }} relative mb-3"
>
    <div class="p-4">
        <div class="flex items-start">
            <div class="flex-shrink-0">
                <flux:icon name="{{ $icon }}" class="h-5 w-5 {{ $iconColor }}" />
            </div>
            <div class="ml-3 w-0 flex-1 pt-0.5">
                <p class="text-body font-medium text-neutral-900">
                    {{ $message }}
                </p>
            </div>
            <div class="ml-4 flex flex-shrink-0">
                <button
                    type="button"
                    @click="show = false"
                    class="inline-flex rounded-md bg-white text-neutral-400 hover:text-neutral-500 focus:outline-hidden focus:ring-2 focus:ring-primary-300"
                >
                    <span class="sr-only">Close</span>
                    <flux:icon.x-mark class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>
    
    <!-- Progress bar -->
    <div class="absolute bottom-0 left-0 h-1 bg-neutral-100 w-full">
        <div 
            class="h-full {{ str_replace('border-', 'bg-', $borderClass) }}" 
            style="animation: progress 4s linear forwards;"
        ></div>
    </div>
</div>

<style>
@keyframes progress {
    from { width: 100%; }
    to { width: 0%; }
}
</style>
