<section class="w-full">
    @include('partials.settings-heading')

    <x-settings.layout :heading="__('Profil')" :subheading="__('Perbarui nama dan alamat email Anda.')">
        <form wire:submit="updateProfileInformation" class="w-full space-y-6">
            <div>
                <x-ui.input name="name" wire:model="name" :label="__('Nama Lengkap')" type="text" required autofocus autocomplete="name" />
                <flux:error name="name" />
            </div>

            <div>
                <x-ui.input name="email" wire:model="email" :label="__('Alamat Email')" type="email" required autocomplete="email" />
                <flux:error name="email" />
            </div>

            <div class="flex items-center gap-4 pt-2">
                <x-ui.button variant="primary" type="submit">{{ __('Simpan Perubahan') }}</x-ui.button>
            </div>
        </form>
    </x-settings.layout>
</section>
