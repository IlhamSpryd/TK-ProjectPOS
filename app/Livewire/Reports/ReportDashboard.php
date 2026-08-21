<?php

namespace App\Livewire\Reports;

use Livewire\Component;
use App\Models\Sale;
use Carbon\Carbon;

class ReportDashboard extends Component
{
    public $dateRange = 'today';

    public function render()
    {
        $query = Sale::query();

        if ($this->dateRange === 'today') {
            $query->whereDate('created_at', Carbon::today());
        } elseif ($this->dateRange === 'week') {
            $query->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()]);
        } elseif ($this->dateRange === 'month') {
            $query->whereMonth('created_at', Carbon::now()->month)
                  ->whereYear('created_at', Carbon::now()->year);
        }

        $sales = $query->with(['customer', 'store'])->orderBy('created_at', 'desc')->get();
        
        $totalRevenue = $sales->where('status', 'completed')->sum('grand_total');
        $totalTransactions = $sales->count();
        $totalItemsSold = $sales->sum('total_items');

        return view('livewire.reports.report-dashboard', [
            'sales' => $sales,
            'totalRevenue' => $totalRevenue,
            'totalTransactions' => $totalTransactions,
            'totalItemsSold' => $totalItemsSold
        ])->layout('layouts.app');
    }
}
