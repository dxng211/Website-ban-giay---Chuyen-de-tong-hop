<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SizeChart extends Model
{
    public $timestamps = false; // Không có created_at và updated_at

    protected $fillable = [
        'chart_key',
        'size_label',
        'reference_value',
        'reference_min',
        'reference_max',
        'sort_order',
    ];

    protected function casts(): array
    {
        return [
            'reference_min' => 'decimal:2',
            'reference_max' => 'decimal:2',
            'sort_order' => 'integer',
        ];
    }

    public function group(): BelongsTo
    {
        return $this->belongsTo(SizeChartGroup::class, 'chart_key', 'chart_key');
    }

    /**
     * Tra size theo số đo (dùng cho Chatbot):
     * SizeChart::where('chart_key', 'shoe_vn')->coveringValue(24.8)->first();
     */
    public function scopeCoveringValue(Builder $query, float $value): Builder
    {
        return $query->where('reference_min', '<=', $value)
                     ->where('reference_max', '>=', $value);
    }

    /** Giá trị tham chiếu để hiển thị: lấy reference_value, nếu NULL thì ghép từ min - max + đơn vị */
    protected function displayValue(): Attribute
    {
        return Attribute::get(function () {
            if ($this->reference_value !== null) {
                return $this->reference_value;
            }
            $unit = $this->group?->unit;
            $text = rtrim(rtrim($this->reference_min, '0'), '.') . ' - ' . rtrim(rtrim($this->reference_max, '0'), '.');
            return $unit ? "{$text} {$unit}" : $text;
        });
    }
}
