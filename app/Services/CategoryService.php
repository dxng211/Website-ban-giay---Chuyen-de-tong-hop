<?php

namespace App\Services;

use App\Models\SizeChartGroup;
use Illuminate\Support\Collection;

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
        // TODO: Category::all() hoặc lọc theo group_type
    }

    /**
     * Bảng chọn size (pop-up) của một nhóm bảng size.
     */
    public function getSizeChart(string $chartKey): ?SizeChartGroup
    {
        // TODO: SizeChartGroup::with('sizeCharts')->find($chartKey); nên cache vì ít thay đổi
    }
}
