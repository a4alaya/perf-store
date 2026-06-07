<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Brand extends Model
{
    use HasFactory;
    use HasLocalizedFields;
    use SoftDeletes;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'name' => 'array',
            'description' => 'array',
            'meta_title' => 'array',
            'meta_description' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }
}
