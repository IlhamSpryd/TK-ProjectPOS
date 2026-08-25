@props(['position' => 'bottom', 'align' => 'start'])

<flux:dropdown :position="$position" :align="$align">
    <button type="button" class="flex items-center h-12 w-full rounded-xl hover:bg-[#f0f4f9] transition-colors focus:outline-none focus-visible:ring-2 focus-visible:ring-primary-500 group"
            :class="(sidebarCollapsed && !sidebarOpen) ? 'justify-center w-12 p-0 mx-auto rounded-full' : 'px-2'">
        <div class="flex-shrink-0 w-8 h-8 rounded-full bg-neutral-200 flex items-center justify-center text-sm font-semibold text-neutral-600">
            {{ auth()->user()->initials() }}
        </div>
        <div :class="(sidebarOpen || !sidebarCollapsed) ? 'block' : 'hidden'" class="ml-3 flex-col flex-1 text-left overflow-hidden">
            <span class="text-sm font-medium text-neutral-900 leading-tight truncate">{{ auth()->user()->full_name ?? auth()->user()->name }}</span>
            <span class="text-[11px] font-medium text-neutral-500 truncate mt-0.5">{{ auth()->user()->role?->name ?? auth()->user()->email ?? 'Administrator' }}</span>
        </div>
        <div :class="(sidebarOpen || !sidebarCollapsed) ? 'block' : 'hidden'" class="ml-auto flex-shrink-0 text-neutral-400 group-hover:text-neutral-900 transition-colors">
            <flux:icon name="chevron-up-down" variant="outline" class="w-5 h-5 stroke-2" />
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
