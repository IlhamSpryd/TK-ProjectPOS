@props(['icon' => 'folder', 'title' => 'Tidak ada data', 'description' => null])

<div {{ $attributes->merge(['class' => 'flex flex-col items-center justify-center py-12 px-4 text-center']) }}>
    <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4 border border-neutral-200 mx-auto shadow-sm">
        @if(str_starts_with($icon, '<svg'))
            {!! $icon !!}
        @else
            @php
                $iconName = str_replace('flux:icon.', '', $icon);
            @endphp
            <flux:icon :icon="$iconName" class="w-8 h-8 text-neutral-400" />
        @endif
    </div>
    <h3 class="text-h3 font-semibold text-neutral-900 mb-1">{{ $title }}</h3>
    @if($description)
        <p class="text-body text-neutral-500 max-w-sm mx-auto mb-4">{{ $description }}</p>
    @endif
    @if(isset($action))
        <div class="mt-2">
            {{ $action }}
        </div>
    @endif
</div>
