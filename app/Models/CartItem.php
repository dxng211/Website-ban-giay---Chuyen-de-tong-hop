<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CartItem extends Model
{
    //
    const UPDATED_AT = null; // Không có updated_at
    
    protected $fillable = [
        'cart_id',
        'product_variant_id',
        'quantity',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
        ];
    }

    public function cart(): BelongsTo
    {
        return $this->belongsTo(Cart::class);
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /** Thành tiền của dòng giỏ. Nhớ eager load: with('variant.product') để tránh truy vấn lặp */
    protected function subtotal(): Attribute
    {
        return Attribute::get(fn () => $this->variant->product->final_price * $this->quantity);
    }
}
