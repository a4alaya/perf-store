<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreSection extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'eyebrow' => 'array',
            'body' => 'array',
            'cta_label' => 'array',
            'secondary_cta_label' => 'array',
            'settings' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function items(): HasMany
    {
        return $this->hasMany(StoreSectionItem::class)->orderBy('sort_order');
    }

    public function activeItems(): HasMany
    {
        return $this->items()->where('is_active', true);
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order')->orderBy('id');
    }
}
