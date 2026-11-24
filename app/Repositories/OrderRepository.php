<?php

namespace App\Repositories;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Models\Order;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Truy vấn nhóm bảng orders, order_items. Cập nhật qua Model (không dùng DB::table) để Observer chạy.
 */
class OrderRepository
{
    /**
     * Lưu đơn hàng (Seq04).
     */
    public function saveOrder(array $data): Order
    {
        // TODO: Order::create($data)
    }

    /**
     * Lưu các dòng đơn kèm snapshot tên, size, giá (Seq04).
     */
    public function saveOrderItems(int $orderId, array $items): void
    {
        // TODO: viết truy vấn
    }

    /**
     * Đơn hàng kèm dòng đơn (Seq05).
     */
    public function findById(int $id): ?Order
    {
        // TODO: Order::with('items')->find($id)
    }

    /**
     * Đổi order_status.
     */
    public function updateStatus(int $id, OrderStatus $status): Order
    {
        // TODO: viết truy vấn
    }

    /**
     * Đổi payment_status (xác nhận đã nhận tiền).
     */
    public function updatePaymentStatus(int $id, PaymentStatus $status): Order
    {
        // TODO: viết truy vấn
    }

    /**
     * Khách đã mua sản phẩm này (đơn completed) chưa? (Seq07)
     */
    public function hasPurchased(int $userId, int $productId): bool
    {
        // TODO: viết truy vấn
    }

    /**
     * Danh sách đơn: theo user (Customer) hoặc tất cả (Admin), có lọc trạng thái, phân trang.
     */
    public function list(?int $userId, array $filters = []): LengthAwarePaginator
    {
        // TODO: viết truy vấn
    }

    /**
     * Tổng doanh thu (chỉ đơn completed, cần thống nhất trong nhóm).
     */
    public function sumRevenue(): int
    {
        // TODO: viết truy vấn
    }

    /**
     * Tổng số đơn hàng.
     */
    public function countOrder(): int
    {
        // TODO: viết truy vấn
    }
}
