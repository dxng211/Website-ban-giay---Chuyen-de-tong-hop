<?php

namespace App\Services;

use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Support\Collection;

/**
 * Phụ thuộc: OrderRepository, ProductRepository. Doanh thu chỉ tính đơn completed (cần thống nhất trong nhóm).
 */
class StatisticsService
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly ProductRepository $products,
    ) {}

    /**
     * Tổng doanh thu.
     */
    public function getRevenue(): int
    {
        // TODO: return $this->orders->sumRevenue()
    }

    /**
     * Tổng số đơn hàng.
     */
    public function getOrderCount(): int
    {
        // TODO: return $this->orders->countOrder()
    }

    /**
     * Danh sách sản phẩm bán chạy nhất.
     */
    public function getBestSellers(int $limit = 5): Collection
    {
        // TODO: return $this->products->getBestSellers($limit)
    }
}
