<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\StockAdjustment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class StockAdjustmentService
{
    public function create(array $data, array $items): StockAdjustment
    {
        return DB::transaction(function () use ($data, $items) {
            $adjustment = StockAdjustment::create($data);

            foreach ($items as $item) {
                $batch = ProductBatch::lockForUpdate()->findOrFail($item['product_batch_id']);
                $difference = (int) $item['actual_quantity'] - (int) $batch->remaining_quantity;
                $type = $difference >= 0 ? 'increase' : 'decrease';
                $batch->update(['remaining_quantity' => (int) $item['actual_quantity']]);

                $adjustment->items()->create([
                    'product_id' => $batch->product_id,
                    'product_batch_id' => $batch->id,
                    'system_quantity' => $item['system_quantity'],
                    'actual_quantity' => $item['actual_quantity'],
                    'difference' => $difference,
                    'type' => $type,
                ]);

                StockMovement::create([
                    'product_id' => $batch->product_id,
                    'product_batch_id' => $batch->id,
                    'type' => $difference >= 0 ? 'adjustment_add' : 'adjustment_sub',
                    'reference_type' => StockAdjustment::class,
                    'reference_id' => $adjustment->id,
                    'quantity' => $difference,
                    'movement_date' => $adjustment->adjustment_date,
                    'notes' => $adjustment->reason,
                    'created_by' => $adjustment->created_by,
                ]);
            }

            return $adjustment;
        });
    }
}
