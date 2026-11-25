<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChatbotLog extends Model
{
    //
    const UPDATED_AT = null; // Không có updated_at

    protected $fillable = [
        'user_id',
        'session_token',
        'need_type',
        'foot_length_cm',
        'price_min',
        'price_max',
        'suggested_product_ids',
        'ai_reply',
    ];

    protected function casts(): array
    {
        return [
            'foot_length_cm' => 'decimal:1',
            'price_min' => 'integer',
            'price_max' => 'integer',
            'suggested_product_ids' => 'array', // cột JSON <-> mảng PHP
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
