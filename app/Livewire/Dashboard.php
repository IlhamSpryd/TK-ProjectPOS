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
        $primaryStore = $staff->stores()->wherePivot('is_primary', true)->first();
        $store = $primaryStore ?: $staff->stores()->first();
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

        return view('dashboard', [
            'todayRevenue' => $todayRevenue,
            'activeProducts' => $activeProducts,
            'totalSalesToday' => $totalSalesToday,
            'store' => $store
        ])->layout('layouts.app');
    }
}
