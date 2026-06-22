<?php

namespace App\Services;

use App\Models\Customer;
use App\Models\Purchase;
use App\Models\Sale;
use App\Models\Supplier;

class DueCalculationService
{
    public function customerDue(?Customer $customer = null): float
    {
        $query = Sale::query()->where('due_amount', '>', 0);

        if ($customer) {
            $query->where('customer_id', $customer->id);
        }

        return (float) $query->sum('due_amount');
    }

    public function supplierDue(?Supplier $supplier = null): float
    {
        $query = Purchase::query()->where('due_amount', '>', 0);

        if ($supplier) {
            $query->where('supplier_id', $supplier->id);
        }

        return (float) $query->sum('due_amount');
    }

    public function recalculateSale(Sale $sale): Sale
    {
        $paid = (float) $sale->payments()->sum('amount');
        $due = max((float) $sale->total - $paid, 0);

        $sale->update([
            'paid_amount' => $paid,
            'due_amount' => $due,
            'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
        ]);

        return $sale->refresh();
    }

    public function recalculatePurchase(Purchase $purchase): Purchase
    {
        $paid = (float) $purchase->payments()->sum('amount');
        $due = max((float) $purchase->total - $paid, 0);

        $purchase->update([
            'paid_amount' => $paid,
            'due_amount' => $due,
            'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
        ]);

        return $purchase->refresh();
    }
}
