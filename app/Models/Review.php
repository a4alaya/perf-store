<?php

namespace App\Models;

use App\Models\Concerns\HasLocalizedFields;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Review extends Model
{
    use HasLocalizedFields;

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'title' => 'array',
            'body' => 'array',
            'admin_response' => 'array',
            'images' => 'array',
            'is_verified_purchase' => 'boolean',
            'reported_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
