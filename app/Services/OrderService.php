<?php

namespace App\Services;

use App\Enums\OrderStatus;
use App\Enums\PaymentStatus;
use App\Exceptions\InsufficientException;
use App\Exceptions\OrderCantBeCancelledException;
use App\Exceptions\PaymentException;
use App\Models\Order;
use App\Models\User;
use App\Repositories\CartRepository;
use App\Repositories\OrderRepository;
use App\Repositories\ProductRepository;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
        $paymentMethod = (string) ($data['payment_method'] ?? 'cod');

        $order = DB::transaction(function () use ($user, $data, $paymentMethod) {
            $order = $this->orders->saveOrder([
                'user_id' => $user->id,
                'order_code' => 'ORD-' . strtoupper(Str::random(8)),
                'customer_name' => $data['customer_name'] ?? $user->name,
                'customer_phone' => $data['customer_phone'] ?? $user->phone,
                'shipping_address' => $data['shipping_address'] ?? $user->address,
                'note' => $data['note'] ?? null,
                'payment_method' => $paymentMethod,
                'order_status' => OrderStatus::Pending,
                'payment_status' => PaymentStatus::Unpaid,
            ]);

            $items = $data['items'] ?? [];
            $this->orders->saveOrderItems((int) $order->id, $items);

            foreach ($items as $item) {
                $variantId = (int) ($item['product_variant_id'] ?? $item['variant_id'] ?? 0);
                $qty = (int) ($item['quantity'] ?? 0);
                if ($this->products->deductStock($variantId, $qty) === false) {
                    throw new InsufficientException("Sản phẩm biến thể #{$variantId} không đủ tồn kho.");
                }
            }

            $this->carts->clearCart($user->id, $data['session_token'] ?? null);

            return $order;
        });

        $qrUrl = null;
        if (in_array(strtolower($paymentMethod), ['bank', 'banking', 'transfer', 'vietqr', 'qr'], true)) {
            try {
                $qrUrl = $this->paymentService->getQr($order);
            } catch (PaymentException) {
                $qrUrl = null;
            }
        }

        return [
            'order' => $order,
            'qrCodeUrl' => $qrUrl,
        ];
    }

    /**
     * Danh sách đơn: Customer chỉ thấy đơn của mình, Admin thấy tất cả và lọc theo trạng thái.
     */
    public function list(User $actor, array $filters = []): LengthAwarePaginator
    {
        $isAdmin = method_exists($actor, 'isAdmin') ? $actor->isAdmin() : (($actor->role ?? null) === 'admin');
        if (!$isAdmin) {
            $filters['user_id'] = $actor->id;
        }

        return $this->orders->list(isset($filters['user_id']) ? (int) $filters['user_id'] : null);
    }

    /**
     * Chi tiết đơn. Customer chỉ xem đơn của mình.
     */
    public function getById(int $id, User $actor): Order
    {
        $order = $this->orders->findById($id);
        $isAdmin = method_exists($actor, 'isAdmin') ? $actor->isAdmin() : (($actor->role ?? null) === 'admin');

        if (!$isAdmin && (int) $order->user_id !== (int) $actor->id) {
            throw new AuthorizationException('Bạn không có quyền xem đơn hàng này.');
        }

        return $order;
    }

    /**
     * Admin đổi trạng thái đơn (UC10).
     */
    public function updateStatus(int $id, OrderStatus $status): Order
    {
        return DB::transaction(function () use ($id, $status) {
            $order = $this->orders->findById($id);
            $current = $order->order_status;

            if ($current instanceof OrderStatus && method_exists($current, 'canTransitionTo')) {
                if (!$current->canTransitionTo($status)) {
                    throw new OrderCantBeCancelledException('Không thể chuyển trạng thái đơn hàng này.');
                }
            }

            $updated = $this->orders->updateStatus($id, $status);

            if ($status === OrderStatus::Cancelled && $current !== OrderStatus::Cancelled) {
                foreach ($order->items ?? [] as $item) {
                    $variantId = (int) ($item->product_variant_id ?? $item->variant_id ?? 0);
                    $qty = (int) ($item->quantity ?? 0);
                    $this->products->restoreStock($variantId, $qty);
                }
            }

            return $updated;
        });
    }

    /**
     * Hủy đơn (Seq05). Customer: chỉ khi pending. Admin: pending hoặc shipping. Hoàn kho từng biến thể.
     */
    public function cancel(int $id, User $actor): Order
    {
        return DB::transaction(function () use ($id, $actor) {
            $order = $this->getById($id, $actor);
            $isAdmin = method_exists($actor, 'isAdmin') ? $actor->isAdmin() : (($actor->role ?? null) === 'admin');
            $current = $order->order_status;

            $allowed = $isAdmin
                ? [OrderStatus::Pending, OrderStatus::Shipping]
                : [OrderStatus::Pending];

            if (!in_array($current, $allowed, true)) {
                throw new OrderCantBeCancelledException('Đơn hàng hiện tại không thể hủy.');
            }

            $updated = $this->orders->updateStatus($id, OrderStatus::Cancelled);

            if ($current !== OrderStatus::Cancelled) {
                foreach ($order->items ?? [] as $item) {
                    $variantId = (int) ($item->product_variant_id ?? $item->variant_id ?? 0);
                    $qty = (int) ($item->quantity ?? 0);
                    $this->products->restoreStock($variantId, $qty);
                }
            }

            return $updated;
        });
    }
}