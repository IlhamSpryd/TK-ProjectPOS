<section class="w-full">
    <x-settings.layout :heading="__('Keamanan')" :subheading="__('Kelola password dan pengaturan keamanan.')">
        <form method="POST" wire:submit="updatePassword" class="space-y-6">
            <div>
                <x-ui.input
                    name="current_password"
                    wire:model="current_password"
                    :label="__('Kata Sandi Saat Ini')"
                    type="password"
                    required
                    autocomplete="current-password"
                />
                <flux:error name="current_password" />
            </div>

            <div>
                <x-ui.input
                    name="password"
                    wire:model="password"
                    :label="__('Kata Sandi Baru')"
                    type="password"
                    required
                    autocomplete="new-password"
                />
                <flux:error name="password" />
            </div>

            <div>
                <x-ui.input
                    name="password_confirmation"
                    wire:model="password_confirmation"
                    :label="__('Konfirmasi Kata Sandi')"
                    type="password"
                    required
                    autocomplete="new-password"
                />
                <flux:error name="password_confirmation" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-ui.button variant="primary" type="submit" data-test="update-password-button">{{ __('Simpan Perubahan') }}</x-ui.button>
            </div>
        </form>
    </x-settings.layout>
</section>
