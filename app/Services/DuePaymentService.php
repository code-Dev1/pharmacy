<?php

namespace App\Services;

use App\Models\Purchase;
use App\Models\PurchasePayment;
use App\Models\Sale;
use App\Models\SalePayment;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class DuePaymentService
{
    public function paySale(Sale $sale, array $data): SalePayment
    {
        if ((float) $data['amount'] > (float) $sale->due_amount) {
            throw ValidationException::withMessages(['amount' => __('payments.amount_exceeds_due')]);
        }

        return DB::transaction(function () use ($sale, $data) {
            $payment = SalePayment::create([
                'sale_id' => $sale->id,
                'customer_id' => $sale->customer_id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            app(DueCalculationService::class)->recalculateSale($sale);

            return $payment;
        });
    }

    public function payPurchase(Purchase $purchase, array $data): PurchasePayment
    {
        if ((float) $data['amount'] > (float) $purchase->due_amount) {
            throw ValidationException::withMessages(['amount' => __('payments.amount_exceeds_due')]);
        }

        return DB::transaction(function () use ($purchase, $data) {
            $payment = PurchasePayment::create([
                'purchase_id' => $purchase->id,
                'supplier_id' => $purchase->supplier_id,
                'amount' => $data['amount'],
                'payment_date' => $data['payment_date'],
                'payment_method' => $data['payment_method'],
                'reference_no' => $data['reference_no'] ?? null,
                'notes' => $data['notes'] ?? null,
                'created_by' => auth()->id(),
            ]);

            app(DueCalculationService::class)->recalculatePurchase($purchase);

            return $payment;
        });
    }
}
