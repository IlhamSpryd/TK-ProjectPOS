<div class="py-6 max-w-2xl mx-auto">

    <x-ui.card class="mt-6">
        <form wire:submit.prevent="save" class="space-y-6">
            
            <div>
                <x-ui.label for="variant_id" class="mb-1.5">Produk Varian <span class="text-danger-500">*</span></x-ui.label>
                <select id="variant_id" wire:model="variant_id" class="bg-white border border-neutral-200 text-neutral-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                    <option value="">-- Pilih Produk --</option>
                    @foreach($variants as $variant)
                        <option value="{{ $variant->id }}">{{ $variant->product->name ?? 'Unknown' }} {{ $variant->sku ? '('.$variant->sku.')' : '' }}</option>
                    @endforeach
                </select>
                @error('variant_id') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-ui.label for="movement_type" class="mb-1.5">Tipe Pergerakan <span class="text-danger-500">*</span></x-ui.label>
                <select id="movement_type" wire:model="movement_type" class="bg-white border border-neutral-200 text-neutral-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5">
                    <option value="adjustment_in">Barang Masuk (Adjustment IN)</option>
                    <option value="adjustment_out">Barang Keluar (Adjustment OUT)</option>
                    <option value="write_off">Pemusnahan / Hilang (Write Off)</option>
                </select>
                @error('movement_type') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-ui.label for="quantity" class="mb-1.5">Jumlah (Qty) <span class="text-danger-500">*</span></x-ui.label>
                <x-ui.input id="quantity" type="number" wire:model="quantity" step="0.01" min="0.01" placeholder="Masukkan jumlah barang" />
                @error('quantity') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
            </div>

            <div>
                <x-ui.label for="note" class="mb-1.5">Catatan / Keterangan</x-ui.label>
                <textarea id="note" wire:model="note" rows="3" class="bg-white border border-neutral-200 text-neutral-900 text-sm rounded-lg focus:ring-primary-500 focus:border-primary-500 block w-full p-2.5" placeholder="Alasan penyesuaian atau detail referensi..."></textarea>
                @error('note') <p class="mt-1 text-sm text-danger-600">{{ $message }}</p> @enderror
            </div>

            <div class="flex justify-end pt-4 border-t border-neutral-100">
                <x-ui.button type="submit" variant="primary" icon="check">Simpan</x-ui.button>
            </div>
        </form>
    </x-ui.card>
</div>
