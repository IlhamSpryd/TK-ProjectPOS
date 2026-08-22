@props([
    'title' => null,
    'description' => null,
    'padding' => 'p-6',
])

<div {{ $attributes->merge(['class' => "bg-white border border-neutral-200 rounded-lg flex flex-col"]) }}>
    @if($title || isset($action) || $description)
        <div class="flex justify-between items-start border-b border-neutral-100 pb-4 mb-4 {{ $padding }} pb-0">
            <div>
                @if($title)
                    <h3 class="text-h3 text-neutral-800">{{ $title }}</h3>
                @endif
                @if($description)
                    <p class="text-body-sm text-neutral-500 mt-1">{{ $description }}</p>
                @endif
            </div>
            @if(isset($action))
                <div class="ml-4 shrink-0">
                    {{ $action }}
                </div>
            @endif
        </div>
    @endif
    
    <div class="{{ $padding }} @if($title || isset($action) || $description) pt-0 @endif flex-1">
        {{ $slot }}
    </div>
</div>
