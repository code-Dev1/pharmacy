<?php

namespace App\Livewire\Pharmacy;

use App\Models\Customer;
use App\Models\Product;
use App\Models\Sale;
use App\Services\SaleService;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Throwable;

#[Layout('layouts.app')]
class SaleCreate extends Component
{
    public array $form = ['customer_id' => '', 'sale_date' => '', 'discount' => 0, 'tax' => 0, 'paid_amount' => 0, 'payment_method' => 'cash', 'notes' => ''];
    public array $items = [];
    public string $productSearch = '';
    public bool $embedded = false;

    public function mount(bool $embedded = false): void
    {
        $this->embedded = $embedded;
        $this->form['sale_date'] = now()->format('Y-m-d\TH:i');
    }

    public function addProduct(int $productId): void
    {
        $product = Product::findOrFail($productId);

        foreach ($this->items as $index => $item) {
            if ((int) $item['product_id'] === $product->id) {
                $this->items[$index]['quantity'] = (int) $item['quantity'] + 1;
                $this->productSearch = '';

                return;
            }
        }

        $this->items[] = ['product_id' => $product->id, 'name' => $product->name, 'quantity' => 1, 'unit_price' => $product->sale_price, 'discount' => 0];
        $this->productSearch = '';
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function save(SaleService $service)
    {
        $validator = Validator::make([
            'form' => $this->form,
            'items' => $this->items,
        ], [
            'form.customer_id' => ['nullable', 'exists:customers,id'],
            'form.sale_date' => ['required', 'date'],
            'form.discount' => ['nullable', 'numeric', 'min:0'],
            'form.tax' => ['nullable', 'numeric', 'min:0'],
            'form.paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
        ]);

        if ($validator->fails()) {
            $this->setErrorBag($validator->errors());
            $this->notifyOrderFailed($validator->errors()->first());

            return null;
        }

        $subtotal = collect($this->items)->sum(fn ($item) => ((float) $item['quantity'] * (float) $item['unit_price']) - (float) ($item['discount'] ?? 0));
        $total = $subtotal - (float) $this->form['discount'] + (float) $this->form['tax'];
        $paid = min((float) $this->form['paid_amount'], $total);
        $due = $total - $paid;

        try {
            $sale = $service->create([
                'customer_id' => $this->form['customer_id'] ?: null,
                'invoice_no' => 'SAL-' . now()->format('Y') . '-' . str_pad((string) (Sale::count() + 1), 6, '0', STR_PAD_LEFT),
                'sale_date' => $this->form['sale_date'],
                'subtotal' => $subtotal,
                'discount' => $this->form['discount'],
                'tax' => $this->form['tax'],
                'total' => $total,
                'paid_amount' => $paid,
                'due_amount' => $due,
                'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                'payment_method' => $this->form['payment_method'],
                'notes' => $this->form['notes'],
                'created_by' => auth()->id(),
            ], $this->items);
        } catch (ValidationException $exception) {
            foreach ($exception->errors() as $field => $messages) {
                $this->addError($field, $messages[0] ?? __('common.order_failed'));
            }

            $this->notifyOrderFailed(collect($exception->errors())->flatten()->first());

            return null;
        } catch (Throwable $exception) {
            report($exception);
            $this->notifyOrderFailed();

            return null;
        }

        session()->flash('toast', __('common.saved'));

        if ($this->embedded) {
            $this->resetSaleForm();
            $this->dispatch('sale-created');
            $this->dispatch('notify', message: __('common.saved'), variant: 'success');

            return null;
        }

        return redirect()->route('pharmacy.show', ['sales', $sale->id]);
    }

    private function resetSaleForm(): void
    {
        $this->form = ['customer_id' => '', 'sale_date' => now()->format('Y-m-d\TH:i'), 'discount' => 0, 'tax' => 0, 'paid_amount' => 0, 'payment_method' => 'cash', 'notes' => ''];
        $this->items = [];
        $this->productSearch = '';
    }

    private function notifyOrderFailed(?string $message = null): void
    {
        $this->dispatch('notify', message: $message ?: __('common.order_failed'), variant: 'danger');
    }

    public function render()
    {
        return view('livewire.pharmacy.sale-create', [
            'customers' => Customer::orderBy('name')->get(),
            'products' => Product::with('productBatches')
                ->when($this->productSearch !== '', fn ($q) => $q->where('name', 'like', "%{$this->productSearch}%")->orWhere('barcode', 'like', "%{$this->productSearch}%")->orWhere('sku', 'like', "%{$this->productSearch}%"))
                ->limit(8)
                ->get(),
            'subtotal' => collect($this->items)->sum(fn ($item) => ((float) ($item['quantity'] ?: 0) * (float) ($item['unit_price'] ?: 0)) - (float) ($item['discount'] ?: 0)),
        ]);
    }
}
