<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Sale;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class Index extends Component
{
    use WithPagination;

    public $dateRange = 'today';

    public function render()
    {
        $query = Sale::query();

        // 1. Filter per store
        $staff = Auth::user();
        if ($staff && method_exists($staff, 'getActiveStore')) {
            $store = $staff->getActiveStore();
            if ($store) {
                $query->where('store_id', $store->id);
            }
        }

        // 2. Filter tanggal
        if ($this->dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->dateRange === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->dateRange === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        $totals = (clone $query)->selectRaw("
            COUNT(*) as total_transactions,
            SUM(CASE WHEN status = 'completed' THEN grand_total ELSE 0 END) as total_revenue
        ")->first();

        $totalRevenue = $totals->total_revenue ?? 0;
        $totalTransactions = $totals->total_transactions ?? 0;
        
        // Optimasi: hitung items sold hanya dari id yang terfilter
        $totalItemsSold = \App\Models\SaleItem::whereIn('sale_id', (clone $query)->select('id'))->sum('quantity');

        $sales = $query->with(['customer', 'store'])->orderBy('created_at', 'desc')->paginate(20);

        return view('livewire.reports.index', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalItemsSold' => $totalItemsSold
        ]);
    }
}
