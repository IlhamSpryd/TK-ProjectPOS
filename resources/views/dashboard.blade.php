<div class="flex h-full w-full flex-1 flex-col gap-6">
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-h2 font-bold text-neutral-900">Selamat Datang, {{ auth()->user()->full_name }}! 👋</h1>
            <p class="text-body text-neutral-500 mt-1">Berikut adalah ringkasan penjualan {{ $store ? $store->name : 'toko Anda' }} hari ini.</p>
        </div>
        <div>
            <x-ui.button variant="primary" icon="calculator" href="{{ route('pos') }}" wire:navigate>
                Buka Kasir
            </x-ui.button>
        </div>
    </div>

    <!-- Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        <!-- Revenue -->
        <x-ui.card class="p-6 border-neutral-200 shadow-xs hover:border-primary-200 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-caption font-medium text-neutral-500 mb-1">Pendapatan Hari Ini</p>
                    <h3 class="text-display font-bold text-neutral-900">Rp {{ number_format($todayRevenue, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <flux:icon.banknotes class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-body-sm font-medium text-success-600">
                <flux:icon.arrow-trending-up class="w-4 h-4" />
                <span>+12.5% vs kemarin</span>
            </div>
        </x-ui.card>

        <!-- Transactions -->
        <x-ui.card class="p-6 border-neutral-200 shadow-xs hover:border-primary-200 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-caption font-medium text-neutral-500 mb-1">Total Transaksi</p>
                    <h3 class="text-display font-bold text-neutral-900">{{ number_format($totalSalesToday, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <flux:icon.shopping-bag class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-body-sm font-medium text-success-600">
                <flux:icon.arrow-trending-up class="w-4 h-4" />
                <span>+5.2% vs kemarin</span>
            </div>
        </x-ui.card>

        <!-- Customers -->
        <x-ui.card class="p-6 border-neutral-200 shadow-xs hover:border-primary-200 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-caption font-medium text-neutral-500 mb-1">Pelanggan Baru</p>
                    <h3 class="text-display font-bold text-neutral-900">12</h3>
                </div>
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <flux:icon.users class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-body-sm font-medium text-success-600">
                <flux:icon.arrow-trending-up class="w-4 h-4" />
                <span>+2.1% vs kemarin</span>
            </div>
        </x-ui.card>

        <!-- Active Products -->
        <x-ui.card class="p-6 border-neutral-200 shadow-xs hover:border-primary-200 transition-colors">
            <div class="flex justify-between items-start">
                <div>
                    <p class="text-caption font-medium text-neutral-500 mb-1">Produk Aktif</p>
                    <h3 class="text-display font-bold text-neutral-900">{{ number_format($activeProducts, 0, ',', '.') }}</h3>
                </div>
                <div class="p-2 bg-primary-50 rounded-lg text-primary-600">
                    <flux:icon.archive-box class="w-6 h-6" />
                </div>
            </div>
            <div class="mt-4 flex items-center gap-1.5 text-body-sm font-medium text-danger-600">
                <flux:icon.arrow-trending-down class="w-4 h-4" />
                <span>-1.5% vs kemarin</span>
            </div>
        </x-ui.card>
    </div>

    <div class="grid grid-cols-12 gap-6 pb-6">
        <!-- Chart Area -->
        <x-ui.card class="col-span-12 lg:col-span-8 p-6 border-neutral-200 shadow-xs flex flex-col">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-6 gap-4">
                <div>
                    <h3 class="text-h3 font-bold text-neutral-900">Grafik Penjualan</h3>
                    <p class="text-body-sm text-neutral-500">7 Hari Terakhir</p>
                </div>
                <x-ui.button variant="ghost" size="sm" icon-trailing="chevron-down">Minggu Ini</x-ui.button>
            </div>
            <div class="flex-1 min-h-[300px] bg-neutral-50 rounded-lg border border-neutral-100 flex items-center justify-center relative overflow-hidden">
                <!-- Placeholder Chart -->
                <div class="absolute inset-0 flex items-end justify-between p-6 opacity-40 pointer-events-none">
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[40%]"></div>
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[60%]"></div>
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[45%]"></div>
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[80%]"></div>
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[65%]"></div>
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[90%]"></div>
                    <div class="w-full max-w-[40px] bg-primary-300 rounded-t-lg h-[75%]"></div>
                </div>
                <div class="text-center z-10">
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center mx-auto mb-3 shadow-xs">
                        <flux:icon.chart-bar class="w-6 h-6 text-neutral-400" />
                    </div>
                    <p class="text-body-sm text-neutral-500 font-medium">Integrasi Chart Akan Segera Hadir</p>
                </div>
            </div>
        </x-ui.card>

        <!-- Top Products -->
        <x-ui.card class="col-span-12 lg:col-span-4 p-6 border-neutral-200 shadow-xs flex flex-col">
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-h3 font-bold text-neutral-900">Produk Terlaris</h3>
                <x-ui.button variant="ghost" size="sm" class="text-primary-600 hover:text-primary-700">Lihat Semua</x-ui.button>
            </div>
            <div class="flex-1 flex flex-col gap-1">
                <!-- Item 1 -->
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-neutral-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-neutral-100 flex-shrink-0 flex items-center justify-center text-neutral-400 border border-neutral-200">
                        <flux:icon.photo class="w-6 h-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body font-bold text-neutral-900 truncate">Kopi Susu Gula Aren</h4>
                        <p class="text-caption text-neutral-500 mt-0.5">42 Terjual</p>
                    </div>
                    <div class="text-right">
                        <span class="text-body font-bold text-neutral-900">Rp 18K</span>
                    </div>
                </div>
                <!-- Item 2 -->
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-neutral-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-neutral-100 flex-shrink-0 flex items-center justify-center text-neutral-400 border border-neutral-200">
                        <flux:icon.photo class="w-6 h-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body font-bold text-neutral-900 truncate">Roti Bakar Coklat</h4>
                        <p class="text-caption text-neutral-500 mt-0.5">28 Terjual</p>
                    </div>
                    <div class="text-right">
                        <span class="text-body font-bold text-neutral-900">Rp 15K</span>
                    </div>
                </div>
                <!-- Item 3 -->
                <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-neutral-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-neutral-100 flex-shrink-0 flex items-center justify-center text-neutral-400 border border-neutral-200">
                        <flux:icon.photo class="w-6 h-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body font-bold text-neutral-900 truncate">Es Teh Manis</h4>
                        <p class="text-caption text-neutral-500 mt-0.5">25 Terjual</p>
                    </div>
                    <div class="text-right">
                        <span class="text-body font-bold text-neutral-900">Rp 5K</span>
                    </div>
                </div>
                 <!-- Item 4 -->
                 <div class="flex items-center gap-4 p-3 rounded-lg hover:bg-neutral-50 transition-colors">
                    <div class="w-12 h-12 rounded-lg bg-neutral-100 flex-shrink-0 flex items-center justify-center text-neutral-400 border border-neutral-200">
                        <flux:icon.photo class="w-6 h-6" />
                    </div>
                    <div class="flex-1 min-w-0">
                        <h4 class="text-body font-bold text-neutral-900 truncate">Nasi Goreng Spesial</h4>
                        <p class="text-caption text-neutral-500 mt-0.5">15 Terjual</p>
                    </div>
                    <div class="text-right">
                        <span class="text-body font-bold text-neutral-900">Rp 25K</span>
                    </div>
                </div>
            </div>
        </x-ui.card>
    </div>
</div>
