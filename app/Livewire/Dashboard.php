<?php

namespace App\Livewire;

use App\Models\ProductVariant;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\HtmlString;
use Livewire\Component;

class Dashboard extends Component
{
    public function render()
    {
        $staff = Auth::user();
        $store = ($staff && method_exists($staff, 'getActiveStore')) ? $staff->getActiveStore() : null;
        $storeId = $store ? $store->id : null;

        $todayRevenue = 0;
        $activeProducts = 0;
        $totalSalesToday = 0;
        $chartLabels = [];
        $chartData = [];

        if ($storeId) {
            // Cache query ringkasan harian (60 detik) — data ini tidak perlu real-time di dashboard.
            [$todayRevenue, $totalSalesToday] = Cache::remember(
                "dashboard_summary_{$storeId}",
                60,
                fn () => [
                    Sale::where('store_id', $storeId)->whereDate('created_at', Carbon::today())->sum('grand_total'),
                    Sale::where('store_id', $storeId)->whereDate('created_at', Carbon::today())->count(),
                ]
            );

            // Cache chart data (2 menit) — akurasi menit-an sudah cukup untuk tren harian.
            [$chartLabels, $chartData] = Cache::remember(
                "dashboard_chart_{$storeId}",
                120,
                function () use ($storeId) {
                    $startDate = Carbon::today()->subDays(6);
                    $endDate   = Carbon::today()->endOfDay();

                    $salesData = Sale::where('store_id', $storeId)
                        ->whereBetween('created_at', [$startDate, $endDate])
                        ->select(DB::raw('DATE(created_at) as date'), DB::raw('SUM(grand_total) as total'))
                        ->groupBy('date')
                        ->pluck('total', 'date')
                        ->toArray();

                    $labels = [];
                    $data   = [];
                    for ($i = 6; $i >= 0; $i--) {
                        $dateObj  = Carbon::today()->subDays($i);
                        $labels[] = $dateObj->format('d/m');
                        $data[]   = $salesData[$dateObj->format('Y-m-d')] ?? 0;
                    }

                    return [$labels, $data];
                }
            );
        }

        // Cache jumlah produk aktif (5 menit) — jarang berubah.
        $activeProducts = Cache::remember('dashboard_active_products', 300, fn () =>
            ProductVariant::where('active', true)->count()
        );

        $view = 'dashboard';
        return view($view, [
            'todayRevenue' => $todayRevenue,
            'activeProducts' => $activeProducts,
            'totalSalesToday' => $totalSalesToday,
            'store' => $store,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData,
        ])->layout('components.layouts.app', [
            'title' => 'Selamat Datang, '.(auth()->user()->full_name ?? auth()->user()->name).' 👋',
            'breadcrumbs' => [
                ['label' => 'Dashboard'],
            ],
            'actions' => new HtmlString(Blade::render(
                '<x-ui.button variant="primary" icon="calculator" href="{{ route(\'pos\') }}" wire:navigate>Buka Kasir</x-ui.button>'
            )),
        ]);
    }
}
