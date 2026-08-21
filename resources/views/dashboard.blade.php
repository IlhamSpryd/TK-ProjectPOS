<x-layouts::app :title="__('Dashboard')">
    <div class="flex h-full w-full flex-1 flex-col gap-6">
        <!-- Welcome Section -->
        <div class="bg-gradient-to-r from-blue-600 to-indigo-600 rounded-3xl p-8 text-white shadow-xl shadow-blue-500/20 relative overflow-hidden">
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h2 class="text-3xl font-black mb-2">Selamat Datang, {{ auth()->user()->name }}! 👋</h2>
                    <p class="text-blue-100 font-medium text-lg">Berikut adalah ringkasan penjualan toko Anda hari ini.</p>
                </div>
                <flux:button href="{{ route('pos') }}" variant="primary" class="bg-white text-blue-600 hover:bg-zinc-50 border-none rounded-xl px-6 py-2.5 font-bold shadow-lg shadow-black/10 transition-transform hover:-translate-y-1">
                    Buka Kasir Sekarang
                </flux:button>
            </div>
        </div>

        <!-- Stat Cards -->
        <div class="grid auto-rows-min gap-6 md:grid-cols-4">
            <!-- Stat 1 -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 border border-zinc-100 dark:border-zinc-800 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 dark:bg-blue-900/20 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-blue-100 dark:bg-blue-500/20 flex items-center justify-center mb-4 text-blue-600 dark:text-blue-400">
                        <flux:icon.banknotes class="w-6 h-6" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium mb-1">Pendapatan Hari Ini</p>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white">Rp 4.520.000</h3>
                    <div class="flex items-center gap-1 mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        <flux:icon.arrow-trending-up class="w-3 h-3" />
                        <span>+12.5% vs kemarin</span>
                    </div>
                </div>
            </div>

            <!-- Stat 2 -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 border border-zinc-100 dark:border-zinc-800 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-emerald-50 dark:bg-emerald-900/20 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-500/20 flex items-center justify-center mb-4 text-emerald-600 dark:text-emerald-400">
                        <flux:icon.shopping-bag class="w-6 h-6" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium mb-1">Total Transaksi</p>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white">84</h3>
                    <div class="flex items-center gap-1 mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        <flux:icon.arrow-trending-up class="w-3 h-3" />
                        <span>+5.2% vs kemarin</span>
                    </div>
                </div>
            </div>

            <!-- Stat 3 -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 border border-zinc-100 dark:border-zinc-800 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-purple-50 dark:bg-purple-900/20 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-purple-100 dark:bg-purple-500/20 flex items-center justify-center mb-4 text-purple-600 dark:text-purple-400">
                        <flux:icon.users class="w-6 h-6" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium mb-1">Pelanggan Baru</p>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white">12</h3>
                    <div class="flex items-center gap-1 mt-2 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                        <flux:icon.arrow-trending-up class="w-3 h-3" />
                        <span>+2.1% vs kemarin</span>
                    </div>
                </div>
            </div>

            <!-- Stat 4 -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl p-6 border border-zinc-100 dark:border-zinc-800 shadow-[0_2px_10px_-3px_rgba(6,81,237,0.1)] hover:-translate-y-1 transition-transform duration-300 relative overflow-hidden group">
                <div class="absolute -right-6 -top-6 w-24 h-24 bg-rose-50 dark:bg-rose-900/20 rounded-full group-hover:scale-150 transition-transform duration-500 z-0"></div>
                <div class="relative z-10">
                    <div class="w-12 h-12 rounded-2xl bg-rose-100 dark:bg-rose-500/20 flex items-center justify-center mb-4 text-rose-600 dark:text-rose-400">
                        <flux:icon.archive-box class="w-6 h-6" />
                    </div>
                    <p class="text-zinc-500 dark:text-zinc-400 text-sm font-medium mb-1">Produk Terjual</p>
                    <h3 class="text-2xl font-black text-zinc-900 dark:text-white">215</h3>
                    <div class="flex items-center gap-1 mt-2 text-xs font-bold text-rose-600 dark:text-rose-400">
                        <flux:icon.arrow-trending-down class="w-3 h-3" />
                        <span>-1.5% vs kemarin</span>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid gap-6 md:grid-cols-3 flex-1 pb-6">
            <!-- Chart Area -->
            <div class="md:col-span-2 bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <div>
                        <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Grafik Penjualan</h3>
                        <p class="text-sm text-zinc-500 dark:text-zinc-400">7 Hari Terakhir</p>
                    </div>
                    <flux:button variant="ghost" size="sm" icon-trailing="chevron-down">Minggu Ini</flux:button>
                </div>
                <div class="flex-1 relative min-h-[250px] bg-zinc-50/50 dark:bg-zinc-800/20 rounded-2xl border border-zinc-100 dark:border-zinc-800/50 flex items-center justify-center overflow-hidden group">
                    <!-- Placeholder Chart Pattern -->
                    <div class="absolute inset-0 flex items-end justify-between p-6 opacity-30 dark:opacity-20 pointer-events-none">
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[40%]"></div>
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[60%]"></div>
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[45%]"></div>
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[80%]"></div>
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[65%]"></div>
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[90%]"></div>
                        <div class="w-12 bg-blue-500 rounded-t-lg h-[75%]"></div>
                    </div>
                    <div class="text-center z-10 group-hover:scale-105 transition-transform">
                        <div class="w-16 h-16 rounded-full bg-zinc-100 dark:bg-zinc-800 flex items-center justify-center mx-auto mb-3">
                            <flux:icon.chart-bar class="w-8 h-8 text-zinc-400" />
                        </div>
                        <p class="text-zinc-500 font-medium">Integrasi Chart Akan Segera Hadir</p>
                    </div>
                </div>
            </div>

            <!-- Top Products -->
            <div class="bg-white dark:bg-zinc-900 rounded-3xl border border-zinc-100 dark:border-zinc-800 p-6 shadow-sm flex flex-col">
                <div class="flex items-center justify-between mb-6">
                    <h3 class="text-lg font-bold text-zinc-900 dark:text-white">Produk Terlaris</h3>
                    <flux:button variant="ghost" size="sm" class="text-blue-600 dark:text-blue-400 hover:text-blue-700">Lihat Semua</flux:button>
                </div>
                <div class="flex-1 space-y-4">
                    <!-- Item 1 -->
                    <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex-shrink-0 flex items-center justify-center text-zinc-400">
                            <flux:icon.photo class="w-6 h-6" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">Kopi Susu Gula Aren</h4>
                            <p class="text-xs text-zinc-500 mt-0.5">42 Terjual</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-zinc-900 dark:text-zinc-100">Rp 18K</span>
                        </div>
                    </div>
                    <!-- Item 2 -->
                    <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex-shrink-0 flex items-center justify-center text-zinc-400">
                            <flux:icon.photo class="w-6 h-6" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">Roti Bakar Coklat</h4>
                            <p class="text-xs text-zinc-500 mt-0.5">28 Terjual</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-zinc-900 dark:text-zinc-100">Rp 15K</span>
                        </div>
                    </div>
                    <!-- Item 3 -->
                    <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex-shrink-0 flex items-center justify-center text-zinc-400">
                            <flux:icon.photo class="w-6 h-6" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">Es Teh Manis</h4>
                            <p class="text-xs text-zinc-500 mt-0.5">25 Terjual</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-zinc-900 dark:text-zinc-100">Rp 5K</span>
                        </div>
                    </div>
                     <!-- Item 4 -->
                     <div class="flex items-center gap-4 p-3 rounded-2xl hover:bg-zinc-50 dark:hover:bg-zinc-800/50 transition-colors">
                        <div class="w-12 h-12 rounded-xl bg-zinc-100 dark:bg-zinc-800 flex-shrink-0 flex items-center justify-center text-zinc-400">
                            <flux:icon.photo class="w-6 h-6" />
                        </div>
                        <div class="flex-1 min-w-0">
                            <h4 class="text-sm font-bold text-zinc-900 dark:text-zinc-100 truncate">Nasi Goreng Spesial</h4>
                            <p class="text-xs text-zinc-500 mt-0.5">15 Terjual</p>
                        </div>
                        <div class="text-right">
                            <span class="text-sm font-black text-zinc-900 dark:text-zinc-100">Rp 25K</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-layouts::app>
