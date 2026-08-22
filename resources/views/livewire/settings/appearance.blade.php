<div class="flex flex-col flex-1 w-full max-w-5xl mx-auto">
    <x-settings.layout :heading="__('Tampilan')" :subheading="__('Pilih mode terang atau gelap untuk aplikasi.')">
        <flux:radio.group x-data variant="segmented" x-model="$flux.appearance">
            <flux:radio value="light" icon="sun">{{ __('Terang') }}</flux:radio>
            <flux:radio value="dark" icon="moon">{{ __('Gelap') }}</flux:radio>
            <flux:radio value="system" icon="computer-desktop">{{ __('Sistem') }}</flux:radio>
        </flux:radio.group>
    </x-settings.layout>
</section>
