<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShippingZone extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'delivery_fee' => 'decimal:2',
            'free_shipping_threshold' => 'decimal:2',
            'cod_fee' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'same_day_available' => 'boolean',
            'next_day_available' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    public function deliverySlots(): HasMany
    {
        return $this->hasMany(DeliverySlot::class);
    }
}
