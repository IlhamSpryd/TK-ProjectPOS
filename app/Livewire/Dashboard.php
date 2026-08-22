<?php

namespace App\Livewire;

use Livewire\Component;
use App\Models\Sale;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
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

        // 7-day revenue trend for chart (P-10)
        $chartData = [];
        $chartLabels = [];
        if ($storeId) {
            $last7Days = collect(range(6, 0))->map(function ($days) {
                return Carbon::today()->subDays($days);
            });
            
            foreach ($last7Days as $date) {
                $chartLabels[] = $date->format('d/m');
                $dailyRevenue = Sale::where('store_id', $storeId)
                    ->whereDate('created_at', $date)
                    ->sum('grand_total');
                $chartData[] = $dailyRevenue;
            }
        }

        return view('dashboard', [
            'todayRevenue' => $todayRevenue,
            'activeProducts' => $activeProducts,
            'totalSalesToday' => $totalSalesToday,
            'store' => $store,
            'chartLabels' => $chartLabels,
            'chartData' => $chartData
        ])->layout('layouts.app');
    }
}
