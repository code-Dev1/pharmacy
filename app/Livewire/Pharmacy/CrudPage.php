<?php

namespace App\Livewire\Pharmacy;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\Setting;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use App\Models\Supplier;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Arr;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class CrudPage extends Component
{
    use WithPagination;

    public string $module;
    public string $action = 'index';
    public ?int $id = null;
    public string $search = '';
    public string $status = '';
    public array $form = [];
    public ?int $deleteId = null;

    public function mount(string $module, string $action = 'index', ?int $id = null): void
    {
        abort_unless(isset($this->modules()[$module]), 404);

        $this->module = $module;
        $this->action = $action;
        $this->id = $id;

        if (in_array($action, ['edit', 'show'], true)) {
            $record = $this->record();
            $this->form = Arr::only($record->toArray(), array_keys($this->fields()));
        } elseif ($action === 'create') {
            $this->form = $this->defaults();
        } elseif ($module === 'settings') {
            $record = Setting::query()->firstOrCreate(['id' => 1], [
                'pharmacy_name' => __('common.app_name'),
                'currency' => 'AFN',
                'low_stock_threshold' => 10,
                'expiry_alert_days' => 30,
            ]);
            $this->id = $record->id;
            $this->action = 'edit';
            $this->form = Arr::only($record->toArray(), array_keys($this->fields()));
        }
    }

    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function save()
    {
        $config = $this->config();
        abort_if($config['readonly'] ?? false, 403);

        $data = $this->validate($this->rules())['form'];
        foreach ($data as $key => $value) {
            if ($value === '') {
                $data[$key] = null;
            }
        }

        if (($config['creator'] ?? false) && $this->action === 'create') {
            $data['created_by'] = auth()->id();
        }
        if ($this->module === 'sale-returns') {
            $data['type'] = 'sale_return';
        }
        if ($this->module === 'purchase-returns') {
            $data['type'] = 'purchase_return';
        }

        $model = $config['model'];
        $record = $this->action === 'edit' ? $this->record() : new $model();
        $record->fill($data)->save();

        session()->flash('toast', $this->action === 'edit' ? __('common.updated') : __('common.saved'));

        return redirect()->route('pharmacy.show', [$this->module, $record->id]);
    }

    public function confirmDelete(int $id): void
    {
        $this->deleteId = $id;
    }

    public function delete(): void
    {
        $record = $this->query()->findOrFail($this->deleteId);

        foreach ($this->config()['guards'] ?? [] as $relation) {
            if ($record->{$relation}()->exists()) {
                session()->flash('toast', __('common.cannot_delete'));
                $this->dispatch('notify', message: __('common.cannot_delete'), variant: 'warning');
                $this->deleteId = null;
                return;
            }
        }

        $record->delete();
        $this->deleteId = null;
        session()->flash('toast', __('common.deleted'));
        $this->dispatch('notify', message: __('common.deleted'), variant: 'success');
    }

    public function getTitleProperty(): string
    {
        return __($this->config()['title']);
    }

    public function getOptionsProperty(): array
    {
        return [
            'categories' => Category::orderBy('name')->pluck('name', 'id')->all(),
            'suppliers' => Supplier::orderBy('name')->pluck('name', 'id')->all(),
            'customers' => Customer::orderBy('name')->pluck('name', 'id')->all(),
            'expense_categories' => ExpenseCategory::orderBy('name')->pluck('name', 'id')->all(),
            'products' => Product::orderBy('name')->pluck('name', 'id')->all(),
        ];
    }

    protected function record()
    {
        return $this->query()->findOrFail($this->id);
    }

    protected function query(): Builder
    {
        $model = $this->config()['model'];
        $query = $model::query();

        foreach ($this->config()['with'] ?? [] as $relation) {
            $query->with($relation);
        }

        return $query;
    }

    protected function listing(): Builder
    {
        $query = $this->query();

        if ($this->search !== '') {
            $columns = $this->config()['search'] ?? ['name'];
            $query->where(function (Builder $builder) use ($columns) {
                foreach ($columns as $column) {
                    if (str_contains($column, '.')) {
                        [$relation, $field] = explode('.', $column, 2);
                        $builder->orWhereHas($relation, fn (Builder $q) => $q->where($field, 'like', "%{$this->search}%"));
                    } else {
                        $builder->orWhere($column, 'like', "%{$this->search}%");
                    }
                }
            });
        }

        if ($this->status !== '' && in_array('is_active', array_keys($this->fields()), true)) {
            $query->where('is_active', $this->status === 'active');
        }

        return $query->latest();
    }

    protected function fields(): array
    {
        return $this->config()['fields'] ?? [];
    }

    protected function defaults(): array
    {
        return collect($this->fields())->mapWithKeys(fn ($field, $key) => [$key => $field['default'] ?? ''])->all();
    }

    protected function rules(): array
    {
        $rules = [];
        foreach ($this->fields() as $name => $field) {
            $rules["form.$name"] = $field['rules'] ?? ['nullable'];
        }

        return $rules;
    }

