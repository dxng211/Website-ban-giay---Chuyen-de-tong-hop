<?php

namespace App\Models;

use App\Enums\CategoryGroupType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    //
    public $timestamps = false; // Không có created_at và updated_at

    protected $fillable = [
        'name',
        'group_type',
        'slug',
        'size_chart_key',
    ];

    protected function casts(): array
    {
        return [
            'group_type' => CategoryGroupType::class,
        ];
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    /** Bảng size dùng cho danh mục này (NULL nếu không cần) */
    public function sizeChartGroup(): BelongsTo
    {
        return $this->belongsTo(SizeChartGroup::class, 'size_chart_key', 'chart_key');
    }

    public function scopeOfGroup(Builder $query, CategoryGroupType $group): Builder
    {
        return $query->where('group_type', $group->value);
    }
}
