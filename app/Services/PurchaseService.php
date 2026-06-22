<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\StockMovement;
use Illuminate\Support\Facades\DB;

class PurchaseService
{
    public function create(array $data, array $items): Purchase
    {
        return DB::transaction(function () use ($data, $items) {
            $purchase = Purchase::create($data);

            foreach ($items as $item) {
                $batch = ProductBatch::create([
                    'product_id' => $item['product_id'],
                    'supplier_id' => $purchase->supplier_id,
                    'batch_number' => $item['batch_number'] ?? null,
                    'manufacture_date' => $item['manufacture_date'] ?? null,
                    'expiry_date' => $item['expiry_date'] ?? null,
                    'purchase_price' => $item['unit_price'],
                    'sale_price' => $item['sale_price'] ?? $item['unit_price'],
                    'quantity' => $item['quantity'],
                    'remaining_quantity' => $item['quantity'],
                ]);

                $purchase->items()->create([
                    ...$item,
                    'product_batch_id' => $batch->id,
                    'total' => ($item['quantity'] * $item['unit_price']) - ($item['discount'] ?? 0),
                ]);

                StockMovement::create([
                    'product_id' => $item['product_id'],
                    'product_batch_id' => $batch->id,
                    'type' => 'purchase',
                    'reference_type' => Purchase::class,
                    'reference_id' => $purchase->id,
                    'quantity' => $item['quantity'],
                    'movement_date' => $purchase->purchase_date,
                    'created_by' => $purchase->created_by,
                ]);
            }

            if ($purchase->paid_amount > 0) {
                PurchasePayment::create([
                    'purchase_id' => $purchase->id,
                    'supplier_id' => $purchase->supplier_id,
                    'amount' => $purchase->paid_amount,
                    'payment_date' => $purchase->purchase_date,
                    'payment_method' => $data['payment_method'] ?? 'cash',
                    'created_by' => $purchase->created_by,
                ]);
            }

            return $purchase;
        });
    }
}
