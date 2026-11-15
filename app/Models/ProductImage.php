<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductImage extends Model
{
    public $timestamps = false; // Không có created_at và updated_at

    protected $fillable = [
        'product_id',
        'image_url',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'sort_order' => 'integer',
        ];
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    /** Đường dẫn dùng trong Blade: giữ nguyên nếu là URL đầy đủ, nếu không thì lấy từ storage/public */
    protected function url(): Attribute
    {
        return Attribute::get(fn () => str_starts_with($this->image_url, 'http')
            ? $this->image_url
            : asset('storage/' . ltrim($this->image_url, '/')));
    }
}
