<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    public $timestamps = false; // Không có created_at và updated_at

    // Các cột *_snapshot lưu lại tên, size, giá tại thời điểm mua
    protected $fillable = [
        'order_id',
        'product_variant_id',
        'product_name_snapshot',
        'size_snapshot',
        'unit_price_snapshot',
        'quantity',
        'subtotal',
    ];

    protected function casts(): array
    {
        return [
            'unit_price_snapshot' => 'integer',
            'quantity' => 'integer',
            'subtotal' => 'integer',
        ];
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }
}
