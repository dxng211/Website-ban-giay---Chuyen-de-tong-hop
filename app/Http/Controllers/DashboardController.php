<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\StatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    //
    public function __construct(
        private readonly StatisticsService $statisticsService,
    ) {}

    /**
     * GET /admin/dashboard (thêm mới: hàm chưa có trong Class Diagram)
     */
    public function index(): View
    {
        // TODO: gọi getRevenue, getOrderCount, getBestSellers của StatisticsService rồi trả view thống kê
    }

    /**
     * JSON endpoint cho từng ô thống kê (dùng khi giao diện nạp bằng AJAX)
     */
    public function getRevenue(): JsonResponse
    {
        // TODO: trả $this->statisticsService->getRevenue()
    }

    /**
     * JSON endpoint
     */
    public function getOrderCount(): JsonResponse
    {
        // TODO: trả $this->statisticsService->getOrderCount()
    }

    /**
     * JSON endpoint
     */
    public function getBestSellers(): JsonResponse
    {
        // TODO: trả $this->statisticsService->getBestSellers()
    }
}
