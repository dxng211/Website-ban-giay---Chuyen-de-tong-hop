<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

/**
 * Phụ thuộc: OrderRepository, ProductRepository, CartRepository, PaymentService.
 */
class OrderService
{
    public function __construct(
        private readonly OrderRepository $orders,
        private readonly ProductRepository $products,
        private readonly CartRepository $carts,
        private readonly PaymentService $paymentService,
    ) {}

    /**
     * Đặt hàng (UC06, Seq04). Trả về đơn và URL mã QR (null nếu COD hoặc sinh QR lỗi).
     */
    public function checkout(User $user, array $data): array
    {
        // TODO: DB::transaction: sinh order_code; saveOrder (order_status = pending, payment_status = unpaid); saveOrderItems (snapshot)
        // TODO: deductStock từng biến thể; hết hàng -> InsufficientException (rollback); clearCart; commit
        // TODO: chuyển khoản: gọi $this->paymentService->getQr($order) SAU khi commit; PaymentException -> qrCodeUrl = null, đơn vẫn giữ pending
        // TODO: return ['order' => $order, 'qrCodeUrl' => $qrUrl]
    }

    /**
     * Danh sách đơn: Customer chỉ thấy đơn của mình, Admin thấy tất cả và lọc theo trạng thái.
     */
    public function list(User $actor, array $filters = []): LengthAwarePaginator
    {
        // TODO: quyết định phạm vi theo $actor->isAdmin(); $this->orders->list(...)
    }

    /**
     * Chi tiết đơn. Customer chỉ xem đơn của mình.
     */
    public function getById(int $id, User $actor): Order
    {
        // TODO: $this->orders->findById($id); kiểm tra quyền
    }

    /**
     * Admin đổi trạng thái đơn (UC10).
     */
    public function updateStatus(int $id, OrderStatus $status): Order
    {
        // TODO: kiểm tra $order->order_status->canTransitionTo($status); không hợp lệ -> OrderCantBeCancelledException hoặc lỗi nghiệp vụ
        // TODO: chuyển sang cancelled -> hoàn kho như cancel()
    }

    /**
     * Hủy đơn (Seq05). Customer: chỉ khi pending. Admin: pending hoặc shipping. Hoàn kho từng biến thể.
     */
    public function cancel(int $id, User $actor): Order
    {
        // TODO: DB::transaction: updateStatus(cancelled); restoreStock từng dòng; sai điều kiện -> OrderCantBeCancelledException
    }
}
