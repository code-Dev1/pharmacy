<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_id',
        'product_id',
        'product_batch_id',
        'quantity',
        'unit_price',
        'discount',
        'total',
        'batch_number',
        'manufacture_date',
        'expiry_date',
    ];

    protected function casts(): array
    {
        return ['manufacture_date' => 'date', 'expiry_date' => 'date'];
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function productBatch()
    {
        return $this->belongsTo(ProductBatch::class);
    }
}
