@props(['target' => null, 'message' => 'Memuat...'])

<div {{ $target ? 'wire:target='.$target : '' }} {{ $attributes->merge(['class' => 'absolute inset-0 z-50 flex flex-col items-center justify-center bg-white/70 backdrop-blur-sm rounded-inherit']) }} wire:loading.flex>
    <div class="bg-white px-4 py-3 rounded-xl shadow-lg border border-neutral-100 flex items-center gap-3">
        <x-ui.loading-spinner size="md" />
        @if($message)
            <span class="text-body-sm font-semibold text-neutral-700">{{ $message }}</span>
        @endif
    </div>
</div>
