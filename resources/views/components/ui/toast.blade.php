@props(['message', 'type' => 'success'])

@php
$borderClass = match($type) {
    'success' => 'border-success-500',
    'error'   => 'border-danger-500',
    'info'    => 'border-info-500',
    'warning' => 'border-warning-500',
    default   => 'border-info-500',
};
$icon = match($type) {
    'success' => 'check-circle',
    'error'   => 'x-circle',
    'info'    => 'information-circle',
    'warning' => 'exclamation-triangle',
    default   => 'information-circle',
};
$iconColor = match($type) {
    'success' => 'text-success-500',
    'error'   => 'text-danger-500',
    'info'    => 'text-info-500',
    'warning' => 'text-warning-500',
    default   => 'text-info-500',
};
$bgClass = str_replace('border-', 'bg-', $borderClass);
@endphp

{{-- Toast: posisi fixed kanan-bawah agar tidak mengganggu layout --}}
<div class="fixed bottom-4 right-4 z-[60] pointer-events-none"
    style="padding-bottom: env(safe-area-inset-bottom);">
<div
    x-data="{ show: true }"
    x-init="setTimeout(() => show = false, 4000)"
    x-show="show"
    x-transition:enter="transition ease-out duration-300"
    x-transition:enter-start="opacity-0 translate-y-2"
    x-transition:enter-end="opacity-100 translate-y-0"
    x-transition:leave="transition ease-in duration-200"
    x-transition:leave-start="opacity-100 translate-y-0"
    x-transition:leave-end="opacity-0 translate-y-2"
    class="pointer-events-auto w-full max-w-sm overflow-hidden rounded-lg bg-white shadow-md border border-neutral-200 border-l-4 {{ $borderClass }} relative"
>
    <div class="p-4">
        <div class="flex items-start gap-3">
            <div class="shrink-0 mt-0.5">
                <flux:icon name="{{ $icon }}" class="h-5 w-5 {{ $iconColor }}" />
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-body font-medium text-neutral-900 leading-snug">
                    {{ $message }}
                </p>
            </div>
            <div class="shrink-0 ml-1">
                <button
                    type="button"
                    @click="show = false"
                    aria-label="Tutup notifikasi"
                    class="inline-flex rounded-md text-neutral-400 hover:text-neutral-600 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400"
                >
                    <flux:icon.x-mark class="h-4 w-4" />
                </button>
            </div>
        </div>
    </div>

    {{-- Progress bar auto-dismiss --}}
    <div class="absolute bottom-0 left-0 h-[3px] bg-neutral-100 w-full">
        <div
            class="h-full {{ $bgClass }}"
            style="animation: toast-progress 4s linear forwards;"
        ></div>
    </div>
</div>
</div>

<style>
@keyframes toast-progress {
    from { width: 100%; }
    to   { width: 0%;   }
}
</style>
