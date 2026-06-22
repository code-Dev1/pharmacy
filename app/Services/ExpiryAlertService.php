<?php

namespace App\Services;

use App\Models\ProductBatch;
use App\Models\Setting;

class ExpiryAlertService
{
    public function thresholdDays(): int
    {
        return (int) (Setting::query()->first()?->expiry_alert_days ?? 30);
    }

    public function expired()
    {
        return ProductBatch::with(['product', 'supplier'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', today())
            ->where('remaining_quantity', '>', 0);
    }

    public function nearExpiry()
    {
        return ProductBatch::with(['product', 'supplier'])
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', today())
            ->whereDate('expiry_date', '<=', now()->addDays($this->thresholdDays()))
            ->where('remaining_quantity', '>', 0);
    }

    public function status(ProductBatch $batch): string
    {
        if (! $batch->expiry_date) {
            return 'valid';
        }

        if ($batch->expiry_date->isPast() && ! $batch->expiry_date->isToday()) {
            return 'expired';
        }

        return $batch->expiry_date->lte(now()->addDays($this->thresholdDays())) ? 'near_expiry' : 'valid';
    }
}
