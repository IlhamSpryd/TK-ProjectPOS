<div class="flex h-[calc(100vh-64px)] bg-zinc-50 dark:bg-zinc-950 border-t border-zinc-200 dark:border-zinc-800 font-sans">
    <!-- Left Column: Products -->
    <div class="flex-1 flex flex-col h-full overflow-hidden border-r border-zinc-200 dark:border-zinc-800">
        <!-- Header & Search -->
        <div class="p-6 bg-white dark:bg-zinc-900 border-b border-zinc-200 dark:border-zinc-800 flex-shrink-0 flex items-center justify-between gap-6">
            <flux:heading size="xl">{{ __('Kasir') }}</flux:heading>
            
            <div class="flex-1 max-w-md">
                <flux:input 
                    wire:model.live.debounce.300ms="search" 
                    icon="magnifying-glass" 
                    placeholder="Cari nama, SKU, atau barcode..." 
                    class="w-full"
                />
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-6 bg-zinc-50/50 dark:bg-zinc-950/50">
            @if(count($products) > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-6">
                    @foreach($products as $variant)
                        <div wire:click="addToCart('{{ $variant->id }}')" class="group relative bg-white dark:bg-zinc-900 rounded-2xl shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:shadow-[0_8px_30px_rgb(0,0,0,0.12)] cursor-pointer transition-all duration-300 overflow-hidden border border-zinc-100 dark:border-zinc-800 hover:-translate-y-1 hover:border-blue-200 dark:hover:border-blue-500/30 flex flex-col">
                            
                            <div class="h-44 bg-gradient-to-br from-zinc-100 to-zinc-200 dark:from-zinc-800 dark:to-zinc-900 w-full relative overflow-hidden">
                                @if($variant->product->image_url)
                                    <img src="{{ Storage::url($variant->product->image_url) }}" alt="{{ $variant->product->name }}" class="object-cover w-full h-full transition-transform duration-500 group-hover:scale-110">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                @else
                                    <div class="flex items-center justify-center h-full text-zinc-400 dark:text-zinc-500 transition-transform duration-500 group-hover:scale-110">
                                        <flux:icon.photo class="w-12 h-12 opacity-50" />
                                    </div>
                                @endif
                                
                                <div class="absolute top-3 right-3">
                                    <div class="bg-white/90 dark:bg-zinc-900/90 backdrop-blur-md text-zinc-800 dark:text-zinc-200 text-[10px] uppercase tracking-wider px-2 py-1 rounded-full font-bold shadow-xs">
                                        {{ $variant->sku ?: 'No SKU' }}
                                    </div>
                                </div>
                            </div>
                            
                            <div class="p-5 flex-1 flex flex-col justify-between gap-4">
                                <flux:heading size="sm" class="line-clamp-2 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $variant->product->name }}</flux:heading>
                                
                                <div class="flex flex-col gap-1.5">
                                    <div class="text-lg font-bold text-zinc-900 dark:text-white">
                                        Rp {{ number_format($variant->selling_price, 0, ',', '.') }}
                                    </div>
                                    <div class="flex items-center justify-between border-t border-zinc-100 dark:border-zinc-800/60 pt-2 mt-1">
                                        <span class="text-[11px] uppercase tracking-wider text-zinc-500 dark:text-zinc-400 font-medium">Stok</span>
                                        @php
                                            $store = Auth::user()->stores()->first();
                                            $stock = 0;
                                            if($store) {
                                                $stockModel = \App\Models\InventoryStock::where('store_id', $store->id)->where('variant_id', $variant->id)->first();
                                                $stock = $stockModel ? $stockModel->quantity : 0;
                                            }
                                        @endphp
                                        <span class="text-xs font-bold {{ $stock > 0 ? 'text-emerald-600 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-500' }} bg-{{ $stock > 0 ? 'emerald' : 'rose' }}-50 dark:bg-{{ $stock > 0 ? 'emerald' : 'rose' }}-500/10 px-2 py-0.5 rounded-md">
                                            {{ number_format($stock, 0) }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                            
                            <!-- Overlay add to cart icon -->
                            <div class="absolute bottom-5 right-5 w-8 h-8 rounded-full bg-blue-600 text-white flex items-center justify-center opacity-0 transform translate-y-2 group-hover:opacity-100 group-hover:translate-y-0 transition-all duration-300 shadow-lg">
                                <flux:icon.plus class="w-4 h-4" />
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-zinc-500">
                    <div class="w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800/50 flex items-center justify-center mb-6">
                        <flux:icon.magnifying-glass class="w-10 h-10 text-zinc-400" />
                    </div>
                    <flux:heading size="lg">Tidak ada produk yang ditemukan.</flux:heading>
                    <flux:subheading class="mt-2">Coba kata kunci lain atau ubah filter pencarian.</flux:subheading>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Cart -->
    <div class="w-[420px] shrink-0 flex flex-col bg-white dark:bg-zinc-900 z-20 h-full overflow-hidden border-l border-zinc-200 dark:border-zinc-800 shadow-xl shadow-black/5">
        <!-- Customer Selection -->
        <div class="p-5 border-b border-zinc-100 dark:border-zinc-800 bg-white dark:bg-zinc-900 shrink-0 space-y-4">
            <flux:select wire:model.live="customer_id" searchable placeholder="Pilih Pelanggan (Opsional)">
                <flux:select.option value="">Umum (Tanpa Nama)</flux:select.option>
                @foreach($customers as $customer)
                    <flux:select.option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</flux:select.option>
                @endforeach
            </flux:select>
        </div>

        @if(session()->has('error'))
            <div class="mx-5 mt-4 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 text-sm font-medium">
                {{ session('error') }}
            </div>
        @endif
        @error('process')
            <div class="mx-5 mt-4 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 text-sm font-medium">
                {{ $message }}
            </div>
        @enderror
        @error('cart')
             <div class="mx-5 mt-4 p-4 rounded-xl bg-red-50 dark:bg-red-500/10 border border-red-200 dark:border-red-500/20 text-red-600 dark:text-red-400 text-sm font-medium">
                {{ $message }}
            </div>
        @enderror

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-5 space-y-3 bg-zinc-50/30 dark:bg-zinc-950/30">
            @if(count($cart) > 0)
                <div class="space-y-3">
                    @foreach($cart as $variantId => $item)
                        <div class="group flex flex-col bg-white dark:bg-zinc-900 p-4 rounded-2xl shadow-sm border border-zinc-100 dark:border-zinc-800 hover:border-blue-200 dark:hover:border-blue-500/30 transition-all duration-300">
                            <div class="flex justify-between items-start mb-3">
                                <div class="pr-4">
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 leading-tight group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">{{ $item['name'] }}</h4>
                                    <p class="text-[13px] font-medium text-zinc-500 dark:text-zinc-400 mt-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <button wire:click="removeFromCart('{{ $variantId }}')" class="text-zinc-400 hover:text-red-500 transition-colors p-1.5 rounded-lg hover:bg-red-50 dark:hover:bg-red-500/10 shrink-0 opacity-0 group-hover:opacity-100">
                                    <flux:icon.trash class="w-4 h-4" />
                                </button>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <div class="flex items-center bg-zinc-100 dark:bg-zinc-800/80 rounded-xl p-1 border border-zinc-200/50 dark:border-zinc-700/50">
                                    <button wire:click="decrementQuantity('{{ $variantId }}')" class="w-8 h-8 flex items-center justify-center rounded-lg text-zinc-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-700 hover:shadow-xs transition-all">
                                        <flux:icon.minus class="w-3 h-3" />
                                    </button>
                                    <input type="number" wire:model.live.debounce.300ms="cart.{{ $variantId }}.quantity" class="w-10 h-8 text-center text-sm font-bold bg-transparent border-none focus:ring-0 text-zinc-900 dark:text-white p-0" min="1">
                                    <button wire:click="incrementQuantity('{{ $variantId }}')" class="w-8 h-8 flex items-center justify-center rounded-lg text-zinc-600 dark:text-zinc-300 hover:bg-white dark:hover:bg-zinc-700 hover:shadow-xs transition-all">
                                        <flux:icon.plus class="w-3 h-3" />
                                    </button>
                                </div>
                                <span class="font-black text-zinc-900 dark:text-zinc-100 text-[15px]">
                                    Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="h-full flex flex-col items-center justify-center text-zinc-400 space-y-4">
                    <div class="w-20 h-20 rounded-full bg-zinc-100 dark:bg-zinc-800/50 flex items-center justify-center">
                        <flux:icon.shopping-bag class="w-10 h-10 text-zinc-400" />
                    </div>
                    <div class="text-center">
                        <flux:heading size="md" class="text-zinc-500">Keranjang Kosong</flux:heading>
                        <flux:subheading class="mt-2 text-zinc-400">Pilih produk untuk ditambahkan.</flux:subheading>
                    </div>
                </div>
            @endif
        </div>

        <!-- Payment Info & Action -->
        <div class="bg-white dark:bg-zinc-900 border-t border-zinc-200 dark:border-zinc-800 p-6 shrink-0 flex flex-col gap-5 shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.05)] dark:shadow-[0_-4px_6px_-1px_rgb(0,0,0,0.2)] relative z-10">
            <div class="space-y-2.5">
                <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                    <span>Subtotal</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-200">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                </div>
                @if($this->discountTotal > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Diskon</span>
                    <span class="font-medium">- Rp {{ number_format($this->discountTotal, 0, ',', '.') }}</span>
                </div>
                @endif
                @if($this->taxTotal > 0)
                <div class="flex justify-between text-sm text-zinc-600 dark:text-zinc-400">
                    <span>Pajak</span>
                    <span class="font-medium text-zinc-900 dark:text-zinc-200">Rp {{ number_format($this->taxTotal, 0, ',', '.') }}</span>
                </div>
                @endif
                <div class="flex justify-between text-lg font-bold text-zinc-900 dark:text-white pt-3 border-t border-zinc-200 dark:border-zinc-800">
                    <span>Total</span>
                    <span class="text-blue-600 dark:text-blue-500">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="space-y-4">
                <flux:select wire:model.live="payment_method" label="Metode Pembayaran">
                    <flux:select.option value="cash">Tunai (Cash)</flux:select.option>
                    <flux:select.option value="qris">QRIS</flux:select.option>
                    <flux:select.option value="transfer">Transfer Bank</flux:select.option>
                    <flux:select.option value="debit">Kartu Debit</flux:select.option>
                    <flux:select.option value="credit">Kartu Kredit</flux:select.option>
                </flux:select>

                @if($payment_method === 'cash')
                <div class="space-y-3 p-4 bg-zinc-50 dark:bg-zinc-800/50 rounded-xl border border-zinc-200 dark:border-zinc-700/50">
                    <flux:input 
                        type="number" 
                        wire:model.live.debounce.500ms="cash_received" 
                        label="Diterima"
                        icon="banknotes"
                        placeholder="0"
                    />
                    
                    <div class="flex justify-between items-center text-sm pt-1">
                        <span class="text-zinc-600 dark:text-zinc-400">Kembalian</span>
                        <span class="font-bold text-lg {{ $change_amount > 0 ? 'text-green-600 dark:text-green-400' : 'text-zinc-900 dark:text-zinc-100' }}">
                            Rp {{ number_format($change_amount, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
                @endif
            </div>

            <div class="flex gap-3 pt-2">
                <flux:button wire:click="clearCart" variant="subtle" class="w-1/3">
                    Batal
                </flux:button>
                <flux:button 
                    wire:click="processPayment" 
                    variant="primary" 
                    class="w-2/3"
                    :disabled="empty($cart) || ($payment_method === 'cash' && $cash_received < $this->grandTotal)"
                >
                    Bayar Sekarang
                </flux:button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccessModal && $lastSale)
    <div 
        x-data="{ show: true }"
        x-show="show"
        x-transition:enter="transition ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 z-50 overflow-y-auto bg-zinc-900/60 backdrop-blur-md flex items-center justify-center p-4"
    >
        <div 
            x-show="show"
            x-transition:enter="transition ease-out duration-500 transform"
            x-transition:enter-start="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-90"
            x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave="transition ease-in duration-300 transform"
            x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
            x-transition:leave-end="opacity-0 translate-y-8 sm:translate-y-0 sm:scale-90"
            class="bg-white/80 dark:bg-zinc-900/80 backdrop-blur-xl rounded-3xl max-w-sm w-full mx-auto overflow-hidden shadow-[0_20px_60px_-15px_rgba(0,0,0,0.3)] border border-white/20 dark:border-zinc-700/30 relative"
        >
            <!-- Decorative blobs -->
            <div class="absolute -top-24 -right-24 w-48 h-48 bg-green-500/20 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-48 h-48 bg-blue-500/20 rounded-full blur-3xl"></div>

            <div class="p-8 relative z-10">
                <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-2xl bg-gradient-to-br from-green-400 to-green-600 shadow-lg shadow-green-500/30 mb-6 transform hover:scale-110 transition-transform duration-300">
                    <flux:icon.check class="h-10 w-10 text-white" />
                </div>
                
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-black text-zinc-900 dark:text-white tracking-tight">Transaksi Berhasil!</h2>
                    <p class="text-zinc-500 dark:text-zinc-400 mt-2 font-medium">No: {{ $lastSale->sale_number }}</p>
                </div>
                
                <div class="bg-white/50 dark:bg-zinc-800/50 backdrop-blur-md rounded-2xl p-5 mb-8 space-y-4 border border-white/50 dark:border-zinc-700/50 shadow-inner">
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400 font-medium">Total</span>
                        <span class="font-black text-lg text-zinc-900 dark:text-zinc-100">Rp {{ number_format($lastSale->grand_total, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-px bg-zinc-200/50 dark:bg-zinc-700/50 w-full"></div>
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400 font-medium">Metode</span>
                        <span class="font-bold text-zinc-900 dark:text-zinc-100 uppercase bg-zinc-100 dark:bg-zinc-700 px-2 py-1 rounded-md">{{ $lastSale->payments->first()->payment_method ?? 'CASH' }}</span>
                    </div>
                    @if(($lastSale->payments->first()->payment_method ?? 'cash') == 'cash')
                    <div class="flex justify-between items-center text-sm">
                        <span class="text-zinc-500 dark:text-zinc-400 font-medium">Kembalian</span>
                        <span class="font-bold text-green-600 dark:text-green-400">Rp {{ number_format($lastSale->payments->first()->change_amount ?? 0, 0, ',', '.') }}</span>
                    </div>
                    @endif
                </div>

                <div class="flex flex-col gap-3">
                    <flux:button variant="outline" class="w-full rounded-xl border-zinc-300 dark:border-zinc-600 font-semibold h-11">
                        Cetak Struk
                    </flux:button>
                    <flux:button wire:click="startNewTransaction" variant="primary" class="w-full rounded-xl shadow-lg shadow-blue-500/25 font-semibold h-11">
                        Transaksi Baru
                    </flux:button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
