<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pharmacy\CrudPage;
use App\Livewire\Pharmacy\AdvancedPage;
use App\Livewire\Pharmacy\CarpetDashboard;
use App\Livewire\Pharmacy\Dashboard;
use App\Livewire\Pharmacy\PurchaseCreate;
use App\Livewire\Pharmacy\SaleCreate;
use App\Livewire\Pharmacy\StockAdjustmentCreate;
use App\Http\Controllers\DatabaseBackupController;
use App\Http\Controllers\PharmacyPdfController;
use App\Models\ActivityLog;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;

Route::get('/', fn () => redirect()->route(auth()->check() ? 'dashboard' : 'login'));

Route::post('locale', function () {
    $locale = request()->validate(['locale' => ['required', 'in:fa,ps,en']])['locale'];
    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::get('carpets', CarpetDashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('carpet.dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('search', function () {
        $query = trim((string) request('q', ''));
        $like = "%{$query}%";

        $results = [
            'products' => $query === '' ? collect() : Product::query()
                ->where(fn ($builder) => $builder
                    ->where('name', 'like', $like)
                    ->orWhere('generic_name', 'like', $like)
                    ->orWhere('barcode', 'like', $like)
                    ->orWhere('sku', 'like', $like))
                ->latest()
                ->limit(8)
                ->get(),
            'customers' => $query === '' ? collect() : Customer::query()
                ->where(fn ($builder) => $builder
                    ->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('address', 'like', $like))
                ->latest()
                ->limit(8)
                ->get(),
            'suppliers' => $query === '' ? collect() : Supplier::query()
                ->where(fn ($builder) => $builder
                    ->where('name', 'like', $like)
                    ->orWhere('phone', 'like', $like)
                    ->orWhere('email', 'like', $like))
                ->latest()
                ->limit(8)
                ->get(),
            'sales' => $query === '' ? collect() : Sale::query()
                ->with('customer')
                ->where('invoice_no', 'like', $like)
                ->latest()
                ->limit(8)
                ->get(),
            'purchases' => $query === '' ? collect() : Purchase::query()
                ->with('supplier')
                ->where('invoice_no', 'like', $like)
                ->latest()
                ->limit(8)
                ->get(),
            'batches' => $query === '' ? collect() : ProductBatch::query()
                ->with('product')
                ->where('batch_number', 'like', $like)
                ->latest()
                ->limit(8)
                ->get(),
            'expenses' => $query === '' ? collect() : Expense::query()
                ->where('title', 'like', $like)
                ->latest()
                ->limit(8)
                ->get(),
            'activities' => $query === '' ? collect() : ActivityLog::query()
                ->where(fn ($builder) => $builder
                    ->where('action', 'like', $like)
                    ->orWhere('module', 'like', $like)
                    ->orWhere('description', 'like', $like))
                ->latest()
                ->limit(8)
                ->get(),
        ];

        $sections = [
            'products' => $results['products']->map(fn ($row) => [
                'label' => $row->name,
                'description' => trim(($row->generic_name ?? '') . ' ' . ($row->sku ?? '')),
                'href' => route('pharmacy.show', ['products', $row->id]),
                'badge' => 'Product',
            ]),
            'customers' => $results['customers']->map(fn ($row) => [
                'label' => $row->name,
                'description' => trim(($row->phone ?? '') . ' ' . ($row->address ?? '')),
                'href' => route('pharmacy.show', ['customers', $row->id]),
                'badge' => 'Customer',
            ]),
            'suppliers' => $results['suppliers']->map(fn ($row) => [
                'label' => $row->name,
                'description' => trim(($row->phone ?? '') . ' ' . ($row->email ?? '')),
                'href' => route('pharmacy.show', ['suppliers', $row->id]),
                'badge' => 'Supplier',
            ]),
            'sales' => $results['sales']->map(fn ($row) => [
                'label' => $row->invoice_no,
                'description' => ($row->customer?->name ?? __('common.walk_in_customer')) . ' - ' . number_format((float) $row->total, 2),
                'href' => route('pharmacy.show', ['sales', $row->id]),
                'badge' => 'Sale',
            ]),
            'purchases' => $results['purchases']->map(fn ($row) => [
                'label' => $row->invoice_no,
                'description' => ($row->supplier?->name ?? '-') . ' - ' . number_format((float) $row->total, 2),
                'href' => route('pharmacy.show', ['purchases', $row->id]),
                'badge' => 'Purchase',
            ]),
            'batches' => $results['batches']->map(fn ($row) => [
                'label' => $row->batch_number ?? ('#' . $row->id),
                'description' => ($row->product?->name ?? '-') . ' - ' . optional($row->expiry_date)->format('Y-m-d'),
                'href' => route('pharmacy.show', ['batches', $row->id]),
                'badge' => 'Batch',
            ]),
            'expenses' => $results['expenses']->map(fn ($row) => [
                'label' => $row->title,
                'description' => number_format((float) $row->amount, 2),
                'href' => route('pharmacy.show', ['expenses', $row->id]),
                'badge' => 'Expense',
            ]),
            'activities' => $results['activities']->map(fn ($row) => [
                'label' => $row->module . ' / ' . $row->action,
                'description' => $row->description,
                'href' => route('pharmacy.show', ['activity-logs', $row->id]),
                'badge' => 'Activity',
            ]),
        ];

        return view('search', ['query' => $query, 'sections' => $sections]);
    })->name('global.search');

    Route::get('pharmacy/{module}', CrudPage::class)->name('pharmacy.index')->defaults('action', 'index');
    Route::get('pharmacy/{module}/create', CrudPage::class)->name('pharmacy.create')->defaults('action', 'create');
    Route::get('pharmacy/{module}/{id}', CrudPage::class)->whereNumber('id')->name('pharmacy.show')->defaults('action', 'show');
    Route::get('pharmacy/{module}/{id}/edit', CrudPage::class)->whereNumber('id')->name('pharmacy.edit')->defaults('action', 'edit');
    Route::get('purchases/create', PurchaseCreate::class)->name('purchases.create');
    Route::get('sales/create', SaleCreate::class)->name('sales.create');
    Route::get('stock-adjustments/create', StockAdjustmentCreate::class)->name('stock-adjustments.create');
    Route::get('advanced/{page}/{id?}', AdvancedPage::class)->name('advanced.page');
    Route::get('backup', [DatabaseBackupController::class, 'index'])->name('backup.index');
    Route::get('backup/download', [DatabaseBackupController::class, 'download'])->name('backup.download');
    Route::post('backup/restore', [DatabaseBackupController::class, 'restore'])->name('backup.restore');
    Route::get('pdf/sales/{sale}', [PharmacyPdfController::class, 'saleInvoice'])->name('pdf.sale');
    Route::get('receipts/sales/{sale}', [PharmacyPdfController::class, 'saleReceipt'])->name('receipt.sale');
    Route::get('pdf/purchases/{purchase}', [PharmacyPdfController::class, 'purchaseInvoice'])->name('pdf.purchase');
    Route::get('pdf/reports/{type}', [PharmacyPdfController::class, 'report'])->name('pdf.report');
    Route::get('pdf/customers/{customer}/statement', [PharmacyPdfController::class, 'customerStatement'])->name('pdf.customer.statement');
    Route::get('pdf/suppliers/{supplier}/statement', [PharmacyPdfController::class, 'supplierStatement'])->name('pdf.supplier.statement');
});

require __DIR__.'/auth.php';
