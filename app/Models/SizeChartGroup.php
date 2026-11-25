<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SizeChartGroup extends Model
{
    public $timestamps = false;          // Không có created_at và updated_at
    protected $primaryKey = 'chart_key'; // Khóa chính là chuỗi (VD: shoe_vn)
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'chart_key',
        'chart_name',
        'size_column_label',
        'reference_column_label',
        'unit',
    ];

    /** Các dòng của bảng size, xếp theo sort_order */
    public function sizeCharts(): HasMany
    {
        return $this->hasMany(SizeChart::class, 'chart_key', 'chart_key')->orderBy('sort_order');
    }

    /** Các danh mục dùng bảng size này */
    public function categories(): HasMany
    {
        return $this->hasMany(Category::class, 'size_chart_key', 'chart_key');
    }
}
