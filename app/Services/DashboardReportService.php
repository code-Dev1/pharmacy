<?php

namespace App\Services;

use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\SaleItem;

class DashboardReportService
{
    public function __construct(
        protected DueCalculationService $dues,
        protected ExpiryAlertService $expiry,
    ) {}

    public function summary(): array
    {
        $salesToday = (float) Sale::whereDate('sale_date', today())->sum('total');
        $costToday = (float) SaleItem::whereHas('sale', fn ($q) => $q->whereDate('sale_date', today()))
            ->with('product')
            ->get()
            ->sum(fn ($item) => $item->quantity * ($item->product?->purchase_price ?? 0));

        return [
            ['label' => __('dashboard.today_sales_total'), 'value' => $salesToday],
            ['label' => __('dashboard.today_purchases_total'), 'value' => Purchase::whereDate('purchase_date', today())->sum('total')],
            ['label' => __('dashboard.today_expenses_total'), 'value' => Expense::whereDate('expense_date', today())->sum('amount')],
            ['label' => __('dashboard.today_profit'), 'value' => $salesToday - $costToday - (float) Expense::whereDate('expense_date', today())->sum('amount')],
            ['label' => __('dashboard.total_products'), 'value' => Product::count()],
            ['label' => __('dashboard.low_stock_products'), 'value' => $this->lowStockProducts()->count()],
            ['label' => __('dashboard.near_expiry_products'), 'value' => $this->expiry->nearExpiry()->count()],
            ['label' => __('dashboard.expired_products'), 'value' => $this->expiry->expired()->count()],
            ['label' => __('dashboard.customer_due_total'), 'value' => $this->dues->customerDue()],
            ['label' => __('dashboard.supplier_due_total'), 'value' => $this->dues->supplierDue()],
        ];
    }

    public function lowStockProducts()
    {
        return Product::with('productBatches')->get()->filter(fn ($product) => $product->current_stock <= $product->minimum_stock);
    }

    public function salesTrend(int $days = 7): array
    {
        return collect(range($days - 1, 0))->map(function ($day) {
            $date = today()->subDays($day);
            return ['date' => $date->format('M d'), 'sales' => (float) Sale::whereDate('sale_date', $date)->sum('total')];
        })->push(['date' => today()->format('M d'), 'sales' => (float) Sale::whereDate('sale_date', today())->sum('total')])->all();
    }
}
