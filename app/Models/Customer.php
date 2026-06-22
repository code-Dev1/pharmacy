<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'opening_balance',
        'notes',
    ];

    protected function casts(): array
    {
        return ['opening_balance' => 'decimal:2'];
    }

    public function sales()
    {
        return $this->hasMany(Sale::class);
    }

    public function salePayments()
    {
        return $this->hasMany(SalePayment::class);
    }
}
