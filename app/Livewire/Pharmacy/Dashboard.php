<?php

namespace App\Livewire\Pharmacy;

use App\Models\Purchase;
use App\Models\Sale;
use App\Services\DashboardReportService;
use App\Services\ExpiryAlertService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class Dashboard extends Component
{
    public function render()
    {
        $dashboard = app(DashboardReportService::class);
        $expiry = app(ExpiryAlertService::class);

        return view('livewire.pharmacy.dashboard', [
            'cards' => $dashboard->summary(),
            'recentSales' => Sale::with('customer')->latest()->limit(5)->get(),
            'recentPurchases' => Purchase::with('supplier')->latest()->limit(5)->get(),
            'lowStockProducts' => $dashboard->lowStockProducts()->take(5),
            'expiringProducts' => $expiry->nearExpiry()->limit(5)->get(),
            'salesTrend' => $dashboard->salesTrend(),
        ]);
    }
}
