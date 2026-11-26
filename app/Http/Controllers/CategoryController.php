<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\CategoryService;
use Illuminate\Http\JsonResponse;

class CategoryController extends Controller
{
    //
    public function __construct(
        private readonly CategoryService $categoryService,
    ) {}

    /**
     * GET /categories (AJAX, JSON)
     */
    public function list(): JsonResponse
    {
        // TODO: trả danh sách danh mục từ $this->categoryService->list() (menu lọc phụ kiện)
    }

    /**
     * GET /categories/{key}/size-chart (AJAX, JSON) - pop-up bảng chọn size
     */
    public function getSizeChart(string $key): JsonResponse
    {
        // TODO: trả bảng size từ $this->categoryService->getSizeChart($key); không có -> 404
        $group = $this->categoryService->getSizeChart($key);

        if (! $group) {
            return response()->json(['message' => 'Không tìm thấy bảng hướng dẫn chọn size.'], 404);
        }

        return response()->json([
            'html' => view('partials._size-chart-table', compact('group'))->render(),
        ]);
    }
}
