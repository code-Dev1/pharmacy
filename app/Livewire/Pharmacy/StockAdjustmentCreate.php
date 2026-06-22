<?php

namespace App\Livewire\Pharmacy;

use App\Models\ProductBatch;
use App\Models\StockAdjustment;
use App\Services\StockAdjustmentService;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class StockAdjustmentCreate extends Component
{
    public array $form = ['adjustment_date' => '', 'reason' => '', 'notes' => ''];
    public array $items = [];

    public function mount(): void
    {
        $this->form['adjustment_date'] = now()->format('Y-m-d\TH:i');
        $this->addItem();
    }

    public function addItem(): void
    {
        $this->items[] = ['product_batch_id' => '', 'system_quantity' => 0, 'actual_quantity' => 0];
    }

    public function updatedItems($value, string $key): void
    {
        if (str_ends_with($key, 'product_batch_id')) {
            [$index] = explode('.', $key);
            $batch = ProductBatch::find($value);
            $this->items[$index]['system_quantity'] = $batch?->remaining_quantity ?? 0;
            $this->items[$index]['actual_quantity'] = $batch?->remaining_quantity ?? 0;
        }
    }

    public function save(StockAdjustmentService $service)
    {
        $this->validate([
            'form.adjustment_date' => ['required', 'date'],
            'items.*.product_batch_id' => ['required', 'exists:product_batches,id'],
            'items.*.actual_quantity' => ['required', 'integer', 'min:0'],
        ]);

        $adjustment = $service->create([
            'adjustment_no' => 'ADJ-' . now()->format('Y') . '-' . str_pad((string) (StockAdjustment::count() + 1), 6, '0', STR_PAD_LEFT),
            'adjustment_date' => $this->form['adjustment_date'],
            'reason' => $this->form['reason'],
            'notes' => $this->form['notes'],
            'created_by' => auth()->id(),
        ], $this->items);

        session()->flash('toast', __('common.saved'));

        return redirect()->route('pharmacy.show', ['stock-adjustments', $adjustment->id]);
    }

    public function render()
    {
        return view('livewire.pharmacy.stock-adjustment-create', [
            'batches' => ProductBatch::with('product')->where('remaining_quantity', '>=', 0)->orderByDesc('id')->get(),
        ]);
    }
}
