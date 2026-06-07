<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Model;

class Page extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'body' => 'array',
            'meta_title' => 'array',
            'meta_description' => 'array',
            'is_active' => 'boolean',
        ];
    }
}
