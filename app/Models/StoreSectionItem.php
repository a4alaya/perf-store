<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreSectionItem extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'is_active' => 'boolean',
        ];
    }

    public function storeSection(): BelongsTo
    {
        return $this->belongsTo(StoreSection::class);
    }
}
