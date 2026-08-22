<div class="flex flex-col h-full bg-white overflow-hidden text-neutral-800" x-data="{ 
    activeCategory: 'all', 
    showCheckoutModal: @entangle('showSuccessModal')
}">
    <!-- Header -->
    <header class="flex items-center justify-between px-5 py-3 border-b border-neutral-200 bg-white shrink-0 h-[60px]">
        <div class="flex items-center gap-3">
            <h1 class="text-[15px] font-semibold text-neutral-800">Buat Pesanan</h1>
            <div class="h-4 w-px bg-neutral-300"></div>
            <span class="text-[12px] font-medium text-neutral-600 flex items-center gap-1.5">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                Kasir Aktif
            </span>
        </div>
        <div class="flex items-center gap-3">
            <button wire:click="clearCart" class="text-[12px] font-medium text-neutral-600 hover:text-neutral-800 border border-transparent hover:border-neutral-200 px-3 py-1.5 rounded-md hover:bg-neutral-50 transition-colors flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                Kosongkan Keranjang
            </button>
        </div>
    </header>

    <!-- Main Content -->
    <div class="flex flex-col lg:flex-row flex-1 overflow-hidden w-full relative">
        
        <!-- Left Panel: Products -->
        <main class="flex-1 flex flex-col min-w-0 border-r border-neutral-200 bg-white">
            
            <!-- Top Bar: Search & Filter -->
            <div class="p-4 border-b border-neutral-200 space-y-3 shrink-0 bg-white shadow-[0_4px_10px_rgba(0,0,0,0.01)] z-10">
                <!-- Search -->
                <div class="relative w-full max-w-lg">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    <input type="text" wire:model.live.debounce.300ms="search" placeholder="Cari produk (Nama atau SKU)..." class="w-full pl-9 pr-4 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-[13px] font-medium focus:outline-none focus:border-neutral-400 focus:bg-white transition-colors">
                    
                    <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400">
                        <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    </div>
                </div>

                <!-- Categories -->
                <div class="flex gap-1 overflow-x-auto pb-1 scrollbar-hide items-center">
                    <button @click="activeCategory = 'all'" :class="activeCategory === 'all' ? 'bg-neutral-800 text-white font-medium shadow-sm' : 'text-neutral-600 hover:bg-neutral-100 bg-white border border-transparent font-medium'" class="px-3 py-1.5 rounded-md text-[13px] whitespace-nowrap transition-colors">Semua</button>
                    @if(isset($categories))
                        @foreach($categories as $category)
                            <button @click="activeCategory = '{{ $category->id }}'" :class="activeCategory === '{{ $category->id }}' ? 'bg-neutral-800 text-white font-medium shadow-sm' : 'text-neutral-600 hover:bg-neutral-100 bg-white border border-transparent font-medium'" class="px-3 py-1.5 rounded-md text-[13px] whitespace-nowrap transition-colors">{{ $category->name }}</button>
                        @endforeach
                    @endif
                </div>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto p-4 bg-neutral-50/50">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 xl:grid-cols-5 gap-3">
                    @foreach($products as $variant)
                        @php
                            $stock = $stockMap[$variant->id] ?? 0;
                            $hasStock = $stock > 0;
                            $categoryId = $variant->product->category_id;
                        @endphp
                        
                        <button type="button" wire:click="addToCart('{{ $variant->id }}')" 
                                x-show="activeCategory === 'all' || activeCategory === '{{ $categoryId }}'"
                                {{ !$hasStock ? 'disabled' : '' }}
                                class="flex flex-col text-left bg-white border border-neutral-200 rounded-md overflow-hidden hover:border-neutral-300 hover:shadow-sm transition-all focus:outline-none focus:ring-1 focus:ring-neutral-400 {{ !$hasStock ? 'opacity-50 grayscale cursor-not-allowed' : '' }}">
                            
                            <!-- Image Container -->
                            <div class="relative w-full overflow-hidden bg-neutral-100 border-b border-neutral-100" style="padding-top: 75%;">
                                @if($variant->product->image_url)
                                    <img src="{{ Storage::url($variant->product->image_url) }}" alt="{{ $variant->product->name }}" class="absolute inset-0 w-full h-full object-cover">
                                @else
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <svg style="width: 2rem; height: 2rem;" class="text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
                                @if(!$hasStock)
                                    <div class="absolute inset-0 bg-white/70 backdrop-blur-[1px] flex items-center justify-center">
                                        <span class="bg-neutral-800 text-white text-[10px] font-medium px-1.5 py-0.5 rounded shadow-sm">Habis</span>
                                    </div>
                                @endif
                            </div>

                            <!-- Details -->
                            <div class="p-2.5 flex flex-col flex-1 bg-white relative">
                                <h3 class="text-[13px] font-medium text-neutral-800 leading-snug line-clamp-2 mb-1">{{ $variant->product->name }} {{ $variant->sku }}</h3>
                                <div class="mt-auto pt-1.5 flex items-center justify-between">
                                    <p class="text-[13px] font-semibold text-neutral-900">Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</p>
                                    
                                    <div class="h-6 w-6 rounded border border-neutral-200 flex items-center justify-center {{ $hasStock ? 'bg-neutral-50 text-neutral-600' : 'bg-transparent text-neutral-300' }}">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
                
                @if(count($products) == 0)
                    <div class="flex flex-col items-center justify-center h-full text-neutral-400 py-10">
                        <x-ui.empty-state icon="cube" title="Tidak ada produk" description="Coba ubah kata kunci pencarian atau kategori." />
                    </div>
                @endif
                @if(count($products) > 0)
                    <div class="mt-5 pt-3 border-t border-neutral-200">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </main>

        <!-- Right Panel: Cart -->
        <aside class="w-full lg:w-[280px] xl:w-[320px] flex flex-col bg-white shrink-0 relative border-t lg:border-t-0 border-neutral-200 z-20 shadow-[-4px_0_24px_rgba(0,0,0,0.02)]">
            
            <div class="p-4 border-b border-neutral-200 shrink-0 bg-white">
                <div class="flex items-center justify-between mb-2.5">
                    <h2 class="text-[14px] font-semibold text-neutral-800">Detail Pesanan</h2>
                    <span class="bg-neutral-100 text-neutral-700 text-[11px] font-medium px-2 py-0.5 rounded border border-neutral-200">
                        {{ count($cart) }} Item
                    </span>
                </div>
                <select wire:model.live="customer_id" class="w-full px-2.5 py-1.5 bg-neutral-50 border border-neutral-200 rounded-md text-[13px] font-medium focus:outline-none focus:border-neutral-400">
                    <option value="">Pilih Pelanggan (Opsional)</option>
                    @foreach($customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Cart Items -->
            <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-neutral-50/30">
                @if(count($cart) === 0)
                    <div class="h-full flex flex-col items-center justify-center text-neutral-400">
                        <svg class="w-8 h-8 mb-2 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        <p class="text-[13px] font-medium">Keranjang kosong</p>
                    </div>
                @else
                    @foreach($cart as $variantId => $item)
                        <div class="group flex flex-col p-2.5 bg-white border border-neutral-200 rounded-md shadow-sm hover:border-neutral-300 transition-colors relative">
                            <!-- Delete Button (Hover) -->
                            <button wire:click="removeFromCart('{{ $variantId }}')" class="absolute -top-1.5 -right-1.5 w-5 h-5 bg-white border border-neutral-200 rounded flex items-center justify-center text-neutral-400 hover:text-rose-500 hover:border-rose-200 md:opacity-0 md:group-hover:opacity-100 transition-opacity shadow-sm z-10 focus:opacity-100">
                                <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>

                            <div class="flex justify-between items-start mb-2">
                                <div class="flex items-center gap-2 pr-2">
                                    @if(isset($item['image_url']) && $item['image_url'])
                                        <div class="w-10 h-10 rounded bg-neutral-100 flex-shrink-0 overflow-hidden border border-neutral-200">
                                            <img src="{{ Storage::url($item['image_url']) }}" alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                        </div>
                                    @endif
                                    <div>
                                        <h4 class="text-[13px] font-medium text-neutral-800 leading-snug">{{ $item['name'] }}</h4>
                                        <p class="text-[12px] font-medium text-neutral-500 mt-0.5">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="flex items-center justify-between mt-auto pt-2 border-t border-neutral-100">
                                <!-- Qty Controls -->
                                <div class="flex items-center border border-neutral-200 rounded overflow-hidden bg-neutral-50">
                                    <button wire:click="decrementQuantity('{{ $variantId }}')" class="px-2 py-0.5 text-neutral-600 hover:bg-white hover:text-neutral-800">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"></path></svg>
                                    </button>
                                    <span class="px-1.5 py-0.5 text-[12px] font-medium text-neutral-800 border-x border-neutral-200 bg-white min-w-[1.75rem] text-center">{{ $item['quantity'] }}</span>
                                    <button wire:click="incrementQuantity('{{ $variantId }}')" class="px-2 py-0.5 text-neutral-600 hover:bg-white hover:text-neutral-800">
                                        <svg style="width: 12px; height: 12px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"></path></svg>
                                    </button>
                                </div>
                                
                                <div class="flex flex-col items-end">
                                    <span class="text-[13px] font-semibold text-neutral-900">Rp {{ number_format(($item['price'] * $item['quantity']) - $item['discount'], 0, ',', '.') }}</span>
                                    @if($item['discount'] > 0)
                                        <span class="text-[10px] text-rose-500 font-medium mt-0.5">Diskon Rp {{ number_format($item['discount'], 0, ',', '.') }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @endif
            </div>

            <!-- Checkout Panel -->
            <div class="p-4 border-t border-neutral-200 bg-white shrink-0 shadow-[0_-4px_10px_rgba(0,0,0,0.02)]">
                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-[13px]">
                        <span class="text-neutral-600 font-medium">Subtotal</span>
                        <span class="font-medium text-neutral-800">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[13px]">
                        <span class="text-neutral-600 font-medium">Diskon</span>
                        <span class="font-medium text-rose-600">- Rp {{ number_format($this->discountTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-[13px] pb-2 border-b border-neutral-100">
                        <span class="text-neutral-600 font-medium">Pajak / PPN</span>
                        <span class="font-medium text-neutral-800">Rp {{ number_format($this->taxTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-end pt-1">
                        <span class="font-medium text-neutral-800 text-[13px]">Total</span>
                        <span class="text-[16px] font-semibold text-neutral-900 leading-none">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-[11px] font-medium text-neutral-500 mb-1.5">Metode Pembayaran</label>
                    <select wire:model.live="payment_method" class="w-full px-2.5 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-[13px] font-medium focus:outline-none focus:border-neutral-400 focus:bg-white mb-2.5 transition-colors shadow-sm">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="card">Kartu Kredit / Debit</option>
                    </select>

                    @if($payment_method === 'cash')
                        <label class="block text-[11px] font-medium text-neutral-500 mb-1.5">Tunai Diterima</label>
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-neutral-500 text-[13px] font-medium">Rp</span>
                            <input type="number" wire:model.live.debounce.500ms="cash_received" class="w-full pl-9 pr-2.5 py-2 bg-neutral-50 border border-neutral-200 rounded-md text-[14px] font-semibold focus:outline-none focus:border-neutral-400 focus:bg-white transition-colors shadow-sm" placeholder="0">
                        </div>
                        @if($cash_received > 0)
                            <div class="mt-2 flex justify-between text-[12px] px-2.5 py-1.5 rounded border {{ $change_amount >= 0 ? 'bg-emerald-50 border-emerald-100 text-emerald-800' : 'bg-rose-50 border-rose-100 text-rose-800' }}">
                                <span class="font-medium">{{ $change_amount >= 0 ? 'Kembalian' : 'Kurang' }}</span>
                                <span class="font-semibold">Rp {{ number_format(abs($change_amount), 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endif
                </div>

                <button wire:click="processPayment" 
                        wire:loading.attr="disabled"
                        {{ count($cart) === 0 || ($cash_received < $this->grandTotal && $this->payment_method === 'cash') ? 'disabled' : '' }}
                        class="w-full py-2 bg-neutral-800 text-white rounded-md text-[13px] font-medium hover:bg-neutral-900 disabled:opacity-50 transition-colors flex justify-center items-center gap-1.5 focus:outline-none focus:ring-1 focus:ring-neutral-400">
                    <span wire:loading.remove wire:target="processPayment">Proses Transaksi</span>
                    <span wire:loading wire:target="processPayment" class="flex items-center gap-1.5">
                        <svg class="w-3.5 h-3.5 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Memproses...
                    </span>
                </button>
            </div>
        </aside>
    </div>

    <!-- Checkout Success Modal -->
    <x-ui.modal name="checkout-success" wire:model="showSuccessModal" maxWidth="md">
        <div class="p-6 text-center">
            <div class="w-16 h-16 bg-success-100 rounded-full flex items-center justify-center mx-auto mb-4 border-4 border-success-50">
                <svg class="w-8 h-8 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-h3 font-bold text-neutral-900 mb-2">Transaksi Berhasil!</h3>
            <p class="text-body text-neutral-500 mb-6">Pembayaran telah diterima dan struk siap dicetak.</p>
            
            <div class="bg-neutral-50 rounded-xl p-4 mb-6 border border-neutral-100">
                <div class="flex justify-between items-center mb-2">
                    <span class="text-body-sm text-neutral-500 font-medium">Total Tagihan</span>
                    <span class="font-bold text-neutral-900">Rp {{ number_format($lastSale?->grand_total ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between items-center mb-2">
                    <span class="text-body-sm text-neutral-500 font-medium">Tunai Diterima</span>
                    <span class="font-medium text-neutral-900">Rp {{ number_format($lastSale?->cash_received ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="w-full h-px bg-neutral-200 my-2 border-dashed"></div>
                <div class="flex justify-between items-center text-success-600 font-bold">
                    <span>Kembalian</span>
                    <span>Rp {{ number_format($lastSale?->change_amount ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <div class="flex flex-col gap-3">
                <x-ui.button variant="primary" class="w-full justify-center">
                    Cetak Struk
                </x-ui.button>
                <x-ui.button variant="ghost" class="w-full justify-center text-neutral-600 hover:text-neutral-900" wire:click="startNewTransaction">
                    Transaksi Baru
                </x-ui.button>
            </div>
        </div>
    </x-ui.modal>
</div>
