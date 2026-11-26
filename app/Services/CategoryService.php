<?php

namespace App\Services;

use App\Models\Category;
use App\Models\SizeChartGroup;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Cache;

/**
 * Phụ thuộc: Category, SizeChartGroup, SizeChart (Model, truy cập trực tiếp).
 */
class CategoryService
{
    /**
     * Danh sách danh mục.
     */
    public function list(): Collection
    {
        return Category::all();
    }

    /**
     * Bảng chọn size (pop-up) của một nhóm bảng size.
     */
    public function getSizeChart(string $chartKey): ?SizeChartGroup
    {
        return Cache::remember("size_chart_{$chartKey}", now()->addMinutes(30), function () use ($chartKey) {
            return SizeChartGroup::with('sizeCharts')->find($chartKey)
                ?? SizeChartGroup::with('sizeCharts')->where('chart_key', $chartKey)->first();
        });
    }
}