    protected function config(): array
    {
        return $this->modules()[$this->module];
    }

    protected function modules(): array
    {
        $money = ['nullable', 'numeric', 'min:0'];
        $text = ['nullable', 'string'];

        return [
            'categories' => ['model' => Category::class, 'title' => 'products.categories', 'search' => ['name'], 'guards' => ['products'], 'fields' => ['name' => ['label' => 'common.name', 'rules' => ['required', 'string', 'max:255']], 'description' => ['label' => 'common.description', 'type' => 'textarea', 'rules' => $text], 'is_active' => ['label' => 'common.status', 'type' => 'checkbox', 'default' => true]]],
            'suppliers' => ['model' => Supplier::class, 'title' => 'suppliers.suppliers', 'search' => ['name', 'phone', 'email'], 'with' => ['purchases'], 'fields' => ['name' => ['label' => 'common.name', 'rules' => ['required', 'string']], 'phone' => ['label' => 'common.phone'], 'email' => ['label' => 'common.email', 'type' => 'email'], 'address' => ['label' => 'common.address', 'type' => 'textarea'], 'contact_person' => ['label' => 'suppliers.contact_person'], 'opening_balance' => ['label' => 'suppliers.opening_balance', 'type' => 'number', 'rules' => $money, 'default' => 0], 'notes' => ['label' => 'common.notes', 'type' => 'textarea'], 'is_active' => ['label' => 'common.status', 'type' => 'checkbox', 'default' => true]]],
            'customers' => ['model' => Customer::class, 'title' => 'customers.customers', 'search' => ['name', 'phone'], 'with' => ['sales'], 'fields' => ['name' => ['label' => 'common.name', 'rules' => ['required', 'string']], 'phone' => ['label' => 'common.phone'], 'address' => ['label' => 'common.address', 'type' => 'textarea'], 'opening_balance' => ['label' => 'customers.opening_balance', 'type' => 'number', 'rules' => $money, 'default' => 0], 'notes' => ['label' => 'common.notes', 'type' => 'textarea']]],
            'products' => ['model' => Product::class, 'title' => 'products.products', 'search' => ['name', 'generic_name', 'barcode', 'sku'], 'with' => ['category', 'productBatches'], 'guards' => ['purchaseItems', 'saleItems'], 'fields' => ['category_id' => ['label' => 'products.category', 'type' => 'select', 'options' => 'categories', 'rules' => ['required', 'exists:categories,id']], 'name' => ['label' => 'common.name', 'rules' => ['required', 'string']], 'generic_name' => ['label' => 'products.generic_name'], 'barcode' => ['label' => 'products.barcode'], 'sku' => ['label' => 'products.sku'], 'strength' => ['label' => 'products.strength'], 'dosage_form' => ['label' => 'products.dosage_form'], 'purchase_price' => ['label' => 'products.purchase_price', 'type' => 'number', 'rules' => $money, 'default' => 0], 'sale_price' => ['label' => 'products.sale_price', 'type' => 'number', 'rules' => $money, 'default' => 0], 'minimum_stock' => ['label' => 'products.minimum_stock', 'type' => 'number', 'rules' => ['nullable', 'integer', 'min:0'], 'default' => 0], 'description' => ['label' => 'common.description', 'type' => 'textarea'], 'is_active' => ['label' => 'common.status', 'type' => 'checkbox', 'default' => true]]],
            'batches' => ['model' => ProductBatch::class, 'title' => 'products.batches', 'readonly' => true, 'search' => ['product.name', 'batch_number'], 'with' => ['product', 'supplier'], 'fields' => []],
            'purchases' => ['model' => Purchase::class, 'title' => 'purchases.purchases', 'readonly' => true, 'search' => ['invoice_no', 'supplier.name'], 'with' => ['supplier', 'items']],
            'purchase-payments' => ['model' => PurchasePayment::class, 'title' => 'purchases.payments', 'readonly' => true, 'search' => ['reference_no', 'supplier.name'], 'with' => ['purchase', 'supplier']],
            'sales' => ['model' => Sale::class, 'title' => 'sales.sales', 'readonly' => true, 'search' => ['invoice_no', 'customer.name'], 'with' => ['customer', 'items']],
            'sale-payments' => ['model' => SalePayment::class, 'title' => 'sales.payments', 'readonly' => true, 'search' => ['reference_no', 'customer.name'], 'with' => ['sale', 'customer']],
            'stock-movements' => ['model' => StockMovement::class, 'title' => 'stock.stock_movements', 'readonly' => true, 'search' => ['product.name', 'type'], 'with' => ['product', 'productBatch']],
            'stock-adjustments' => ['model' => StockAdjustment::class, 'title' => 'stock.stock_adjustments', 'readonly' => true, 'search' => ['adjustment_no', 'reason'], 'with' => ['items']],
            'returns' => ['model' => ReturnModel::class, 'title' => 'returns.returns', 'readonly' => true, 'search' => ['return_no', 'type'], 'with' => ['customer', 'supplier', 'items']],
            'sale-returns' => ['model' => ReturnModel::class, 'title' => 'returns.sale_return', 'creator' => true, 'fields' => ['sale_id' => ['label' => 'sales.sale', 'type' => 'number', 'rules' => ['required', 'exists:sales,id']], 'return_no' => ['label' => 'returns.return_no', 'rules' => ['required', 'string', 'unique:returns,return_no']], 'return_date' => ['label' => 'returns.return_date', 'type' => 'date', 'rules' => ['required', 'date']], 'total_amount' => ['label' => 'returns.total_amount', 'type' => 'number', 'rules' => $money, 'default' => 0], 'notes' => ['label' => 'common.notes', 'type' => 'textarea']]],
            'purchase-returns' => ['model' => ReturnModel::class, 'title' => 'returns.purchase_return', 'creator' => true, 'fields' => ['purchase_id' => ['label' => 'purchases.purchase', 'type' => 'number', 'rules' => ['required', 'exists:purchases,id']], 'return_no' => ['label' => 'returns.return_no', 'rules' => ['required', 'string', 'unique:returns,return_no']], 'return_date' => ['label' => 'returns.return_date', 'type' => 'date', 'rules' => ['required', 'date']], 'total_amount' => ['label' => 'returns.total_amount', 'type' => 'number', 'rules' => $money, 'default' => 0], 'notes' => ['label' => 'common.notes', 'type' => 'textarea']]],
            'expense-categories' => ['model' => ExpenseCategory::class, 'title' => 'expenses.expense_categories', 'search' => ['name'], 'guards' => ['expenses'], 'fields' => ['name' => ['label' => 'common.name', 'rules' => ['required', 'string']], 'description' => ['label' => 'common.description', 'type' => 'textarea']]],
            'expenses' => ['model' => Expense::class, 'title' => 'expenses.expenses', 'creator' => true, 'search' => ['title'], 'with' => ['expenseCategory'], 'fields' => ['expense_category_id' => ['label' => 'expenses.expense_category', 'type' => 'select', 'options' => 'expense_categories', 'rules' => ['required', 'exists:expense_categories,id']], 'title' => ['label' => 'expenses.title', 'rules' => ['required', 'string']], 'amount' => ['label' => 'common.amount', 'type' => 'number', 'rules' => ['required', 'numeric', 'min:0']], 'expense_date' => ['label' => 'expenses.expense_date', 'type' => 'date', 'rules' => ['required', 'date']], 'notes' => ['label' => 'common.notes', 'type' => 'textarea']]],
            'reports' => ['model' => Sale::class, 'title' => 'sidebar.reports', 'readonly' => true, 'report' => true],
            'settings' => ['model' => Setting::class, 'title' => 'settings.settings', 'fields' => ['pharmacy_name' => ['label' => 'settings.pharmacy_name', 'rules' => ['required', 'string']], 'phone' => ['label' => 'common.phone'], 'email' => ['label' => 'common.email'], 'address' => ['label' => 'common.address', 'type' => 'textarea'], 'currency' => ['label' => 'settings.currency', 'rules' => ['required', 'string'], 'default' => 'AFN'], 'invoice_footer' => ['label' => 'settings.invoice_footer', 'type' => 'textarea'], 'low_stock_threshold' => ['label' => 'settings.low_stock_threshold', 'type' => 'number', 'rules' => ['required', 'integer', 'min:0'], 'default' => 10], 'expiry_alert_days' => ['label' => 'settings.expiry_alert_days', 'type' => 'number', 'rules' => ['required', 'integer', 'min:1'], 'default' => 30], 'timezone' => ['label' => 'settings.timezone', 'rules' => ['nullable', 'string'], 'default' => 'Asia/Kabul'], 'date_format' => ['label' => 'settings.date_format', 'rules' => ['nullable', 'string'], 'default' => 'Y-m-d'], 'default_language' => ['label' => 'settings.default_language', 'rules' => ['nullable', 'in:fa,ps,en'], 'default' => 'fa'], 'default_theme' => ['label' => 'settings.default_theme', 'rules' => ['nullable', 'in:light,dark,system'], 'default' => 'light']]],
            'activity-logs' => ['model' => ActivityLog::class, 'title' => 'settings.activity_logs', 'readonly' => true, 'search' => ['action', 'module'], 'with' => ['user']],
        ];
    }

    public function render()
    {
        return view('livewire.pharmacy.crud-page', [
            'records' => $this->action === 'index' && ! ($this->config()['report'] ?? false) ? $this->listing()->paginate(10) : null,
            'fields' => $this->fields(),
            'record' => in_array($this->action, ['show', 'edit'], true) ? $this->record() : null,
            'config' => $this->config(),
        ]);
    }
}
