<?php

namespace App\Services;

use App\Models\ShippingZone;
use Illuminate\Support\Carbon;

class ShippingService
{
    public function zoneFor(?string $emirate): ?ShippingZone
    {
        if (! $emirate) {
            return null;
        }

        return ShippingZone::query()
            ->where('emirate', $emirate)
            ->where('is_active', true)
            ->first();
    }

    public function feeFor(?string $emirate, float $subtotal): float
    {
        $zone = $this->zoneFor($emirate);

        if (! $zone) {
            return 0;
        }

        $threshold = (float) ($zone->free_shipping_threshold ?? config('store.free_shipping_threshold', 500));

        return $subtotal >= $threshold ? 0 : (float) $zone->delivery_fee;
    }

    public function codFeeFor(?string $emirate, string $paymentMethod): float
    {
        if ($paymentMethod !== 'cod') {
            return 0;
        }

        return (float) ($this->zoneFor($emirate)?->cod_fee ?? 0);
    }

    public function estimatedDeliveryDate(?string $emirate, bool $sameDay = false): Carbon
    {
        $zone = $this->zoneFor($emirate);

        if ($sameDay && $zone?->same_day_available) {
            return now();
        }

        return now()->addDays((int) ($zone?->estimated_days_min ?? 2));
    }

    public function freeShippingProgress(float $subtotal, ?string $emirate = null): array
    {
        $threshold = (float) (($this->zoneFor($emirate)?->free_shipping_threshold) ?? config('store.free_shipping_threshold', 500));
        $remaining = max(0, $threshold - $subtotal);

        return [
            'threshold' => $threshold,
            'remaining' => $remaining,
            'qualified' => $remaining <= 0,
            'percent' => $threshold > 0 ? min(100, round(($subtotal / $threshold) * 100)) : 100,
        ];
    }
}
