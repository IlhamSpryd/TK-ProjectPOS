<div class="flex h-screen bg-gray-100 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-800">
    <!-- Left Column: Products -->
    <div class="flex-1 flex flex-col h-full overflow-hidden border-r border-gray-200 dark:border-gray-700">
        <!-- Header & Search -->
        <div class="p-4 bg-white dark:bg-gray-800 shadow-sm z-10 flex-shrink-0">
            <h1 class="text-xl font-bold text-gray-800 dark:text-white mb-4">Kasir</h1>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="h-5 w-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input wire:model.live.debounce.300ms="search" type="text" class="block w-full pl-10 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md leading-5 bg-white dark:bg-gray-700 text-gray-900 dark:text-white placeholder-gray-500 focus:outline-none focus:ring-2 focus:ring-blue-500 sm:text-sm" placeholder="Cari nama, SKU, atau barcode...">
            </div>
        </div>

        <!-- Product Grid -->
        <div class="flex-1 overflow-y-auto p-4 bg-gray-50 dark:bg-gray-900">
            @if(count($products) > 0)
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-4">
                    @foreach($products as $variant)
                        <div wire:click="addToCart('{{ $variant->id }}')" class="bg-white dark:bg-gray-800 rounded-lg shadow-sm hover:shadow-md cursor-pointer transition-shadow duration-200 overflow-hidden border border-gray-100 dark:border-gray-700 flex flex-col">
                            <div class="h-32 bg-gray-200 dark:bg-gray-700 w-full relative">
                                @if($variant->product->image_url)
                                    <img src="{{ Storage::url($variant->product->image_url) }}" alt="{{ $variant->product->name }}" class="object-cover w-full h-full">
                                @else
                                    <div class="flex items-center justify-center h-full text-gray-400">
                                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                <div class="absolute bottom-0 left-0 bg-blue-600 text-white text-xs px-2 py-1 bg-opacity-80">
                                    {{ $variant->sku }}
                                </div>
                            </div>
                            <div class="p-3 flex-1 flex flex-col justify-between">
                                <h3 class="text-sm font-semibold text-gray-800 dark:text-gray-100 leading-tight mb-1 line-clamp-2">{{ $variant->product->name }}</h3>
                                <div>
                                    <p class="text-xs text-gray-500 dark:text-gray-400 mb-1">Stok: 
                                        @php
                                            $store = Auth::user()->stores()->first();
                                            $stock = 0;
                                            if($store) {
                                                $stockModel = \App\Models\InventoryStock::where('store_id', $store->id)->where('variant_id', $variant->id)->first();
                                                $stock = $stockModel ? $stockModel->quantity : 0;
                                            }
                                        @endphp
                                        <span class="{{ $stock > 0 ? 'text-green-600 dark:text-green-400' : 'text-red-500' }} font-medium">{{ number_format($stock, 0) }}</span>
                                    </p>
                                    <p class="text-blue-600 dark:text-blue-400 font-bold">Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</p>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="flex flex-col items-center justify-center h-full text-gray-500">
                    <svg class="w-16 h-16 mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    <p class="text-lg">Tidak ada produk yang ditemukan.</p>
                </div>
            @endif
        </div>
    </div>

    <!-- Right Column: Cart -->
    <div class="w-96 min-w-96 flex flex-col bg-white dark:bg-gray-800 shadow-xl z-20 h-full overflow-hidden shrink-0">
        <!-- Customer Selection -->
        <div class="p-4 border-b border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-850 flex-shrink-0">
            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Pelanggan (Opsional)</label>
            <select wire:model.live="customer_id" class="block w-full border-gray-300 dark:border-gray-600 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500 sm:text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                <option value="">-- Pilih Pelanggan --</option>
                @foreach($customers as $customer)
                    <option value="{{ $customer->id }}">{{ $customer->name }} ({{ $customer->phone }})</option>
                @endforeach
            </select>
        </div>

        @if(session()->has('error'))
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mx-4 mt-2 text-sm">
                {{ session('error') }}
            </div>
        @endif
        @error('process')
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mx-4 mt-2 text-sm">
                {{ $message }}
            </div>
        @enderror
        @error('cart')
             <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-2 mx-4 mt-2 text-sm">
                {{ $message }}
            </div>
        @enderror

        <!-- Cart Items -->
        <div class="flex-1 overflow-y-auto p-4">
            @if(count($cart) > 0)
                <ul class="space-y-3">
                    @foreach($cart as $variantId => $item)
                        <li class="flex flex-col bg-gray-50 dark:bg-gray-750 p-3 rounded-lg border border-gray-200 dark:border-gray-600">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="text-sm font-medium text-gray-800 dark:text-white">{{ $item['name'] }}</h4>
                                    <p class="text-xs text-gray-500 dark:text-gray-400">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                </div>
                                <button wire:click="removeFromCart('{{ $variantId }}')" class="text-red-500 hover:text-red-700 transition">
                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </div>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center border border-gray-300 dark:border-gray-500 rounded-md h-8 w-24 overflow-hidden">
                                    <button wire:click="decrementQuantity('{{ $variantId }}')" class="w-8 h-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-600 dark:text-gray-300 flex items-center justify-center font-bold">-</button>
                                    <input type="number" wire:model.live.debounce.300ms="cart.{{ $variantId }}.quantity" class="w-8 h-full text-center text-sm border-none bg-white dark:bg-gray-800 text-gray-900 dark:text-white p-0 focus:ring-0" min="1">
                                    <button wire:click="incrementQuantity('{{ $variantId }}')" class="w-8 h-full bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 text-gray-600 dark:text-gray-300 flex items-center justify-center font-bold">+</button>
                                </div>
                                <span class="font-bold text-gray-800 dark:text-white">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</span>
                            </div>
                        </li>
                    @endforeach
                </ul>
            @else
                <div class="h-full flex flex-col items-center justify-center text-gray-400">
                    <svg class="w-12 h-12 mb-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    <p class="text-sm">Keranjang Kosong</p>
                </div>
            @endif
        </div>

        <!-- Payment Info & Action -->
        <div class="bg-gray-50 dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700 p-4 space-y-3 flex-shrink-0">
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>Subtotal</span>
                <span>Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
            </div>
            @if($this->discountTotal > 0)
            <div class="flex justify-between text-sm text-green-600">
                <span>Diskon</span>
                <span>- Rp {{ number_format($this->discountTotal, 0, ',', '.') }}</span>
            </div>
            @endif
            @if($this->taxTotal > 0)
            <div class="flex justify-between text-sm text-gray-600 dark:text-gray-400">
                <span>Pajak (PPN/PBJT)</span>
                <span>Rp {{ number_format($this->taxTotal, 0, ',', '.') }}</span>
            </div>
            @endif
            <div class="flex justify-between text-lg font-bold text-gray-900 dark:text-white pt-2 border-t border-gray-200 dark:border-gray-700 mt-2">
                <span>Total</span>
                <span>Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
            </div>

            <div class="mt-4">
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Metode Pembayaran</label>
                <select wire:model.live="payment_method" class="block w-full border-gray-300 dark:border-gray-600 rounded-md focus:ring-blue-500 focus:border-blue-500 text-sm bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                    <option value="cash">Tunai (Cash)</option>
                    <option value="qris">QRIS</option>
                    <option value="transfer">Transfer Bank</option>
                    <option value="debit">Kartu Debit</option>
                    <option value="credit">Kartu Kredit</option>
                </select>
            </div>

            @if($payment_method === 'cash')
            <div>
                <label class="block text-xs font-medium text-gray-700 dark:text-gray-300 mb-1">Diterima</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <span class="text-gray-500 sm:text-sm">Rp</span>
                    </div>
                    <input type="number" wire:model.live.debounce.500ms="cash_received" class="block w-full pl-9 pr-3 py-2 border border-gray-300 dark:border-gray-600 rounded-md bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                
                <div class="flex justify-between mt-2 text-sm">
                    <span class="text-gray-600 dark:text-gray-400">Kembalian:</span>
                    <span class="font-bold {{ $change_amount > 0 ? 'text-blue-600 dark:text-blue-400' : 'text-gray-900 dark:text-white' }}">Rp {{ number_format($change_amount, 0, ',', '.') }}</span>
                </div>
            </div>
            @endif

            <div class="flex space-x-2 pt-2">
                <button wire:click="clearCart" class="px-3 py-2 bg-red-100 hover:bg-red-200 text-red-700 rounded-md transition text-sm flex-shrink-0">
                    Batal
                </button>
                <button wire:click="processPayment" 
                        class="flex-1 px-4 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-md shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed text-center"
                        @if(empty($cart) || ($payment_method === 'cash' && $cash_received < $this->grandTotal)) disabled @endif>
                    Bayar Sekarang
                </button>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    @if($showSuccessModal && $lastSale)
    <div class="fixed inset-0 z-50 overflow-y-auto bg-gray-900 bg-opacity-75 flex items-center justify-center fade-in">
        <div class="bg-white dark:bg-gray-800 rounded-lg max-w-sm w-full mx-4 overflow-hidden shadow-2xl p-6">
            <div class="text-center mb-4">
                <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                    <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                    </svg>
                </div>
                <h3 class="text-lg leading-6 font-bold text-gray-900 dark:text-white">Transaksi Berhasil!</h3>
                <p class="text-sm text-gray-500 dark:text-gray-400 mt-1">No: {{ $lastSale->sale_number }}</p>
            </div>
            
            <div class="border-t border-b border-gray-200 dark:border-gray-700 py-3 mb-4 space-y-2">
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Total Transaksi:</span>
                    <span class="font-bold text-gray-900 dark:text-white">Rp {{ number_format($lastSale->grand_total, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Metode Pembayaran:</span>
                    <span class="uppercase">{{ $lastSale->payments->first()->payment_method ?? 'CASH' }}</span>
                </div>
                @if(($lastSale->payments->first()->payment_method ?? 'cash') == 'cash')
                <div class="flex justify-between text-sm text-gray-600 dark:text-gray-300">
                    <span>Kembalian:</span>
                    <span>Rp {{ number_format($lastSale->payments->first()->change_amount ?? 0, 0, ',', '.') }}</span>
                </div>
                @endif
            </div>

            <div class="flex flex-col space-y-2">
                <button class="w-full justify-center rounded-md border border-gray-300 dark:border-gray-600 shadow-sm px-4 py-2 bg-white dark:bg-gray-700 text-base font-medium text-gray-700 dark:text-gray-200 hover:bg-gray-50 dark:hover:bg-gray-600 sm:text-sm">
                    Cetak Struk
                </button>
                <button wire:click="startNewTransaction" class="w-full justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:text-sm">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
    @endif
    
    <style>
        .fade-in { animation: fadeIn 0.2s ease-out; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
    </style>
</div>
