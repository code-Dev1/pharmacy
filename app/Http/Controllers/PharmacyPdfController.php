<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Setting;
use App\Models\Supplier;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class PharmacyPdfController extends Controller
{
    public function saleInvoice(Sale $sale)
    {
        return $this->pdf('pdf.invoice', [
            'title' => __('sales.sale'),
            'document' => $sale->load(['customer', 'items.product', 'payments']),
            'person' => $sale->customer,
            'items' => $sale->items,
        ], "sale-{$sale->invoice_no}.pdf");
    }

    public function saleReceipt(Sale $sale)
    {
        return view('pdf.receipt-print', [
            'title' => __('sales.sale'),
            'document' => $sale->load(['customer', 'items.product', 'payments']),
            'person' => $sale->customer,
            'items' => $sale->items,
            'setting' => Setting::query()->first(),
            'dir' => \App\Support\Locale::direction(),
        ]);
    }

    public function purchaseInvoice(Purchase $purchase)
    {
        return $this->pdf('pdf.invoice', [
            'title' => __('purchases.purchase'),
            'document' => $purchase->load(['supplier', 'items.product', 'payments']),
            'person' => $purchase->supplier,
            'items' => $purchase->items,
        ], "purchase-{$purchase->invoice_no}.pdf");
    }

    public function report(Request $request, string $type)
    {
        [$title, $rows, $totals] = match ($type) {
            'sales-report', 'customer-due-report' => [__('reports.sales_report'), Sale::with('customer')->when($request->from, fn ($q) => $q->whereDate('sale_date', '>=', $request->from))->when($request->to, fn ($q) => $q->whereDate('sale_date', '<=', $request->to))->get(), []],
            'purchase-report', 'supplier-due-report' => [__('reports.purchase_report'), Purchase::with('supplier')->when($request->from, fn ($q) => $q->whereDate('purchase_date', '>=', $request->from))->when($request->to, fn ($q) => $q->whereDate('purchase_date', '<=', $request->to))->get(), []],
            'stock-report' => [__('reports.stock_report'), Product::with('productBatches')->get(), []],
            'low-stock-products', 'low-stock-report' => [__('sidebar.low_stock'), Product::with('productBatches')->get()->filter(fn ($product) => $product->current_stock <= $product->minimum_stock), []],
            'expired-products', 'expiry-report', 'near-expiry-products' => [__('reports.expiry_report'), ProductBatch::with('product')->whereNotNull('expiry_date')->orderBy('expiry_date')->get(), []],
            'expense-report' => [__('reports.expense_report'), Expense::with('expenseCategory')->get(), []],
            'profit-loss-report' => [__('reports.profit_loss_report'), collect(), ['sales' => Sale::sum('total'), 'expenses' => Expense::sum('amount'), 'net' => Sale::sum('total') - Expense::sum('amount')]],
            default => [__('sidebar.reports'), collect(), []],
        };

        return $this->pdf('pdf.report', compact('title', 'rows', 'totals', 'type'), str($type)->slug() . '.pdf');
    }

    public function customerStatement(Customer $customer)
    {
        return $this->pdf('pdf.statement', [
            'title' => __('reports.customer_statement'),
            'person' => $customer->load(['sales.payments', 'salePayments']),
            'documents' => $customer->sales,
            'payments' => $customer->salePayments,
        ], "customer-statement-{$customer->id}.pdf");
    }

    public function supplierStatement(Supplier $supplier)
    {
        return $this->pdf('pdf.statement', [
            'title' => __('reports.supplier_statement'),
            'person' => $supplier->load(['purchases.payments', 'purchasePayments']),
            'documents' => $supplier->purchases,
            'payments' => $supplier->purchasePayments,
        ], "supplier-statement-{$supplier->id}.pdf");
    }

    protected function pdf(string $view, array $data, string $filename)
    {
        $data['setting'] = Setting::query()->first();
        $data['dir'] = \App\Support\Locale::direction();

        return Pdf::loadView($view, $data)->setPaper('a4')->download($filename);
    }
}
