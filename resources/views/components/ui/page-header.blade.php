@props([
    'title',
    'description' => null,
])

<div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
    <div>
        <h1 class="text-h2 font-semibold text-neutral-800 tracking-tight">{{ $title }}</h1>
        @if($description)
            <p class="text-body text-neutral-500 mt-1">{{ $description }}</p>
        @endif
    </div>
    @if(isset($slot) && $slot->isNotEmpty())
        <div class="flex items-center gap-3">
            {{ $slot }}
        </div>
    @endif
</div>
