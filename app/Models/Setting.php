<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = [
        'pharmacy_name',
        'logo',
        'phone',
        'email',
        'address',
        'currency',
        'invoice_footer',
        'low_stock_threshold',
        'expiry_alert_days',
        'timezone',
        'date_format',
        'default_language',
        'default_theme',
    ];
}
