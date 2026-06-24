<?php

namespace App\Livewire\Pharmacy;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;
use App\Services\DashboardReportService;
use App\Services\DuePaymentService;
use App\Services\ExpiryAlertService;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class AdvancedPage extends Component
{
    use WithPagination;

    public string $page;
    public ?int $id = null;
    public array $filters = ['from' => '', 'to' => '', 'payment_status' => '', 'customer_id' => '', 'supplier_id' => '', 'category_id' => ''];
    public array $payment = ['sale_id' => '', 'purchase_id' => '', 'amount' => '', 'payment_date' => '', 'payment_method' => 'cash', 'reference_no' => '', 'notes' => ''];

    public function mount(string $page, ?int $id = null): void
    {
        $this->page = $page;
        $this->id = $id;
        $this->payment['payment_date'] = now()->format('Y-m-d\TH:i');
    }

    public function saveCustomerPayment(DuePaymentService $service)
    {
        $data = $this->validate([
            'payment.sale_id' => ['required', 'exists:sales,id'],
            'payment.amount' => ['required', 'numeric', 'min:0.01'],
            'payment.payment_date' => ['required', 'date'],
            'payment.payment_method' => ['required', 'in:cash,bank,card,other'],
            'payment.reference_no' => ['nullable', 'string'],
            'payment.notes' => ['nullable', 'string'],
        ])['payment'];

        $service->paySale(Sale::findOrFail($data['sale_id']), $data);
        session()->flash('toast', __('common.saved'));
        $this->dispatch('notify', message: __('common.saved'), variant: 'success');
        $this->reset('payment');
        $this->payment['payment_date'] = now()->format('Y-m-d\TH:i');
    }

    public function saveSupplierPayment(DuePaymentService $service)
    {
        $data = $this->validate([
            'payment.purchase_id' => ['required', 'exists:purchases,id'],
            'payment.amount' => ['required', 'numeric', 'min:0.01'],
            'payment.payment_date' => ['required', 'date'],
            'payment.payment_method' => ['required', 'in:cash,bank,card,other'],
            'payment.reference_no' => ['nullable', 'string'],
            'payment.notes' => ['nullable', 'string'],
        ])['payment'];

        $service->payPurchase(Purchase::findOrFail($data['purchase_id']), $data);
        session()->flash('toast', __('common.saved'));
        $this->dispatch('notify', message: __('common.saved'), variant: 'success');
        $this->reset('payment');
        $this->payment['payment_date'] = now()->format('Y-m-d\TH:i');
    }

    public function render()
    {
        return view('livewire.pharmacy.advanced-page', [
            'title' => $this->title(),
            'rows' => $this->rows(),
            'customers' => Customer::orderBy('name')->get(),
            'suppliers' => Supplier::orderBy('name')->get(),
            'salesDue' => Sale::with('customer', 'payments')->where('due_amount', '>', 0)->latest()->get(),
            'purchasesDue' => Purchase::with('supplier', 'payments')->where('due_amount', '>', 0)->latest()->get(),
            'customerDueRows' => $this->customerDueRows()->get(),
            'supplierDueRows' => $this->supplierDueRows()->get(),
            'statement' => $this->statement(),
            'reportTotals' => $this->reportTotals(),
        ]);
    }

    protected function title(): string
    {
        return match ($this->page) {
            'debts' => 'قرضداری',
            'customer-due-payments' => __('sidebar.customer_due_payments'),
            'supplier-due-payments' => __('sidebar.supplier_due_payments'),
            'expired-products' => __('sidebar.expired_products'),
            'near-expiry-products' => __('sidebar.expiring_products'),
            'low-stock-products' => __('sidebar.low_stock'),
            'customer-statement' => __('reports.customer_statement'),
            'supplier-statement' => __('reports.supplier_statement'),
            'sales-report' => __('reports.sales_report'),
            'purchase-report' => __('reports.purchase_report'),
            'stock-report' => __('reports.stock_report'),
            'expiry-report' => __('reports.expiry_report'),
            'profit-loss-report' => __('reports.profit_loss_report'),
            'customer-due-report' => __('sidebar.customer_due_report'),
            'supplier-due-report' => __('sidebar.supplier_due_report'),
            'expense-report' => __('reports.expense_report'),
            default => __('sidebar.reports'),
        };
    }

    protected function rows()
    {
        $expiry = app(ExpiryAlertService::class);

        return match ($this->page) {
            'debts' => collect(),
            'expired-products' => $expiry->expired()->paginate(10),
            'near-expiry-products' => $expiry->nearExpiry()->paginate(10),
            'low-stock-products' => app(DashboardReportService::class)->lowStockProducts(),
            'sales-report' => $this->salesReport()->paginate(10),
            'customer-due-payments', 'customer-due-report' => $this->customerDueRows()->paginate(10),
            'purchase-report' => $this->purchaseReport()->paginate(10),
            'supplier-due-payments', 'supplier-due-report' => $this->supplierDueRows()->paginate(10),
            'stock-report' => Product::with('productBatches')->paginate(10),
            'expiry-report' => ProductBatch::with('product')->whereNotNull('expiry_date')->orderBy('expiry_date')->paginate(10),
            'expense-report' => $this->expenseReport()->paginate(10),
            'profit-loss-report' => Sale::with('customer')->latest()->paginate(10),
            default => collect(),
        };
    }

    protected function salesReport()
    {
        return Sale::with('customer')
            ->when($this->filters['from'], fn ($q) => $q->whereDate('sale_date', '>=', $this->filters['from']))
            ->when($this->filters['to'], fn ($q) => $q->whereDate('sale_date', '<=', $this->filters['to']))
            ->when($this->filters['customer_id'], fn ($q) => $q->where('customer_id', $this->filters['customer_id']))
            ->when($this->filters['payment_status'], fn ($q) => $q->where('payment_status', $this->filters['payment_status']))
            ->latest();
    }

    protected function purchaseReport()
    {
        return Purchase::with('supplier')
            ->when($this->filters['from'], fn ($q) => $q->whereDate('purchase_date', '>=', $this->filters['from']))
            ->when($this->filters['to'], fn ($q) => $q->whereDate('purchase_date', '<=', $this->filters['to']))
            ->when($this->filters['supplier_id'], fn ($q) => $q->where('supplier_id', $this->filters['supplier_id']))
            ->when($this->filters['payment_status'], fn ($q) => $q->where('payment_status', $this->filters['payment_status']))
            ->latest();
    }

    protected function customerDueRows()
    {
        return Customer::query()
            ->whereHas('sales', fn ($query) => $query->where('due_amount', '>', 0))
            ->withCount(['sales as due_documents_count' => fn ($query) => $query->where('due_amount', '>', 0)])
            ->withSum(['sales as due_amount' => fn ($query) => $query->where('due_amount', '>', 0)], 'due_amount')
            ->orderByDesc('due_amount');
    }

    protected function supplierDueRows()
    {
        return Supplier::query()
            ->whereHas('purchases', fn ($query) => $query->where('due_amount', '>', 0))
            ->withCount(['purchases as due_documents_count' => fn ($query) => $query->where('due_amount', '>', 0)])
            ->withSum(['purchases as due_amount' => fn ($query) => $query->where('due_amount', '>', 0)], 'due_amount')
            ->orderByDesc('due_amount');
    }

    protected function expenseReport()
    {
        return Expense::with('expenseCategory')
            ->when($this->filters['from'], fn ($q) => $q->whereDate('expense_date', '>=', $this->filters['from']))
            ->when($this->filters['to'], fn ($q) => $q->whereDate('expense_date', '<=', $this->filters['to']))
            ->latest();
    }

    protected function statement(): ?array
    {
        if ($this->page === 'customer-statement' && $this->id) {
            $customer = Customer::with(['sales.payments'])->findOrFail($this->id);
            return ['person' => $customer, 'documents' => $customer->sales, 'payments' => $customer->salePayments, 'due' => $customer->sales->sum('due_amount')];
        }

        if ($this->page === 'supplier-statement' && $this->id) {
            $supplier = Supplier::with(['purchases.payments'])->findOrFail($this->id);
            return ['person' => $supplier, 'documents' => $supplier->purchases, 'payments' => $supplier->purchasePayments, 'due' => $supplier->purchases->sum('due_amount')];
        }

        return null;
    }

    protected function reportTotals(): array
    {
        return match ($this->page) {
            'sales-report' => ['total' => (clone $this->salesReport())->sum('total'), 'paid' => (clone $this->salesReport())->sum('paid_amount'), 'due' => (clone $this->salesReport())->sum('due_amount')],
            'purchase-report' => ['total' => (clone $this->purchaseReport())->sum('total'), 'paid' => (clone $this->purchaseReport())->sum('paid_amount'), 'due' => (clone $this->purchaseReport())->sum('due_amount')],
            'expense-report' => ['total' => (clone $this->expenseReport())->sum('amount')],
            'profit-loss-report' => ['total' => Sale::sum('total') - Expense::sum('amount')],
            default => [],
        };
    }
}
