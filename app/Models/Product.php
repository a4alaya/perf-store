<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory;
    use HasLocalizedFields;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'short_description' => 'array',
            'description' => 'array',
            'top_notes' => 'array',
            'middle_notes' => 'array',
            'base_notes' => 'array',
            'meta_title' => 'array',
            'meta_description' => 'array',
            'price' => 'decimal:2',
            'sale_price' => 'decimal:2',
            'average_rating' => 'decimal:2',
            'is_featured' => 'boolean',
            'is_best_seller' => 'boolean',
            'is_new_arrival' => 'boolean',
            'is_uae_exclusive' => 'boolean',
            'is_active' => 'boolean',
            'vat_taxable' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true)->whereNotNull('published_at');
    }

    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    public function relatedProducts(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'product_related', 'product_id', 'related_product_id');
    }

    public function currentPrice(): float
    {
        return (float) ($this->sale_price ?: $this->price);
    }
}
