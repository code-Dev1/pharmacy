<?php

namespace App\Livewire\Pharmacy;

use App\Models\Category;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\Supplier;
use App\Services\PurchaseService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class PurchaseCreate extends Component
{
    public array $form = ['supplier_id' => '', 'purchase_date' => '', 'discount' => 0, 'tax' => 0, 'paid_amount' => 0, 'payment_method' => 'cash', 'notes' => ''];
    public array $items = [];
    public bool $showQuickProductForm = false;
    public array $quickProduct = ['item_index' => null, 'name' => '', 'barcode' => ''];

    public function mount(): void
    {
        $this->form['purchase_date'] = now()->format('Y-m-d');
        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = ['product_id' => '', 'quantity' => 1, 'unit_price' => 0, 'sale_price' => 0, 'discount' => 0, 'batch_number' => '', 'manufacture_date' => '', 'expiry_date' => ''];
    }

    public function removeItem(int $index): void
    {
        unset($this->items[$index]);
        $this->items = array_values($this->items);
    }

    public function openQuickProductForm(int $index): void
    {
        $this->quickProduct = ['item_index' => $index, 'name' => '', 'barcode' => ''];
        $this->showQuickProductForm = true;
    }

    public function createQuickProduct(): void
    {
        $validated = $this->validate([
            'quickProduct.item_index' => ['required', 'integer'],
            'quickProduct.name' => ['required', 'string', 'max:255', 'unique:products,name'],
            'quickProduct.barcode' => ['nullable', 'string', 'max:255', 'unique:products,barcode'],
        ]);

        $index = (int) $validated['quickProduct']['item_index'];

        if (! array_key_exists($index, $this->items)) {
            $this->resetQuickProductForm();

            return;
        }

        $category = Category::firstOrCreate(
            ['name' => 'عمومی'],
            ['description' => null, 'is_active' => true],
        );

        $product = Product::create([
            'category_id' => $category->id,
            'name' => $validated['quickProduct']['name'],
            'barcode' => $validated['quickProduct']['barcode'] ?: null,
            'purchase_price' => (float) ($this->items[$index]['unit_price'] ?? 0),
            'sale_price' => (float) ($this->items[$index]['sale_price'] ?? 0),
            'minimum_stock' => 0,
            'is_active' => true,
        ]);

        $this->items[$index]['product_id'] = $product->id;
        $this->resetQuickProductForm();
        $this->dispatch('notify', type: 'success', message: __('common.saved'));
    }

    public function resetQuickProductForm(): void
    {
        $this->showQuickProductForm = false;
        $this->quickProduct = ['item_index' => null, 'name' => '', 'barcode' => ''];
    }

    public function save(PurchaseService $service)
    {
        $this->validate([
            'form.supplier_id' => ['required', 'exists:suppliers,id'],
            'form.purchase_date' => ['required', 'date'],
            'form.discount' => ['nullable', 'numeric', 'min:0'],
            'form.tax' => ['nullable', 'numeric', 'min:0'],
            'form.paid_amount' => ['nullable', 'numeric', 'min:0'],
            'items.*.product_id' => ['required', 'exists:products,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.unit_price' => ['required', 'numeric', 'min:0'],
            'items.*.discount' => ['nullable', 'numeric', 'min:0'],
        ]);

        $subtotal = collect($this->items)->sum(fn ($item) => ((float) $item['quantity'] * (float) $item['unit_price']) - (float) ($item['discount'] ?? 0));
        $total = $subtotal - (float) $this->form['discount'] + (float) $this->form['tax'];
        $paid = min((float) $this->form['paid_amount'], $total);
        $due = $total - $paid;
        $purchase = $service->create([
            'supplier_id' => $this->form['supplier_id'],
            'invoice_no' => 'PUR-' . now()->format('Y') . '-' . str_pad((string) (Purchase::count() + 1), 6, '0', STR_PAD_LEFT),
            'purchase_date' => $this->form['purchase_date'],
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

        session()->flash('toast', __('common.saved'));

        return redirect()->route('pharmacy.show', ['purchases', $purchase->id]);
    }

    public function render()
    {
        return view('livewire.pharmacy.purchase-create', [
            'suppliers' => Supplier::orderBy('name')->get(),
            'products' => Product::orderBy('name')->get(),
            'subtotal' => collect($this->items)->sum(fn ($item) => ((float) ($item['quantity'] ?: 0) * (float) ($item['unit_price'] ?: 0)) - (float) ($item['discount'] ?: 0)),
        ]);
    }
}
