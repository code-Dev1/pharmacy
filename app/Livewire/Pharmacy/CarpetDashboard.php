<?php

namespace App\Livewire\Pharmacy;

use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Services\DashboardReportService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\On;
use Livewire\Component;

#[Layout('layouts.app')]
class CarpetDashboard extends Component
{
    public string $search = '';

    #[On('sale-created')]
    public function refreshAfterSale(): void
    {
    }

    public function render()
    {
        $dashboard = app(DashboardReportService::class);
        $search = trim($this->search);

        $products = Product::query()
            ->with(['category', 'productBatches' => fn ($query) => $query->latest()->limit(3)])
            ->when($search !== '', function ($query) use ($search) {
                $like = "%{$search}%";

                $query->where(function ($builder) use ($like) {
                    $builder
                        ->where('name', 'like', $like)
                        ->orWhere('generic_name', 'like', $like)
                        ->orWhere('barcode', 'like', $like)
                        ->orWhere('sku', 'like', $like)
                        ->orWhere('description', 'like', $like);
                });
            })
            ->latest()
            ->limit($search === '' ? 8 : 16)
            ->get();

        $inventoryValue = ProductBatch::query()
            ->selectRaw('COALESCE(SUM(remaining_quantity * sale_price), 0) as value')
            ->value('value');

        return view('livewire.pharmacy.carpet-dashboard', [
            'carpetStats' => [
                ['label' => 'کل طرح‌های فرش', 'value' => Product::count(), 'variant' => 'primary', 'icon' => 'box'],
                ['label' => 'موجودی فعال', 'value' => ProductBatch::sum('remaining_quantity'), 'variant' => 'success', 'icon' => 'stock'],
                ['label' => 'ارزش فروش موجودی', 'value' => number_format((float) $inventoryValue, 2), 'variant' => 'info', 'icon' => 'sales'],
                ['label' => 'طرح‌های کم‌موجودی', 'value' => $dashboard->lowStockProducts()->count(), 'variant' => 'warning', 'icon' => 'warning'],
            ],
            'products' => $products,
            'lowStockProducts' => $dashboard->lowStockProducts()->take(5),
            'recentSales' => Sale::with('customer')->latest()->limit(4)->get(),
            'recentPurchases' => Purchase::with('supplier')->latest()->limit(4)->get(),
            'searchTerm' => $search,
        ]);
    }
}
