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


    public function getRevenue(): int
    {
        return (int) $this->orders->sumRevenue();
    }


    public function getOrderCount(): int
    {
        return (int) $this->orders->countOrder();
    }


    public function getBestSellers(int $limit = 5): Collection
    {
        return $this->products->getBestSellers($limit);
    }

    public function getPendingOrderCount(): int
    {
        return (int) ($this->orders->countGroupByStatus()['pending'] ?? 0);
    }

    public function getOrderCountByStatus(): array
    {
        return $this->orders->countGroupByStatus();
    }

    public function getLowStockProducts(int $threshold = 5)
    {
        if (method_exists($this->products, 'getLowStockProducts')) {
            return call_user_func([$this->products, 'getLowStockProducts'], $threshold);
        }

        return collect();
    }

    public function getRecentOrders(int $limit = 10)
    {
        return $this->orders->getRecent($limit);
    }
}