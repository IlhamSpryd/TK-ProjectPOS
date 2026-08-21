<div class="bg-white flex flex-col h-full overflow-hidden" x-data="{ 
    activeCategory: 'all', 
    showCheckoutModal: @entangle('showSuccessModal')
}">

    <!-- Bagian Header: Menampilkan informasi singkat dan tombol kosongkan keranjang -->
    <header class="bg-white flex justify-between items-center px-6 h-[72px] border-b border-neutral-200 shrink-0 sticky top-0 z-30">
        <div class="flex items-center gap-4">
            <h1 class="text-xl font-bold text-neutral-900 leading-none">Buat Pesanan</h1>
            <div class="h-6 w-px bg-neutral-200"></div>
            <span class="text-sm font-medium text-neutral-800 bg-neutral-50 py-1.5 px-3 rounded-full border border-neutral-200 shadow-sm flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse border border-white"></span>
                <span>Kasir Aktif</span>
            </span>
        </div>
        <div class="flex items-center gap-4">
            <span class="text-sm font-medium text-neutral-800 hidden sm:inline-block">
                {{ \Carbon\Carbon::now()->format('d M Y') }}
            </span>
            <button wire:click="clearCart" class="text-sm font-semibold text-neutral-600 border border-neutral-200 rounded-lg px-4 h-11 hover:bg-neutral-50 hover:text-rose-600 hover:border-rose-200 transition-colors flex items-center gap-2 shadow-sm">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                Kosongkan Keranjang
            </button>
        </div>
    </header>

    <!-- Struktur Utama: Layout Kiri (Produk) & Kanan (Keranjang) -->
    <div class="flex flex-col lg:flex-row flex-1 w-full overflow-hidden relative">

        <!-- PANEL KIRI: Pencarian, Kategori & Daftar Produk -->
        <section class="flex-1 min-w-0 flex flex-col bg-neutral-50 border-b lg:border-b-0 lg:border-r border-neutral-200 overflow-y-auto">
            <!-- Navigasi Bar Lengket (Sticky) untuk Cari & Filter -->
            <div class="sticky top-0 p-4 lg:p-6 bg-white shrink-0 border-b border-neutral-200 z-20 shadow-[0_4px_20px_rgba(0,0,0,0.02)]">
                <div class="flex flex-col xl:flex-row xl:items-center gap-4 xl:gap-6">

                    <!-- Search Bar -->
                    <div class="shrink-0 w-full xl:w-72 order-1 xl:order-2 relative">
                        <div class="flex items-center w-full bg-neutral-50 border border-neutral-200 rounded-xl focus-within:border-neutral-900 focus-within:ring-1 focus-within:ring-neutral-900 focus-within:bg-white transition-colors overflow-hidden h-11">
                            <div class="pl-3 pr-2 text-neutral-400 flex items-center justify-center">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" wire:model.live.debounce.300ms="search" class="flex-1 w-full h-full pr-3 bg-transparent outline-none text-neutral-900 text-[14px] font-medium placeholder:text-neutral-400 placeholder:font-normal" placeholder="Cari produk..." />
                        </div>
                        <div wire:loading wire:target="search" class="absolute right-3 top-1/2 -translate-y-1/2 text-neutral-400">
                            <flux:icon.arrow-path class="w-4 h-4 animate-spin" />
                        </div>
                    </div>

                    <!-- Filter Categories -->
                    <div class="flex-1 overflow-hidden order-2 xl:order-1">
                        <div class="flex gap-3 overflow-x-auto pb-2 pt-1 px-1 scrollbar-hide">
                            <button @click="activeCategory = 'all'"
                                    :class="activeCategory === 'all' ? 'bg-neutral-900 border-neutral-900 shadow-md ring-2 ring-neutral-900 ring-offset-1 text-white' : 'bg-white hover:bg-neutral-50 border-neutral-200 text-neutral-700'"
                                    class="shrink-0 rounded-xl border p-2.5 flex flex-col items-start min-w-[110px] sm:min-w-[124px] h-16 transition-all duration-200 group text-left relative overflow-hidden">
                                <div class="flex justify-between items-start w-full gap-1 mb-1 relative z-10">
                                    <span class="text-[13px] font-bold tracking-tight line-clamp-1 flex-1">Semua</span>
                                </div>
                                <span :class="activeCategory === 'all' ? 'text-neutral-300' : 'text-neutral-800'" class="text-[11px] font-semibold mt-auto relative z-10">Semua Item</span>
                            </button>

                            @if(isset($categories))
                            @foreach($categories as $category)
                                <button @click="activeCategory = '{{ $category->id }}'"
                                        :class="activeCategory === '{{ $category->id }}' ? 'bg-neutral-900 border-neutral-900 shadow-md ring-2 ring-neutral-900 ring-offset-1 text-white' : 'bg-white hover:bg-neutral-50 border-neutral-200 text-neutral-700'"
                                        class="shrink-0 rounded-xl border p-2.5 flex flex-col items-start min-w-[110px] sm:min-w-[124px] h-16 transition-all duration-200 group text-left relative overflow-hidden">
                                    <div class="flex justify-between items-start w-full gap-1 mb-1 relative z-10">
                                        <span class="text-[13px] font-bold tracking-tight line-clamp-1 flex-1">{{ $category->name }}</span>
                                    </div>
                                    <span :class="activeCategory === '{{ $category->id }}' ? 'text-neutral-300' : 'text-neutral-800'" class="text-[11px] font-semibold mt-auto relative z-10">Kategori</span>
                                </button>
                            @endforeach
                            @endif
                        </div>
                    </div>

                </div>
            </div>

            <!-- Grid Produk -->
            <div class="flex-1 p-4 lg:p-6 overflow-y-auto">
                <div class="grid grid-cols-2 sm:grid-cols-3 xl:grid-cols-4 2xl:grid-cols-5 gap-4 lg:gap-5">
                    @foreach($products as $variant)
                        @php
                            $stock = $variant->stocks->first()->quantity ?? 0;
                            $hasStock = $stock > 0;
                            $categoryName = $variant->product->category ? $variant->product->category->name : 'Umum';
                            $categoryId = $variant->product->category_id;
                        @endphp
                        
                        <div x-show="activeCategory === 'all' || activeCategory === '{{ $categoryId }}'" 
                             class="bg-white rounded-[14px] border border-neutral-200 overflow-hidden shadow-sm hover:shadow-lg transition-all duration-300 flex flex-col group relative {{ !$hasStock ? 'opacity-60 grayscale-[0.5]' : '' }}">
                            
                            <!-- Area Gambar Produk -->
                            <div class="aspect-[4/3] w-full bg-neutral-100 relative overflow-hidden">
                                @if($variant->product->image_url)
                                    <img src="{{ Storage::url($variant->product->image_url) }}" alt="{{ $variant->product->name }}" loading="lazy" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-neutral-300 transition-transform duration-500 group-hover:scale-110">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                @endif
                                
                                @if(!$hasStock)
                                    <!-- Overlay Habis -->
                                    <div class="absolute inset-0 bg-neutral-900/60 flex items-center justify-center backdrop-blur-[2px]">
                                        <span class="bg-white/95 text-neutral-900 text-xs font-bold px-3 py-1.5 rounded-md shadow-sm border border-neutral-200 uppercase tracking-widest">Habis</span>
                                    </div>
                                @else
                                    <div class="absolute inset-0 bg-neutral-900/0 group-hover:bg-neutral-900/5 transition-colors duration-300"></div>
                                @endif
                            </div>

                            <!-- Area Informasi Produk -->
                            <div class="p-3.5 lg:p-4 flex flex-col flex-1 relative z-10 bg-white">
                                <div class="flex justify-between items-start gap-2 mb-1.5">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-neutral-400">{{ $categoryName }}</span>
                                </div>
                                <h3 class="text-[14px] font-bold text-neutral-900 leading-[1.3] line-clamp-2 mb-3">{{ $variant->product->name }} {{ $variant->sku }}</h3>
                                
                                <div class="mt-auto flex items-end justify-between gap-2">
                                    <div class="flex flex-col">
                                        <span class="text-[16px] font-black text-neutral-900 tracking-tight leading-none">Rp {{ number_format($variant->selling_price, 0, ',', '.') }}</span>
                                    </div>
                                    
                                    <!-- Tombol Tambah ke Keranjang -->
                                    <button wire:click="addToCart('{{ $variant->id }}')" 
                                            {{ !$hasStock ? 'disabled' : '' }}
                                            class="h-9 w-9 md:h-10 md:w-10 rounded-xl flex items-center justify-center transition-all duration-300 shrink-0 shadow-sm {{ $hasStock ? 'bg-neutral-900 text-white hover:bg-neutral-800 hover:scale-105 active:scale-95' : 'bg-neutral-100 text-neutral-400 cursor-not-allowed border border-neutral-200' }}">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                @if(count($products) > 0)
                <div class="mt-8 border-t border-neutral-200 pt-6">
                    {{ $products->links() }}
                </div>
                @else
                <div class="flex flex-col items-center justify-center h-full text-neutral-500 pb-12">
                    <div class="w-16 h-16 rounded-full bg-neutral-100 border border-neutral-200 flex items-center justify-center mb-4">
                        <flux:icon.magnifying-glass class="w-8 h-8 text-neutral-400" />
                    </div>
                    <h3 class="text-h3 font-bold text-neutral-900">Tidak ada produk yang ditemukan.</h3>
                    <p class="text-body mt-1 text-neutral-500">Coba kata kunci lain atau periksa ejaan.</p>
                </div>
                @endif
            </div>
        </section>

        <!-- PANEL KANAN: Keranjang & Detail Pembayaran -->
        <aside class="w-full lg:w-[400px] xl:w-[440px] flex flex-col bg-white shrink-0 border-t lg:border-t-0 border-neutral-200 z-30 lg:z-auto shadow-[-4px_0_24px_rgba(0,0,0,0.02)] relative h-full">
            <div class="p-5 lg:p-6 pb-4 border-b border-neutral-200 bg-white shrink-0">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-[18px] font-extrabold text-neutral-900 flex items-center gap-2">
                        Keranjang
                        <span class="bg-neutral-100 text-neutral-800 text-[12px] font-bold px-2 py-0.5 rounded-full border border-neutral-200">
                            {{ count($cart) }}
                        </span>
                    </h2>
                </div>
                
                <div class="flex gap-2">
                    <select wire:model.live="customer_id" class="flex-1 bg-neutral-50 border border-neutral-200 text-neutral-900 text-sm rounded-lg focus:ring-neutral-900 focus:border-neutral-900 block w-full p-2.5 font-medium transition-colors">
                        <option value="">Pilih Pelanggan (Opsional)</option>
                        @foreach($customers as $c)
                            <option value="{{ $c->id }}">{{ $c->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- List Item Keranjang -->
            <div class="flex-1 overflow-y-auto bg-neutral-50/30 p-2 sm:p-4">
                @if(count($cart) === 0)
                    <!-- Empty State Keranjang -->
                    <div class="h-full flex flex-col items-center justify-center text-center px-6 pb-12">
                        <div class="w-20 h-20 bg-neutral-50 rounded-full flex items-center justify-center mb-4 border border-neutral-200 shadow-inner">
                            <svg class="w-10 h-10 text-neutral-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path></svg>
                        </div>
                        <p class="text-[15px] font-bold text-neutral-700 mb-1">Keranjang Kosong</p>
                        <p class="text-[13px] text-neutral-500">Pilih produk dari area kiri untuk menambahkannya ke pesanan.</p>
                    </div>
                @else
                    <div class="flex flex-col gap-3">
                        @foreach($cart as $variantId => $item)
                            <div class="bg-white border border-neutral-200 p-4 rounded-xl shadow-sm relative group hover:border-neutral-300 transition-colors">
                                <!-- Tombol Hapus Item -->
                                <button wire:click="removeFromCart('{{ $variantId }}')" class="absolute -top-2.5 -right-2.5 w-7 h-7 bg-white border border-neutral-200 text-neutral-400 hover:text-rose-500 hover:border-rose-200 hover:bg-rose-50 rounded-full flex items-center justify-center shadow-sm transition-all md:opacity-0 md:group-hover:opacity-100 focus:opacity-100 z-10">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                                
                                <div class="flex items-start justify-between gap-3 mb-3">
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-[14px] font-bold text-neutral-900 leading-snug line-clamp-2 pr-4">{{ $item['name'] }}</h4>
                                        <p class="text-[13px] font-bold text-neutral-700 mt-1">Rp {{ number_format($item['price'], 0, ',', '.') }}</p>
                                    </div>
                                </div>
                                
                                <div class="flex items-center justify-between border-t border-neutral-100 pt-3">
                                    <p class="text-[14px] font-black text-neutral-900 tracking-tight">Rp {{ number_format($item['price'] * $item['quantity'], 0, ',', '.') }}</p>
                                    
                                    <div class="flex items-center bg-neutral-50 border border-neutral-200 rounded-lg p-0.5 shadow-sm">
                                        <button wire:click="decrementQuantity('{{ $variantId }}')" class="w-8 h-8 flex items-center justify-center text-neutral-600 hover:bg-white hover:text-neutral-900 rounded-md transition-colors hover:shadow-xs focus:outline-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M20 12H4"></path></svg>
                                        </button>
                                        <div class="w-10 text-center font-bold text-[14px] text-neutral-900 border-x border-neutral-200 flex items-center justify-center h-8 bg-white">
                                            {{ $item['quantity'] }}
                                        </div>
                                        <button wire:click="incrementQuantity('{{ $variantId }}')" class="w-8 h-8 flex items-center justify-center text-neutral-600 hover:bg-white hover:text-neutral-900 rounded-md transition-colors hover:shadow-xs focus:outline-none">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"></path></svg>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Panel Ringkasan Pembayaran & Tombol Checkout -->
            <div class="bg-white border-t border-neutral-200 p-5 lg:p-6 shrink-0 relative z-20 shadow-[0_-10px_20px_rgba(0,0,0,0.02)]">
                
                <div class="space-y-3 mb-5">
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-neutral-500 font-medium">Subtotal</span>
                        <span class="font-bold text-neutral-900">Rp {{ number_format($this->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[14px]">
                        <span class="text-neutral-500 font-medium">Diskon</span>
                        <span class="font-bold text-rose-500">- Rp {{ number_format($this->discountTotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between items-center text-[14px] pb-3 border-b border-neutral-100">
                        <span class="text-neutral-500 font-medium">Pajak / PPN</span>
                        <span class="font-bold text-neutral-900">Rp {{ number_format($this->taxTotal, 0, ',', '.') }}</span>
                    </div>
                    
                    <div class="flex justify-between items-end pt-1">
                        <span class="text-[15px] font-bold text-neutral-800">Total Tagihan</span>
                        <span class="text-[24px] font-black text-neutral-900 leading-none tracking-tight">Rp {{ number_format($this->grandTotal, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="mb-5">
                    <label class="block text-[13px] font-bold text-neutral-700 mb-2">Metode Pembayaran</label>
                    <select wire:model.live="payment_method" class="w-full bg-neutral-50 border border-neutral-200 text-neutral-900 text-[14px] rounded-xl focus:ring-neutral-900 focus:border-neutral-900 p-3 mb-4 font-bold outline-none transition-colors shadow-sm">
                        <option value="cash">Tunai (Cash)</option>
                        <option value="qris">QRIS</option>
                        <option value="transfer">Transfer Bank</option>
                        <option value="card">Kartu Kredit / Debit</option>
                    </select>

                    @if($payment_method === 'cash')
                        <label class="block text-[13px] font-bold text-neutral-700 mb-2">Tunai (Bayar)</label>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none">
                                <span class="text-neutral-500 font-bold text-sm">Rp</span>
                            </div>
                            <input type="number" wire:model.live.debounce.500ms="cash_received" class="bg-neutral-50 border border-neutral-200 text-neutral-900 text-lg font-black rounded-xl focus:ring-neutral-900 focus:border-neutral-900 block w-full pl-10 p-3.5 transition-colors shadow-sm" placeholder="0">
                        </div>
                        
                        <!-- Area Kembalian -->
                        @if($cash_received > 0)
                            <div class="mt-3 flex justify-between items-center p-3 rounded-xl border {{ $change_amount >= 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-rose-50 border-rose-100' }}">
                                <span class="text-[13px] font-bold {{ $change_amount >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                                    {{ $change_amount >= 0 ? 'Kembalian' : 'Kurang' }}
                                </span>
                                <span class="text-[16px] font-black {{ $change_amount >= 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                                    Rp {{ number_format(abs($change_amount), 0, ',', '.') }}
                                </span>
                            </div>
                        @endif
                    @endif
                </div>

                <!-- Tombol Proses Pesanan -->
                <button wire:click="processPayment" 
                        {{ count($cart) === 0 || ($cash_received < $this->grandTotal && $this->payment_method === 'cash') ? 'disabled' : '' }}
                        class="w-full bg-neutral-900 hover:bg-neutral-800 text-white h-[52px] rounded-xl font-bold text-[15px] flex justify-center items-center gap-2 transition-all shadow-md focus:ring-2 focus:ring-neutral-900 focus:ring-offset-2 focus:outline-none disabled:opacity-50 disabled:cursor-not-allowed disabled:hover:bg-neutral-900">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                    Proses Transaksi
                </button>
            </div>
        </aside>
    </div>

    <!-- Modal Sukses (Diambil dari PosScreen.php variable showSuccessModal) -->
    <div x-show="showCheckoutModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div x-show="showCheckoutModal" x-transition.opacity.duration.300ms class="fixed inset-0 bg-neutral-900/40 backdrop-blur-sm transition-opacity"></div>
        <div x-show="showCheckoutModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" class="relative bg-white rounded-2xl shadow-xl p-8 max-w-sm w-full mx-4 text-center transform transition-all z-10 border border-neutral-100">
            <div class="mx-auto flex items-center justify-center h-20 w-20 rounded-full bg-emerald-50 border-[6px] border-emerald-100/50 mb-5">
                <svg class="h-10 w-10 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
            </div>
            <h3 class="text-xl font-black text-neutral-900 tracking-tight" id="modal-title">Transaksi Berhasil!</h3>
            <p class="text-[14px] text-neutral-500 mt-2 font-medium">Pembayaran telah diterima dan pesanan selesai.</p>
            <div class="mt-8 flex flex-col gap-3">
                <button type="button" class="w-full inline-flex justify-center rounded-xl border border-neutral-200 bg-white px-5 py-3.5 text-[14px] font-bold text-neutral-700 shadow-sm hover:bg-neutral-50 hover:text-neutral-900 transition-colors">
                    <svg class="w-4 h-4 mr-2 -ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                    Cetak Struk
                </button>
                <button wire:click="startNewTransaction" type="button" class="w-full inline-flex justify-center rounded-xl bg-neutral-900 px-5 py-3.5 text-[14px] font-bold text-white shadow-md hover:bg-neutral-800 transition-colors">
                    Transaksi Baru
                </button>
            </div>
        </div>
    </div>
</div>
