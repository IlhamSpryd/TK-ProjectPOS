<div class="flex flex-col h-full bg-neutral-100 overflow-hidden"
    x-data="{
        activeCategory: 'all',
        activeCategoryName: 'Semua Produk',
        showCheckoutModal: @entangle('showSuccessModal')
    }">

    {{-- ═══════════════════════════════════════════════════════
         TOP HEADER
    ═══════════════════════════════════════════════════════ --}}
    <header class="h-[60px] bg-white border-b border-neutral-200 flex items-center px-4 gap-4 shrink-0 z-10">

        {{-- Hamburger: mobile sidebar overlay --}}
        <button @click="sidebarOpen = true" aria-label="Buka menu"
            class="flex md:hidden p-1.5 rounded-md text-neutral-500 hover:bg-neutral-100 transition-colors focus:outline-none w-8 h-8 items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        {{-- Hamburger: desktop toggle sidebar collapse --}}
        <button @click="sidebarCollapsed = !sidebarCollapsed" aria-label="Toggle sidebar"
            class="hidden md:flex p-1.5 rounded-md text-neutral-500 hover:bg-neutral-100 transition-colors focus:outline-none w-8 h-8 items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>

        {{-- Category title (updates dynamically) --}}
        <h1 class="text-base font-bold text-neutral-900 shrink-0 min-w-[100px]" x-text="activeCategoryName"></h1>

        {{-- Search bar --}}
        <div class="flex-1 max-w-sm relative">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-neutral-400 pointer-events-none" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.15z"/>
            </svg>
            <input type="text" wire:model.live.debounce.300ms="search"
                placeholder="Cari produk..."
                class="w-full pl-9 pr-4 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-sm focus:outline-none focus:border-neutral-400 focus:bg-white transition-colors">
            <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2">
                <svg class="w-4 h-4 animate-spin text-neutral-400" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                </svg>
            </div>
        </div>

        {{-- Spacer --}}
        <div class="flex-1"></div>

        {{-- Right actions --}}
        <div class="flex items-center gap-2 shrink-0">
            {{-- Kasir info --}}
            <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-lg border border-neutral-200 bg-white hover:bg-neutral-50 cursor-default">
                <div class="w-6 h-6 rounded-full bg-neutral-900 flex items-center justify-center text-white text-xs font-bold shrink-0">
                    {{ substr(auth()->user()->name ?? 'K', 0, 1) }}
                </div>
                <span class="text-sm font-medium text-neutral-700">Kasir</span>
                <svg class="w-3.5 h-3.5 text-neutral-400" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7"/>
                </svg>
            </div>
        </div>
    </header>

    {{-- ═══════════════════════════════════════════════════════
         BODY
    ═══════════════════════════════════════════════════════ --}}
    <div class="flex flex-1 overflow-hidden">

        {{-- ══ LEFT: Vertical Category Sidebar ══ --}}
        <nav class="w-[52px] bg-neutral-900 flex flex-col items-center pt-2 pb-2 shrink-0 overflow-y-auto overflow-x-hidden z-10"
            style="scrollbar-width: none;">

            {{-- "Semua" --}}
            <button type="button"
                @click="activeCategory = 'all'; activeCategoryName = 'Semua Produk'"
                class="relative w-full flex items-center justify-center py-5 transition-colors focus:outline-none group"
                :class="activeCategory === 'all' ? 'text-white' : 'text-neutral-500 hover:text-neutral-300'">
                {{-- Active indicator bar --}}
                <span class="absolute left-0 top-3 bottom-3 w-[3px] bg-white rounded-r transition-opacity duration-200"
                    :class="activeCategory === 'all' ? 'opacity-100' : 'opacity-0'">
                </span>
                <span class="text-[11px] font-semibold tracking-widest uppercase"
                    style="writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg);">
                    Semua
                </span>
            </button>

            @if (isset($categories))
                @foreach ($categories as $category)
                    <button type="button"
                        @click="activeCategory = '{{ $category->id }}'; activeCategoryName = '{{ addslashes($category->name) }}'"
                        class="relative w-full flex items-center justify-center py-5 transition-colors focus:outline-none group"
                        :class="activeCategory === '{{ $category->id }}' ? 'text-white' : 'text-neutral-500 hover:text-neutral-300'">
                        <span class="absolute left-0 top-3 bottom-3 w-[3px] bg-white rounded-r transition-opacity duration-200"
                            :class="activeCategory === '{{ $category->id }}' ? 'opacity-100' : 'opacity-0'">
                        </span>
                        <span class="text-[11px] font-semibold tracking-wider whitespace-nowrap leading-none"
                            style="writing-mode: vertical-rl; text-orientation: mixed; transform: rotate(180deg);">
                            {{ $category->name }}
                        </span>
                    </button>
                @endforeach
            @endif
        </nav>

        {{-- ══ CENTER: Product Grid ══ --}}
        <main class="flex-1 overflow-y-auto bg-neutral-50 min-w-0">
            <div class="p-5">

                {{-- Product Grid --}}
                <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-3 lg:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4">
                    @foreach ($products as $variant)
                        @php
                            $stock      = $stockMap[$variant->id] ?? 0;
                            $hasStock   = $stock > 0;
                            $categoryId = $variant->product->category_id;
                        @endphp

                        <button type="button"
                            @if ($hasStock) wire:click="addToCart('{{ $variant->id }}')" @endif
                            x-show="activeCategory === 'all' || activeCategory === '{{ $categoryId }}'"
                            @if (!$hasStock) disabled @endif
                            class="group text-left bg-white rounded-2xl overflow-hidden border border-neutral-200/80 transition-all duration-200 focus:outline-none {{ $hasStock ? 'hover:shadow-lg hover:-translate-y-0.5 cursor-pointer focus-visible:ring-2 focus-visible:ring-neutral-400' : 'opacity-50 cursor-not-allowed' }}">

                            {{-- Product Image --}}
                            <div class="relative overflow-hidden bg-neutral-100" style="padding-top: 75%;">
                                @if ($variant->product->image_url)
                                    <img src="{{ Storage::url($variant->product->image_url) }}"
                                        alt="{{ $variant->product->name }}"
                                        class="absolute inset-0 w-full h-full object-cover transition-transform duration-300 {{ $hasStock ? 'group-hover:scale-105' : '' }}">
                                @else
                                    <div class="absolute inset-0 flex flex-col items-center justify-center bg-neutral-50">
                                        <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 12.75V18a.75.75 0 00.75.75h16.5A.75.75 0 0021 18v-5.25M16.5 6.75h.008v.008h-.008V6.75z"/>
                                        </svg>
                                    </div>
                                @endif

                                {{-- Stock habis --}}
                                @if (!$hasStock)
                                    <div class="absolute inset-0 bg-white/60 backdrop-blur-[1px] flex items-center justify-center">
                                        <span class="bg-neutral-800 text-white text-[11px] font-bold px-2.5 py-1 rounded-full uppercase tracking-wider">Habis</span>
                                    </div>
                                @endif

                                {{-- ⊕ Add button (decorative, whole card is clickable) --}}
                                @if ($hasStock)
                                    <span aria-hidden="true"
                                        class="absolute top-2.5 right-2.5 w-9 h-9 rounded-full bg-neutral-900 text-white flex items-center justify-center shadow-md
                                               transition-transform duration-200 group-hover:scale-110 group-hover:bg-black pointer-events-none">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                        </svg>
                                    </span>
                                @endif
                            </div>

                            {{-- Info --}}
                            <div class="px-3 py-3">
                                <h3 class="text-sm font-semibold text-neutral-800 line-clamp-1 leading-snug">
                                    {{ $variant->product->name }}
                                </h3>
                                @if ($variant->sku)
                                    <p class="text-[11px] text-neutral-400 font-mono mt-0.5">{{ $variant->sku }}</p>
                                @endif
                                <p class="text-sm font-bold text-neutral-900 mt-2">
                                    Rp {{ number_format($variant->selling_price, 0, ',', '.') }}
                                </p>
                            </div>
                        </button>
                    @endforeach
                </div>

                {{-- Empty state --}}
                @if (count($products) === 0)
                    <div class="flex flex-col items-center justify-center py-20 text-center">
                        <div class="w-16 h-16 rounded-full bg-neutral-100 flex items-center justify-center mb-4">
                            <svg class="w-7 h-7 text-neutral-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 1010.5 18a7.5 7.5 0 006.15-3.15z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-semibold text-neutral-500">Produk tidak ditemukan</p>
                        <p class="text-xs text-neutral-400 mt-1">Coba ubah kata kunci atau pilih kategori lain</p>
                    </div>
                @endif

                {{-- Pagination --}}
                @if (count($products) > 0)
                    <div class="mt-6 pt-4 border-t border-neutral-200">
                        {{ $products->links() }}
                    </div>
                @endif
            </div>
        </main>

        {{-- ══ RIGHT: Order Panel ══ --}}
        <aside class="w-[280px] xl:w-[300px] bg-white border-l border-neutral-200 flex flex-col shrink-0">

            {{-- Order Header --}}
            <div class="px-5 pt-5 pb-4 border-b border-neutral-100 shrink-0">
                <div class="flex items-center justify-between mb-3">
                    <h2 class="text-base font-bold text-neutral-900">Detail Order</h2>
                    <button wire:click="clearCart"
                        class="text-xs text-neutral-400 hover:text-danger-600 transition-colors font-medium">
                        Kosongkan
                    </button>
                </div>
                <select wire:model="customer_id"
                    class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-sm text-neutral-600 focus:outline-none focus:border-neutral-400 focus:bg-white transition-colors">
                    <option value="">Pilih Pelanggan (Opsional)</option>
                    @foreach ($this->customers as $c)
                        <option value="{{ $c->id }}">{{ $c->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Cart Items --}}
            <div class="flex-1 overflow-y-auto py-3 px-4 space-y-2.5" style="scrollbar-width: thin; scrollbar-color: #e5e5e5 transparent;">
                @if (count($cart) === 0)
                    <div class="h-full min-h-[180px] flex flex-col items-center justify-center text-center py-8">
                        <div class="w-12 h-12 rounded-full bg-neutral-50 border border-neutral-200 flex items-center justify-center mb-3">
                            <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 3h1.386c.51 0 .955.343 1.087.835l.383 1.437M7.5 14.25a3 3 0 00-3 3h15.75m-12.75-3h11.218c1.121-2.3 2.1-4.684 2.924-7.138a60.114 60.114 0 00-16.536-1.84M7.5 14.25L5.106 5.272M6 20.25a.75.75 0 11-1.5 0 .75.75 0 011.5 0zm12.75 0a.75.75 0 11-1.5 0 .75.75 0 011.5 0z"/>
                            </svg>
                        </div>
                        <p class="text-sm font-medium text-neutral-400">Keranjang kosong</p>
                        <p class="text-xs text-neutral-300 mt-1">Pilih produk dari menu</p>
                    </div>
                @else
                    @foreach ($cart as $variantId => $item)
                        <div class="flex items-start gap-3 group/item">
                            {{-- Thumbnail --}}
                            <div class="w-12 h-12 rounded-xl bg-neutral-100 overflow-hidden shrink-0 border border-neutral-100">
                                @if (isset($item['image_url']) && $item['image_url'])
                                    <img src="{{ Storage::url($item['image_url']) }}"
                                        alt="{{ $item['name'] }}" class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex items-center justify-center">
                                        <svg class="w-5 h-5 text-neutral-300" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 15.75l5.159-5.159a2.25 2.25 0 013.182 0l5.159 5.159m-1.5-1.5l1.409-1.409a2.25 2.25 0 013.182 0l2.909 2.909M3 12.75V18a.75.75 0 00.75.75h16.5A.75.75 0 0021 18v-5.25"/>
                                        </svg>
                                    </div>
                                @endif
                            </div>

                            {{-- Details --}}
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-1">
                                    <p class="text-sm font-semibold text-neutral-800 leading-tight line-clamp-1">{{ $item['name'] }}</p>
                                    <button wire:click="removeFromCart('{{ $variantId }}')"
                                        aria-label="Hapus"
                                        class="shrink-0 w-5 h-5 flex items-center justify-center text-neutral-300 hover:text-danger-500 transition-colors opacity-0 group-hover/item:opacity-100 focus:opacity-100 mt-0.5">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                                        </svg>
                                    </button>
                                </div>
                                <p class="text-xs text-neutral-400 font-medium mt-0.5">
                                    Rp {{ number_format($item['price'], 0, ',', '.') }}
                                </p>

                                <div class="flex items-center justify-between mt-2">
                                    {{-- Stepper --}}
                                    <div class="flex items-center gap-2">
                                        <button wire:click="decrementQuantity('{{ $variantId }}')" aria-label="Kurangi"
                                            class="w-6 h-6 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-500 hover:border-neutral-400 hover:bg-neutral-50 transition-colors focus:outline-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 12h14"/>
                                            </svg>
                                        </button>
                                        <span class="text-sm font-bold text-neutral-800 tabular-nums w-4 text-center">{{ $item['quantity'] }}</span>
                                        <button wire:click="incrementQuantity('{{ $variantId }}')" aria-label="Tambah"
                                            class="w-6 h-6 rounded-full border border-neutral-200 flex items-center justify-center text-neutral-500 hover:border-neutral-400 hover:bg-neutral-50 transition-colors focus:outline-none">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                                            </svg>
                                        </button>
                                    </div>

                                    {{-- Subtotal --}}
                                    <div class="text-right">
                                        <span class="text-sm font-bold text-neutral-900 tabular-nums">
                                            Rp {{ number_format($item['price'] * $item['quantity'] - $item['discount'], 0, ',', '.') }}
                                        </span>
                                        @if ($item['discount'] > 0)
                                            <span class="block text-[11px] text-danger-500 font-medium">
                                                -Rp {{ number_format($item['discount'], 0, ',', '.') }}
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Divider --}}
                        @if (!$loop->last)
                            <div class="h-px bg-neutral-100 mx-1"></div>
                        @endif
                    @endforeach
                @endif
            </div>

            {{-- ── Summary + Payment ── --}}
            <div class="border-t border-neutral-100 shrink-0">
                {{-- Price summary --}}
                <div class="px-5 py-4 space-y-2">
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Subtotal</span>
                        <span class="font-semibold text-neutral-800 tabular-nums">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Diskon</span>
                        <span class="font-semibold tabular-nums {{ $this->discountTotal > 0 ? 'text-danger-600' : 'text-neutral-800' }}">
                            {{ $this->discountTotal > 0 ? '-' : '' }}Rp {{ number_format($this->discountTotal, 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-neutral-500">Pajak / PPN</span>
                        <span class="font-semibold text-neutral-800 tabular-nums">Rp {{ number_format($this->taxTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="h-px bg-neutral-100 my-1"></div>
                    <div class="flex justify-between items-baseline">
                        <span class="text-sm font-bold text-neutral-900">Total</span>
                        <span class="text-lg font-bold text-neutral-900 tabular-nums">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                {{-- Payment section --}}
                <div class="px-4 pb-4 space-y-2.5">
                    <select wire:model.live="payment_method"
                        class="w-full px-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-sm text-neutral-700 focus:outline-none focus:border-neutral-400 transition-colors">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="card">Kartu Kredit / Debit</option>
                    </select>

                    @if ($payment_method === 'cash')
                        <div class="relative">
                            <span class="absolute left-3 top-1/2 -translate-y-1/2 text-sm text-neutral-400 font-medium">Rp</span>
                            <input type="number" wire:model.live.debounce.500ms="cash_received"
                                placeholder="Jumlah tunai"
                                class="w-full pl-9 pr-3 py-2 bg-neutral-50 border border-neutral-200 rounded-lg text-sm font-semibold focus:outline-none focus:border-neutral-400 focus:bg-white transition-colors tabular-nums">
                        </div>
                        @if ($cash_received > 0)
                            <div class="flex justify-between items-center text-sm px-3 py-2 rounded-lg border
                                {{ $change_amount >= 0 ? 'bg-success-50 border-success-500/20 text-success-700' : 'bg-danger-50 border-danger-500/20 text-danger-700' }}">
                                <span class="font-semibold">{{ $change_amount >= 0 ? 'Kembalian' : 'Kurang' }}</span>
                                <span class="font-bold tabular-nums">Rp {{ number_format(abs($change_amount), 0, ',', '.') }}</span>
                            </div>
                        @endif
                    @endif

                    @error('process')
                        <p class="text-xs text-danger-600 bg-danger-50 border border-danger-200 rounded-lg px-3 py-2">{{ $message }}</p>
                    @enderror
                    @error('cart')
                        <p class="text-xs text-danger-600 bg-danger-50 border border-danger-200 rounded-lg px-3 py-2">{{ $message }}</p>
                    @enderror

                    {{-- Print / Pay button --}}
                    <button wire:click="processPayment"
                        wire:loading.attr="disabled"
                        {{ count($cart) === 0 || ($cash_received < $this->grandTotal && $this->payment_method === 'cash') ? 'disabled' : '' }}
                        class="w-full py-3 rounded-xl bg-neutral-900 text-white text-sm font-bold
                               hover:bg-black disabled:opacity-40 disabled:cursor-not-allowed
                               transition-all duration-150 flex items-center justify-center gap-2
                               focus:outline-none focus-visible:ring-2 focus-visible:ring-neutral-400">
                        <span wire:loading.remove wire:target="processPayment">
                            <svg class="w-4 h-4 inline -mt-0.5 mr-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                            </svg>
                            Cetak Tagihan
                        </span>
                        <span wire:loading wire:target="processPayment" class="flex items-center gap-2">
                            <svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/>
                            </svg>
                            Memproses...
                        </span>
                    </button>
                </div>
            </div>
        </aside>
    </div>

    {{-- ═══════════════════════════════════════════════════════
         SUCCESS MODAL
    ═══════════════════════════════════════════════════════ --}}
    <x-ui.modal name="checkout-success" wire:model="showSuccessModal" maxWidth="md">
        <div class="p-6">
            {{-- Header --}}
            <div class="flex flex-col items-center text-center mb-6">
                <div class="w-16 h-16 rounded-full bg-success-50 border-4 border-white shadow flex items-center justify-center mb-4">
                    <svg class="w-8 h-8 text-success-500" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-xl font-bold text-neutral-900">Transaksi Berhasil</h3>
                <p class="text-sm text-neutral-500 mt-1">Pembayaran diterima. Struk siap dicetak.</p>
            </div>

            {{-- Receipt --}}
            <div class="bg-neutral-50 rounded-2xl p-4 mb-4 border border-neutral-200 space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-neutral-500">Total</span>
                    <span class="font-bold text-neutral-900 tabular-nums">Rp {{ number_format($lastSale?->grand_total ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-neutral-500">Tunai Diterima</span>
                    <span class="font-semibold text-neutral-700 tabular-nums">Rp {{ number_format($lastSale?->cash_received ?? 0, 0, ',', '.') }}</span>
                </div>
                <div class="h-px bg-neutral-200"></div>
                <div class="flex justify-between text-sm font-bold text-success-700">
                    <span>Kembalian</span>
                    <span class="tabular-nums">Rp {{ number_format($lastSale?->change_amount ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            {{-- Items --}}
            @if ($lastSale && $lastSale->items)
                <div class="bg-white rounded-xl border border-neutral-200 p-4 mb-5 max-h-48 overflow-y-auto">
                    <p class="text-xs font-bold text-neutral-400 uppercase tracking-wider mb-3">Item Pembelian</p>
                    <div class="space-y-2.5">
                        @foreach ($lastSale->items as $item)
                            <div class="flex justify-between items-start gap-3 text-sm">
                                <div class="min-w-0">
                                    <p class="font-semibold text-neutral-800 line-clamp-1">{{ $item->product_name }}</p>
                                    <p class="text-xs text-neutral-400 tabular-nums">{{ $item->quantity }} × Rp {{ number_format($item->unit_price, 0, ',', '.') }}</p>
                                </div>
                                <span class="font-bold text-neutral-800 tabular-nums shrink-0">
                                    Rp {{ number_format($item->unit_price * $item->quantity, 0, ',', '.') }}
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif

            {{-- Actions --}}
            <div class="flex flex-col gap-2.5">
                <button class="w-full py-3 bg-neutral-900 text-white font-bold rounded-xl hover:bg-black transition-colors flex items-center justify-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231c-.662 0-1.18-.568-1.12-1.227L6.34 18m11.318 0h1.091A2.25 2.25 0 0021 15.75V9.456c0-1.081-.768-2.015-1.837-2.175a48.055 48.055 0 00-1.913-.247M6.34 18H5.25A2.25 2.25 0 013 15.75V9.456c0-1.081.768-2.015 1.837-2.175a48.056 48.056 0 011.913-.247m10.5 0a48.536 48.536 0 00-10.5 0m10.5 0V3.375c0-.621-.504-1.125-1.125-1.125h-8.25c-.621 0-1.125.504-1.125 1.125v3.659M18 10.5h.008v.008H18V10.5zm-3 0h.008v.008H15V10.5z"/>
                    </svg>
                    Cetak Struk
                </button>
                <button wire:click="startNewTransaction"
                    class="w-full py-2.5 text-sm font-semibold text-neutral-500 hover:text-neutral-800 rounded-xl hover:bg-neutral-50 transition-colors">
                    Transaksi Baru →
                </button>
            </div>
        </div>
    </x-ui.modal>
</div>
