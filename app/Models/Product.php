<?php

namespace App\Models;

use App\Enums\CategoryGroupType;
use App\Enums\Gender;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use SoftDeletes; // Xóa mềm (BR2-UC09)

    // sold_count, rating_avg, review_count KHÔNG cho gán hàng loạt: Observer cập nhật
    protected $fillable = [
        'category_id',
        'sku',
        'name',
        'description',
        'gender',
        'base_price',
        'sale_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'gender' => Gender::class,
            'base_price' => 'integer',
            'sale_price' => 'integer',
            'sold_count' => 'integer',
            'rating_avg' => 'decimal:1',
            'review_count' => 'integer',
            'is_active' => 'boolean',
        ];
    }

    /* ---------------- Quan hệ ---------------- */

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /** Ảnh xếp theo sort_order (ảnh đầu tiên là ảnh khung lớn) */
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

    /** Chỉ các đánh giá đã duyệt (hiển thị công khai) */
    public function approvedReviews(): HasMany
    {
        return $this->hasMany(Review::class)->where('status', 'approved');
    }

    /* ---------------- Thuộc tính tính toán ---------------- */

    /** Giá bán thực tế: giá giảm nếu có, không thì giá gốc */
    protected function finalPrice(): Attribute
    {
        return Attribute::get(fn () => (int) ($this->sale_price ?? $this->base_price));
    }

    /** % giảm giá để hiển thị tag (0 nếu không giảm) */
    protected function discountPercent(): Attribute
    {
        return Attribute::get(function () {
            if ($this->sale_price === null || $this->base_price <= 0 || $this->sale_price >= $this->base_price) {
                return 0;
            }
            return (int) round((1 - $this->sale_price / $this->base_price) * 100);
        });
    }

    /** Tổng tồn kho của mọi size */
    protected function totalStock(): Attribute
    {
        return Attribute::get(fn () => $this->relationLoaded('variants')
            ? (int) $this->variants->sum('stock_quantity')
            : (int) $this->variants()->sum('stock_quantity'));
    }

    /* ---------------- Scope dùng cho Repository ---------------- */

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    public function scopeOnSale(Builder $query): Builder
    {
        return $query->whereNotNull('sale_price');
    }

    /** Lọc theo nhóm: giày (shoe) hoặc phụ kiện (accessory) */
    public function scopeInGroup(Builder $query, CategoryGroupType $group): Builder
    {
        return $query->whereHas('category', fn (Builder $c) => $c->where('group_type', $group->value));
    }

    /** Chỉ lấy sản phẩm còn hàng ở size được chọn */
    public function scopeHasSizeInStock(Builder $query, string $size): Builder
    {
        return $query->whereHas('variants', fn (Builder $v) => $v->where('size', $size)->where('stock_quantity', '>', 0));
    }
}
