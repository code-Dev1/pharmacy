<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Pharmacy\CrudPage;
use App\Livewire\Pharmacy\AdvancedPage;
use App\Livewire\Pharmacy\Dashboard;
use App\Livewire\Pharmacy\PurchaseCreate;
use App\Livewire\Pharmacy\SaleCreate;
use App\Livewire\Pharmacy\StockAdjustmentCreate;
use App\Http\Controllers\PharmacyPdfController;

Route::view('/', 'welcome');

Route::post('locale', function () {
    $locale = request()->validate(['locale' => ['required', 'in:fa,ps,en']])['locale'];
    session(['locale' => $locale]);

    return back();
})->name('locale.switch');

Route::get('dashboard', Dashboard::class)
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::view('profile', 'profile')
    ->middleware(['auth'])
    ->name('profile');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('pharmacy/{module}', CrudPage::class)->name('pharmacy.index')->defaults('action', 'index');
    Route::get('pharmacy/{module}/create', CrudPage::class)->name('pharmacy.create')->defaults('action', 'create');
    Route::get('pharmacy/{module}/{id}', CrudPage::class)->whereNumber('id')->name('pharmacy.show')->defaults('action', 'show');
    Route::get('pharmacy/{module}/{id}/edit', CrudPage::class)->whereNumber('id')->name('pharmacy.edit')->defaults('action', 'edit');
    Route::get('purchases/create', PurchaseCreate::class)->name('purchases.create');
    Route::get('sales/create', SaleCreate::class)->name('sales.create');
    Route::get('stock-adjustments/create', StockAdjustmentCreate::class)->name('stock-adjustments.create');
    Route::get('advanced/{page}/{id?}', AdvancedPage::class)->name('advanced.page');
    Route::get('pdf/sales/{sale}', [PharmacyPdfController::class, 'saleInvoice'])->name('pdf.sale');
    Route::get('pdf/purchases/{purchase}', [PharmacyPdfController::class, 'purchaseInvoice'])->name('pdf.purchase');
    Route::get('pdf/reports/{type}', [PharmacyPdfController::class, 'report'])->name('pdf.report');
    Route::get('pdf/customers/{customer}/statement', [PharmacyPdfController::class, 'customerStatement'])->name('pdf.customer.statement');
    Route::get('pdf/suppliers/{supplier}/statement', [PharmacyPdfController::class, 'supplierStatement'])->name('pdf.supplier.statement');
});

require __DIR__.'/auth.php';
