<div class="flex items-start max-md:flex-col py-6 max-w-5xl mx-auto">
    <div class="me-10 w-full pb-4 md:w-[260px]">
        <div class="flex flex-col gap-2">
            <a href="{{ route('profile.edit') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('profile.edit') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                <flux:icon.user class="w-5 h-5 {{ request()->routeIs('profile.edit') ? 'text-primary-600' : 'text-neutral-400' }}" />
                {{ __('Profil') }}
            </a>
            <a href="{{ route('security.edit') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('security.edit') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                <flux:icon.shield-check class="w-5 h-5 {{ request()->routeIs('security.edit') ? 'text-primary-600' : 'text-neutral-400' }}" />
                {{ __('Keamanan') }}
            </a>
            <a href="{{ route('appearance.edit') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl transition-colors {{ request()->routeIs('appearance.edit') ? 'bg-primary-50 text-primary-700 font-bold' : 'text-neutral-600 hover:bg-neutral-100 hover:text-neutral-900' }}">
                <flux:icon.swatch class="w-5 h-5 {{ request()->routeIs('appearance.edit') ? 'text-primary-600' : 'text-neutral-400' }}" />
                {{ __('Tampilan') }}
            </a>
        </div>
    </div>

    <div class="max-md:hidden w-px h-[calc(100vh-200px)] bg-neutral-200 mr-10"></div>
    <div class="md:hidden w-full h-px bg-neutral-200 my-6"></div>

    <div class="flex-1 self-stretch">
        <div class="mb-8 border-b border-neutral-100 pb-5">
            <h2 class="text-h2 font-black text-neutral-900 tracking-tight">{{ $heading ?? '' }}</h2>
            <p class="text-body text-neutral-500 mt-1">{{ $subheading ?? '' }}</p>
        </div>

        <div class="w-full max-w-2xl">
            {{ $slot }}
        </div>
    </div>
</div>
