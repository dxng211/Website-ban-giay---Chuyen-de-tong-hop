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
    /**
     * Danh sách đơn: theo user (Customer) hoặc tất cả (Admin), có lọc trạng thái, phân trang.
     */
    public function list(?int $userId, array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = \App\Models\Order::query();

        if ($userId !== null) {
            $query->where('user_id', $userId);
        }

        $status = $filters['status'] ?? $filters['order_status'] ?? null;
        if (!empty($status)) {
            $query->where('order_status', $status);
        }

        $paymentStatus = $filters['payment_status'] ?? null;
        if (!empty($paymentStatus) && \Illuminate\Support\Facades\Schema::hasColumn('orders', 'payment_status')) {
            $query->where('payment_status', $paymentStatus);
        }

        $search = trim((string) ($filters['q'] ?? $filters['search'] ?? ''));
        if ($search !== '') {
            $query->where(function ($q) use ($search) {
                if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'order_code')) {
                    $q->where('order_code', 'like', "%{$search}%");
                } elseif (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'code')) {
                    $q->where('code', 'like', "%{$search}%");
                } else {
                    $q->where('id', $search);
                }

                if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'customer_name')) {
                    $q->orWhere('customer_name', 'like', "%{$search}%");
                }
                if (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'recipient_name')) {
                    $q->orWhere('recipient_name', 'like', "%{$search}%");
                }
            });
        }

        $perPage = (int) ($filters['per_page'] ?? 15);
        $perPage = $perPage > 0 ? min($perPage, 100) : 15;

        return $query->latest('id')->paginate($perPage)->withQueryString();
    }

    /**
     * Tổng doanh thu (chỉ đơn completed, cần thống nhất trong nhóm).
     */
    public function sumRevenue(): int
    {
        $query = \App\Models\Order::query()->where(function ($q) {
            $q->where('order_status', 'completed');
            if (class_exists(\App\Enums\OrderStatus::class) && defined('\App\Enums\OrderStatus::Completed')) {
                $q->orWhere('order_status', \App\Enums\OrderStatus::Completed->value);
            }
        });

        $column = \Illuminate\Support\Facades\Schema::hasColumn('orders', 'total_amount')
            ? 'total_amount'
            : (\Illuminate\Support\Facades\Schema::hasColumn('orders', 'total') ? 'total' : 'total_amount');

        return (int) $query->sum($column);
    }

    /**
     * Tổng số đơn hàng.
     */
    public function countOrder(): int
    {
        return (int) \App\Models\Order::query()->count();
    }

    /**
     * Đếm số đơn theo từng trạng thái (trả về mảng ['pending' => ..., ...]).
     */
    public function countGroupByStatus(): array
    {
        $rows = \App\Models\Order::query()
            ->selectRaw('order_status, COUNT(*) as total')
            ->groupBy('order_status')
            ->get();

        $result = [];
        foreach ($rows as $row) {
            $status = $row->order_status instanceof \BackedEnum
                ? (string) $row->order_status->value
                : (string) ($row->order_status ?? '');
            $result[$status] = (int) $row->total;
        }

        return $result;
    }

    /**
     * Danh sách đơn hàng gần nhất.
     */
    public function getRecent(int $limit = 10)
    {
        return \App\Models\Order::query()
            ->latest()
            ->limit($limit)
            ->get();
    }
}
