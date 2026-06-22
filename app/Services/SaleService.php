<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\Sale;
use App\Models\SalePayment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class SaleService
{
    public function create(array $data, array $items): Sale
    {
        return DB::transaction(function () use ($data, $items) {
            $sale = Sale::create($data);

            foreach ($items as $item) {
                $quantity = (int) $item['quantity'];
                $batches = ProductBatch::where('product_id', $item['product_id'])
                    ->where('remaining_quantity', '>', 0)
                    ->where(function ($query) {
                        $query->whereNull('expiry_date')->orWhereDate('expiry_date', '>=', today());
                    })
                    ->orderByRaw('expiry_date is null')
                    ->orderBy('expiry_date')
                    ->lockForUpdate()
                    ->get();

                if ($batches->sum('remaining_quantity') < $quantity) {
                    throw ValidationException::withMessages(['cart' => __('products.available_stock')]);
                }

                foreach ($batches as $batch) {
                    if ($quantity <= 0) {
                        break;
                    }

                    $take = min($quantity, $batch->remaining_quantity);
                    $batch->decrement('remaining_quantity', $take);

                    $sale->items()->create([
                        'product_id' => $item['product_id'],
                        'product_batch_id' => $batch->id,
                        'quantity' => $take,
                        'unit_price' => $item['unit_price'],
                        'discount' => $item['discount'] ?? 0,
                        'total' => ($take * $item['unit_price']) - ($item['discount'] ?? 0),
                    ]);

                    StockMovement::create([
                        'product_id' => $item['product_id'],
                        'product_batch_id' => $batch->id,
                        'type' => 'sale',
                        'reference_type' => Sale::class,
                        'reference_id' => $sale->id,
                        'quantity' => -$take,
                        'movement_date' => $sale->sale_date,
                        'created_by' => $sale->created_by,
                    ]);

                    $quantity -= $take;
                }
            }

            if ($sale->paid_amount > 0) {
                SalePayment::create([
                    'sale_id' => $sale->id,
                    'customer_id' => $sale->customer_id,
                    'amount' => $sale->paid_amount,
                    'payment_date' => $sale->sale_date,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'created_by' => $sale->created_by,
                ]);
            }

            return $sale;
        });
    }
}
