<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

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

        if ($storeId) {
            $todayRevenue = Sale::where('store_id', $storeId)
                ->whereDate('created_at', Carbon::today())
                ->sum('grand_total');

            $totalSalesToday = Sale::where('store_id', $storeId)
                ->whereDate('created_at', Carbon::today())
                ->count();
        }

        $activeProducts = ProductVariant::where('active', true)->count();

        // 7-day revenue trend for chart (P-10) - Optimized 1 Query GROUP BY
        $chartData = [];
        $chartLabels = [];
        
        if ($storeId) {
            $startDate = Carbon::today()->subDays(6);
            $endDate = Carbon::today()->endOfDay();
            
            // Get data in single query grouped by date
            $salesData = Sale::where('store_id', $storeId)
                ->whereBetween('created_at', [$startDate, $endDate])
                ->select(
                    DB::raw('DATE(created_at) as date'),
                    DB::raw('SUM(grand_total) as total')
                )
                ->groupBy('date')
                ->pluck('total', 'date')
                ->toArray();
                
            // Fill arrays ensuring all 7 days are represented
            for ($i = 6; $i >= 0; $i--) {
                $dateObj = Carbon::today()->subDays($i);
                $dateStr = $dateObj->format('Y-m-d');
                
                $chartLabels[] = $dateObj->format('d/m');
                $chartData[] = $salesData[$dateStr] ?? 0;
            }
        }

        return view('dashboard', [
            'todayRevenue' => $todayRevenue,
            'activeProducts' => $activeProducts,
            'totalSalesToday' => $totalSalesToday,
            'store' => $store,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ]);
    }
}

