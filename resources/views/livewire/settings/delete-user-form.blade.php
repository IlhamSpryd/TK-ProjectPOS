<section class="mt-12 pt-8 border-t border-neutral-200">
    <div class="mb-5">
        <h3 class="text-h3 font-bold text-danger-600">{{ __('Hapus Akun') }}</h3>
        <p class="text-body-sm text-neutral-500 mt-1">{{ __('Hapus akun Anda secara permanen dan semua data yang terkait.') }}</p>
    </div>

    <x-ui.button variant="danger" x-data="" x-on:click.prevent="window.dispatchEvent(new CustomEvent('open-modal', { detail: 'confirm-user-deletion' }))">
        {{ __('Hapus Akun') }}
    </x-ui.button>

    <x-ui.modal name="confirm-user-deletion" maxWidth="md">
        <form method="POST" wire:submit="deleteUser">
            <div class="mb-6 border-b border-neutral-100 pb-4">
                <h2 class="text-h2 font-bold text-neutral-900 tracking-tight">{{ __('Konfirmasi Penghapusan') }}</h2>
                <p class="text-body text-neutral-500 mt-1">
                    {{ __('Semua data yang terkait dengan akun Anda akan dihapus secara permanen. Masukkan kata sandi Anda untuk melanjutkan.') }}
                </p>
            </div>

            <div class="space-y-5 my-4">
                <div>
                    <x-ui.input name="password" wire:model="password" :label="__('Kata Sandi')" type="password" />
                    <flux:error name="password" />
                </div>
            </div>

            <x-slot:footer>
                <div class="flex justify-end gap-3 w-full">
                    <x-ui.button variant="ghost" x-on:click="window.dispatchEvent(new CustomEvent('close-modal', { detail: 'confirm-user-deletion' }))">
                        {{ __('Batal') }}
                    </x-ui.button>
                    <x-ui.button variant="danger" type="submit">
                        {{ __('Hapus Akun') }}
                    </x-ui.button>
                </div>
            </x-slot:footer>
        </form>
    </x-ui.modal>
</section>
