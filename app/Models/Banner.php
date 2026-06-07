<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'subtitle' => 'array',
            'starts_at' => 'datetime',
            'ends_at' => 'datetime',
            'is_active' => 'boolean',
        ];
    }
}
