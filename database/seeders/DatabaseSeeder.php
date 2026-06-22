<?php

namespace Database\Seeders;

use App\Models\ActivityLog;
use App\Models\Category;
use App\Models\Customer;
use App\Models\Expense;
use App\Models\ExpenseCategory;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchasePayment;
use App\Models\ReturnItem;
use App\Models\ReturnModel;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\SalePayment;
use App\Models\Setting;
use App\Models\StockAdjustment;
use App\Models\StockAdjustmentItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        DB::transaction(function (): void {
            $user = User::query()->updateOrCreate(
                ['email' => 'test@example.com'],
                [
                    'name' => 'Test User',
                    'password' => Hash::make('password'),
                    'email_verified_at' => now(),
                ],
            );

            Setting::query()->updateOrCreate(
                ['id' => 1],
                [
                    'pharmacy_name' => 'Parmicy Demo Pharmacy',
                    'phone' => '+93 700 000 111',
                    'email' => 'demo@parmicy.test',
                    'address' => 'Kabul, Afghanistan',
                    'currency' => 'AFN',
                    'invoice_footer' => 'Thank you for choosing Parmicy.',
                    'low_stock_threshold' => 12,
                    'expiry_alert_days' => 45,
                    'timezone' => 'Asia/Kabul',
                    'date_format' => 'Y-m-d',
                    'default_language' => 'fa',
                    'default_theme' => 'light',
                ],
            );

            $categories = collect([
                ['Analgesics', 'Pain relief and anti-inflammatory medicines'],
                ['Antibiotics', 'Prescription antimicrobial medicines'],
                ['Vitamins', 'Supplements and daily nutrition'],
                ['Diabetes Care', 'Glucose and diabetic support products'],
                ['Cardiology', 'Blood pressure and heart medicines'],
                ['Pediatrics', 'Child-safe syrups and drops'],
            ])->mapWithKeys(fn ($row) => [
                $row[0] => Category::query()->updateOrCreate(
                    ['name' => $row[0]],
                    ['description' => $row[1], 'is_active' => true],
                ),
            ]);

            $suppliers = collect([
                ['MedLife Distribution', 'supplier1@parmicy.test', '+93 700 100 100', 'Kabul Warehouse'],
                ['Kabul Pharma Supply', 'supplier2@parmicy.test', '+93 700 200 200', 'Shahr-e Naw, Kabul'],
                ['Asia Health Imports', 'supplier3@parmicy.test', '+93 700 300 300', 'Herat Main Road'],
                ['Noor Medical Co', 'supplier4@parmicy.test', '+93 700 400 400', 'Mazar Industrial Zone'],
            ])->mapWithKeys(fn ($row) => [
                $row[0] => Supplier::query()->updateOrCreate(
                    ['email' => $row[1]],
                    [
                        'name' => $row[0],
                        'phone' => $row[2],
                        'address' => $row[3],
                        'opening_balance' => fake()->numberBetween(1500, 18000),
                        'is_active' => true,
                    ],
                ),
            ]);

            $customers = collect([
                ['Walk-in Ahmed', '+93 799 110 001', 'Karte 4, Kabul', 0],
                ['Fatima Clinic', '+93 799 110 002', 'Deh Afghanan, Kabul', 4200],
                ['Maiwand Health Center', '+93 799 110 003', 'Maiwand Road', 8700],
                ['Zahra Akbari', '+93 799 110 004', 'Taimani, Kabul', 1200],
                ['Omid Pharmacy Branch', '+93 799 110 005', 'Company Road', 15200],
                ['Shafiullah Noori', '+93 799 110 006', 'Khair Khana', 600],
            ])->mapWithKeys(fn ($row) => [
                $row[0] => Customer::query()->updateOrCreate(
                    ['phone' => $row[1]],
                    [
                        'name' => $row[0],
                        'address' => $row[2],
                        'opening_balance' => $row[3],
                    ],
                ),
            ]);

            $productRows = [
                ['Paracetamol 500mg', 'Paracetamol', 'TAB', 'Analgesics', 18, 28, 40, 80],
                ['Ibuprofen 400mg', 'Ibuprofen', 'TAB', 'Analgesics', 25, 40, 25, 65],
                ['Amoxicillin 250mg', 'Amoxicillin', 'CAP', 'Antibiotics', 45, 70, 30, 55],
                ['Azithromycin 500mg', 'Azithromycin', 'TAB', 'Antibiotics', 95, 140, 20, 34],
                ['Vitamin C 1000mg', 'Ascorbic Acid', 'TAB', 'Vitamins', 30, 50, 35, 90],
                ['Vitamin D3 Drops', 'Cholecalciferol', 'DROP', 'Vitamins', 70, 110, 16, 24],
                ['Metformin 500mg', 'Metformin', 'TAB', 'Diabetes Care', 22, 35, 35, 120],
                ['Gluco Test Strips', 'Glucose Strip', 'BOX', 'Diabetes Care', 210, 280, 10, 22],
                ['Amlodipine 5mg', 'Amlodipine', 'TAB', 'Cardiology', 28, 45, 30, 75],
                ['Losartan 50mg', 'Losartan', 'TAB', 'Cardiology', 35, 55, 24, 52],
                ['Children Cough Syrup', 'Dextromethorphan', 'SYRUP', 'Pediatrics', 55, 90, 18, 9],
                ['ORS Sachet', 'Oral Rehydration Salt', 'SACHET', 'Pediatrics', 8, 15, 100, 180],
            ];

            $products = collect($productRows)->mapWithKeys(function ($row, $index) use ($categories) {
                [$name, $generic, $form, $category, $purchasePrice, $salePrice, $minimumStock] = $row;

                return [
                    $name => Product::query()->updateOrCreate(
                        ['sku' => 'MED-' . str_pad((string) ($index + 1), 4, '0', STR_PAD_LEFT)],
                        [
                            'category_id' => $categories[$category]->id,
                            'name' => $name,
                            'generic_name' => $generic,
                            'barcode' => '899100' . str_pad((string) ($index + 1), 6, '0', STR_PAD_LEFT),
                            'strength' => str_contains($name, 'mg') ? trim(str($name)->afterLast(' ')->toString()) : null,
                            'dosage_form' => $form,
                            'purchase_price' => $purchasePrice,
                            'sale_price' => $salePrice,
                            'minimum_stock' => $minimumStock,
                            'description' => 'Seeded demo product for UI and workflow testing.',
                            'is_active' => true,
                        ],
                    ),
                ];
            });

            foreach ($productRows as $index => $row) {
                [$name,,,, $purchasePrice, $salePrice,, $stock] = $row;
                $product = $products[$name];
                $supplier = $suppliers->values()[$index % $suppliers->count()];
                $expiry = match (true) {
                    $index === 2 => today()->subDays(20),
                    in_array($index, [5, 10], true) => today()->addDays(18 + $index),
                    default => today()->addMonths(8 + ($index % 8)),
                };

                ProductBatch::query()->updateOrCreate(
                    ['product_id' => $product->id, 'batch_number' => 'BATCH-' . str_pad((string) ($index + 1), 3, '0', STR_PAD_LEFT)],
                    [
                        'supplier_id' => $supplier->id,
                        'manufacture_date' => today()->subMonths(8 + ($index % 7)),
                        'expiry_date' => $expiry,
                        'purchase_price' => $purchasePrice,
                        'sale_price' => $salePrice,
                        'quantity' => $stock,
                        'remaining_quantity' => $stock,
                        'notes' => 'Demo batch seeded for inventory tests.',
                    ],
                );
            }

            $expenseCategories = collect(['Rent', 'Utilities', 'Salaries', 'Transport', 'Packaging', 'Marketing'])
                ->mapWithKeys(fn ($name) => [
                    $name => ExpenseCategory::query()->updateOrCreate(
                        ['name' => $name],
                        ['description' => 'Demo ' . strtolower($name) . ' expense category.'],
                    ),
                ]);

            foreach ($expenseCategories->values() as $index => $category) {
                Expense::query()->updateOrCreate(
                    ['title' => $category->name . ' - Demo Expense'],
                    [
                        'expense_category_id' => $category->id,
                        'amount' => [12000, 2600, 38000, 1900, 850, 3200][$index],
                        'expense_date' => now()->subDays($index * 3 + 1),
                        'notes' => 'Seeded demo expense.',
                        'created_by' => $user->id,
                    ],
                );
            }

            $purchaseProducts = $products->values()->chunk(3);
            foreach ($purchaseProducts->take(4) as $purchaseIndex => $chunk) {
                $supplier = $suppliers->values()[$purchaseIndex % $suppliers->count()];
                $invoiceNo = 'PUR-DEMO-' . str_pad((string) ($purchaseIndex + 1), 4, '0', STR_PAD_LEFT);
                $subtotal = $chunk->sum(fn ($product) => (float) $product->purchase_price * (12 + $purchaseIndex * 3));
                $discount = $purchaseIndex * 120;
                $tax = round(($subtotal - $discount) * 0.02, 2);
                $total = $subtotal - $discount + $tax;
                $paid = $purchaseIndex % 3 === 0 ? $total : round($total * (0.45 + $purchaseIndex * 0.12), 2);
                $due = max($total - $paid, 0);

                $purchase = Purchase::query()->updateOrCreate(
                    ['invoice_no' => $invoiceNo],
                    [
                        'supplier_id' => $supplier->id,
                        'purchase_date' => now()->subDays(18 - $purchaseIndex * 3),
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'tax' => $tax,
                        'total' => $total,
                        'paid_amount' => $paid,
                        'due_amount' => $due,
                        'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                        'notes' => 'Seeded demo purchase.',
                        'created_by' => $user->id,
                    ],
                );

                if (! $purchase->items()->exists()) {
                    foreach ($chunk as $product) {
                        $batch = $product->productBatches()->first();
                        $quantity = 12 + $purchaseIndex * 3;
                        $lineTotal = (float) $product->purchase_price * $quantity;

                        PurchaseItem::query()->create([
                            'purchase_id' => $purchase->id,
                            'product_id' => $product->id,
                            'product_batch_id' => $batch?->id,
                            'quantity' => $quantity,
                            'unit_price' => $product->purchase_price,
                            'discount' => 0,
                            'total' => $lineTotal,
                            'batch_number' => $batch?->batch_number,
                            'manufacture_date' => $batch?->manufacture_date,
                            'expiry_date' => $batch?->expiry_date,
                        ]);

                        StockMovement::query()->firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'product_batch_id' => $batch?->id,
                                'type' => 'purchase',
                                'reference_type' => Purchase::class,
                                'reference_id' => $purchase->id,
                            ],
                            [
                                'quantity' => $quantity,
                                'movement_date' => $purchase->purchase_date,
                                'notes' => 'Seeded purchase stock movement.',
                                'created_by' => $user->id,
                            ],
                        );
                    }
                }

                PurchasePayment::query()->updateOrCreate(
                    ['purchase_id' => $purchase->id, 'reference_no' => 'PAY-' . $invoiceNo],
                    [
                        'supplier_id' => $supplier->id,
                        'amount' => $paid,
                        'payment_date' => $purchase->purchase_date->copy()->addHours(2),
                        'payment_method' => ['cash', 'bank', 'card', 'other'][$purchaseIndex % 4],
                        'notes' => 'Seeded purchase payment.',
                        'created_by' => $user->id,
                    ],
                );
            }

            foreach (range(1, 10) as $saleIndex) {
                $customer = $customers->values()[($saleIndex - 1) % $customers->count()];
                $invoiceNo = 'SAL-DEMO-' . str_pad((string) $saleIndex, 4, '0', STR_PAD_LEFT);
                $selectedProducts = $products->values()->slice(($saleIndex - 1) % 6, 3);
                $subtotal = $selectedProducts->sum(fn ($product) => (float) $product->sale_price * (($saleIndex % 3) + 1));
                $discount = $saleIndex % 2 === 0 ? 75 : 0;
                $tax = round(($subtotal - $discount) * 0.01, 2);
                $total = $subtotal - $discount + $tax;
                $paid = match ($saleIndex % 4) {
                    0 => 0,
                    1 => $total,
                    default => round($total * 0.65, 2),
                };
                $due = max($total - $paid, 0);

                $sale = Sale::query()->updateOrCreate(
                    ['invoice_no' => $invoiceNo],
                    [
                        'customer_id' => $customer->id,
                        'sale_date' => now()->subDays(12 - $saleIndex),
                        'subtotal' => $subtotal,
                        'discount' => $discount,
                        'tax' => $tax,
                        'total' => $total,
                        'paid_amount' => $paid,
                        'due_amount' => $due,
                        'payment_status' => $due <= 0 ? 'paid' : ($paid > 0 ? 'partial' : 'due'),
                        'notes' => 'Seeded demo sale.',
                        'created_by' => $user->id,
                    ],
                );

                if (! $sale->items()->exists()) {
                    foreach ($selectedProducts as $product) {
                        $batch = $product->productBatches()->orderByDesc('remaining_quantity')->first();
                        $quantity = ($saleIndex % 3) + 1;
                        $lineTotal = (float) $product->sale_price * $quantity;

                        SaleItem::query()->create([
                            'sale_id' => $sale->id,
                            'product_id' => $product->id,
                            'product_batch_id' => $batch?->id,
                            'quantity' => $quantity,
                            'unit_price' => $product->sale_price,
                            'discount' => 0,
                            'total' => $lineTotal,
                        ]);

                        if ($batch) {
                            $batch->decrement('remaining_quantity', min($quantity, $batch->remaining_quantity));
                        }

                        StockMovement::query()->firstOrCreate(
                            [
                                'product_id' => $product->id,
                                'product_batch_id' => $batch?->id,
                                'type' => 'sale',
                                'reference_type' => Sale::class,
                                'reference_id' => $sale->id,
                            ],
                            [
                                'quantity' => -$quantity,
                                'movement_date' => $sale->sale_date,
                                'notes' => 'Seeded sale stock movement.',
                                'created_by' => $user->id,
                            ],
                        );
                    }
                }

                if ($paid > 0) {
                    SalePayment::query()->updateOrCreate(
                        ['sale_id' => $sale->id, 'reference_no' => 'PAY-' . $invoiceNo],
                        [
                            'customer_id' => $customer->id,
                            'amount' => $paid,
                            'payment_date' => $sale->sale_date->copy()->addMinutes(15),
                            'payment_method' => ['cash', 'bank', 'card', 'other'][$saleIndex % 4],
                            'notes' => 'Seeded sale payment.',
                            'created_by' => $user->id,
                        ],
                    );
                }
            }

            $sale = Sale::query()->where('invoice_no', 'SAL-DEMO-0002')->first();
            $saleItem = $sale?->items()->first();
            if ($sale && $saleItem) {
                $return = ReturnModel::query()->updateOrCreate(
                    ['return_no' => 'RET-SALE-DEMO-0001'],
                    [
                        'type' => 'sale_return',
                        'sale_id' => $sale->id,
                        'customer_id' => $sale->customer_id,
                        'return_date' => now()->subDays(2),
                        'total_amount' => $saleItem->unit_price,
                        'notes' => 'Seeded sale return.',
                        'created_by' => $user->id,
                    ],
                );

                ReturnItem::query()->firstOrCreate(
                    ['return_id' => $return->id, 'product_id' => $saleItem->product_id],
                    [
                        'product_batch_id' => $saleItem->product_batch_id,
                        'quantity' => 1,
                        'unit_price' => $saleItem->unit_price,
                        'total' => $saleItem->unit_price,
                        'reason' => 'Customer returned sealed item.',
                    ],
                );
            }

            $purchase = Purchase::query()->where('invoice_no', 'PUR-DEMO-0002')->first();
            $purchaseItem = $purchase?->items()->first();
            if ($purchase && $purchaseItem) {
                $return = ReturnModel::query()->updateOrCreate(
                    ['return_no' => 'RET-PUR-DEMO-0001'],
                    [
                        'type' => 'purchase_return',
                        'purchase_id' => $purchase->id,
                        'supplier_id' => $purchase->supplier_id,
                        'return_date' => now()->subDay(),
                        'total_amount' => $purchaseItem->unit_price,
                        'notes' => 'Seeded purchase return.',
                        'created_by' => $user->id,
                    ],
                );

                ReturnItem::query()->firstOrCreate(
                    ['return_id' => $return->id, 'product_id' => $purchaseItem->product_id],
                    [
                        'product_batch_id' => $purchaseItem->product_batch_id,
                        'quantity' => 1,
                        'unit_price' => $purchaseItem->unit_price,
                        'total' => $purchaseItem->unit_price,
                        'reason' => 'Supplier batch correction.',
                    ],
                );
            }

            $batch = ProductBatch::query()->where('remaining_quantity', '>', 8)->first();
            if ($batch) {
                $adjustment = StockAdjustment::query()->updateOrCreate(
                    ['adjustment_no' => 'ADJ-DEMO-0001'],
                    [
                        'adjustment_date' => now()->subDays(3),
                        'reason' => 'Cycle count correction',
                        'notes' => 'Seeded stock adjustment.',
                        'created_by' => $user->id,
                    ],
                );

                StockAdjustmentItem::query()->firstOrCreate(
                    ['stock_adjustment_id' => $adjustment->id, 'product_batch_id' => $batch->id],
                    [
                        'product_id' => $batch->product_id,
                        'system_quantity' => $batch->remaining_quantity,
                        'actual_quantity' => $batch->remaining_quantity - 2,
                        'difference' => -2,
                        'type' => 'decrease',
                    ],
                );

                StockMovement::query()->firstOrCreate(
                    [
                        'product_id' => $batch->product_id,
                        'product_batch_id' => $batch->id,
                        'type' => 'adjustment_sub',
                        'reference_type' => StockAdjustment::class,
                        'reference_id' => $adjustment->id,
                    ],
                    [
                        'quantity' => -2,
                        'movement_date' => $adjustment->adjustment_date,
                        'notes' => 'Seeded adjustment movement.',
                        'created_by' => $user->id,
                    ],
                );
            }

            foreach ([
                ['created', 'products', $products->first()?->id, 'Demo products seeded.'],
                ['created', 'purchases', Purchase::query()->first()?->id, 'Demo purchases seeded.'],
                ['created', 'sales', Sale::query()->first()?->id, 'Demo sales seeded.'],
                ['updated', 'settings', 1, 'Demo pharmacy settings updated.'],
            ] as $log) {
                ActivityLog::query()->firstOrCreate(
                    ['action' => $log[0], 'module' => $log[1], 'reference_id' => $log[2]],
                    [
                        'user_id' => $user->id,
                        'description' => $log[3],
                        'ip_address' => '127.0.0.1',
                    ],
                );
            }
        });
    }
}
