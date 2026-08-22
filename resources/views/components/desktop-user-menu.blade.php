@props(['position' => 'bottom', 'align' => 'start'])

<flux:dropdown :position="$position" :align="$align">
    <button type="button" class="flex items-center w-full p-1.5 rounded-lg hover:bg-neutral-100 transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500">
        <div class="flex-shrink-0 w-8 h-8 rounded bg-neutral-200 flex items-center justify-center text-sm font-semibold text-neutral-600">
            {{ auth()->user()->initials() }}
        </div>
        <div :class="sidebarOpen ? 'block' : 'hidden'" class="ml-2.5 flex-1 text-left overflow-hidden">
            <p class="text-[14px] font-medium text-neutral-900 truncate leading-tight">{{ auth()->user()->full_name ?? auth()->user()->name }}</p>
            <p class="text-[12px] text-neutral-500 truncate mt-0.5">{{ auth()->user()->role?->name ?? auth()->user()->email ?? 'Staff' }}</p>
        </div>
        <div :class="sidebarOpen ? 'block' : 'hidden'" class="flex-shrink-0 ml-2">
            <svg class="w-4 h-4 text-neutral-400" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <polyline points="17 10 12 15 7 10"></polyline>
            </svg>
        </div>
    </button>

    <flux:menu>
        <div class="flex items-center gap-2 px-1 py-1.5 text-start text-sm">
            <flux:avatar
                :name="auth()->user()->full_name ?? auth()->user()->name"
                :initials="auth()->user()->initials()"
            />
            <div class="grid flex-1 text-start text-sm leading-tight">
                <flux:heading class="truncate">{{ auth()->user()->full_name ?? auth()->user()->name }}</flux:heading>
                <flux:text class="truncate">{{ auth()->user()->role?->name ?? auth()->user()->email ?? 'Staff' }}</flux:text>
            </div>
        </div>
        <flux:menu.separator />
        <flux:menu.radio.group>
            <flux:menu.item :href="route('profile.edit')" icon="cog" wire:navigate>
                {{ __('Settings') }}
            </flux:menu.item>
            <form method="POST" action="{{ route('logout') }}" class="w-full">
                @csrf
                <flux:menu.item
                    as="button"
                    type="submit"
                    icon="arrow-right-start-on-rectangle"
                    class="w-full cursor-pointer"
                    data-test="logout-button"
                >
                    {{ __('Log out') }}
                </flux:menu.item>
            </form>
        </flux:menu.radio.group>
    </flux:menu>
</flux:dropdown>
