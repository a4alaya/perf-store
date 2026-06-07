<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Coupon extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'value' => 'decimal:2',
            'min_order_amount' => 'decimal:2',
            'is_active' => 'boolean',
            'starts_at' => 'datetime',
            'expires_at' => 'datetime',
        ];
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function isUsableFor(float $subtotal): bool
    {
        return $this->is_active
            && (!$this->starts_at || $this->starts_at->isPast())
            && (!$this->expires_at || $this->expires_at->isFuture())
            && (!$this->min_order_amount || $subtotal >= (float) $this->min_order_amount)
            && (!$this->usage_limit || $this->usage_count < $this->usage_limit);
    }
}
