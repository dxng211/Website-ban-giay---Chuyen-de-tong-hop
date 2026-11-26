<?php

namespace App\Http\Controllers;


use App\Services\StatisticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{

    public function __construct(
        private readonly StatisticsService $statisticsService,
    ) {}

    
    public function index(): View
    {
        $revenue = $this->statisticsService->getRevenue();
        $orderCount = $this->statisticsService->getOrderCount();
        $pendingOrdersCount = $this->statisticsService->getPendingOrderCount();
        $ordersByStatus = $this->statisticsService->getOrderCountByStatus();
        $lowStockProducts = $this->statisticsService->getLowStockProducts();
        $bestSellers = $this->statisticsService->getBestSellers();
        $recentOrders = $this->statisticsService->getRecentOrders();

        return view('admin.dashboard.index', compact(
            'revenue',
            'orderCount',
            'pendingOrdersCount',
            'ordersByStatus',
            'lowStockProducts',
            'bestSellers',
            'recentOrders'
        ));
    }

    public function getRevenue(): JsonResponse
    {
        return response()->json(['data' => $this->statisticsService->getRevenue()]);
    }


    public function getOrderCount(): JsonResponse
    {
        return response()->json(['data' => $this->statisticsService->getOrderCount()]);
    }


    public function getBestSellers(): JsonResponse
    {
        return response()->json(['data' => $this->statisticsService->getBestSellers()]);
    }
}
