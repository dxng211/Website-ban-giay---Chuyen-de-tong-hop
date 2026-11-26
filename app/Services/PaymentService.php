<?php

namespace App\Services;

use App\Enums\PaymentStatus;
use App\Exceptions\PaymentException;
use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Clients\VietQrClient;
use Throwable;

/**
 * Phụ thuộc: VietQrClient, OrderRepository.
 */
class PaymentService
{
    public function __construct(
        private readonly VietQrClient $vietQr,
        private readonly OrderRepository $orders,
    ) {}

    /**
     * Lấy URL mã QR VietQR cho đơn chuyển khoản (Seq04). Nhận Order hoặc id đơn.
     */
    public function getQr(Order|int $order): string
    {
        try {
            $targetOrder = $order instanceof Order ? $order : $this->orders->findById($order);

            return (string) $this->vietQr->generateQrUrl(
                (int) $targetOrder->total_amount,
                (string) $targetOrder->order_code
            );
        } catch (Throwable $e) {
            throw new PaymentException('Không thể tạo mã QR thanh toán: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * Admin xác nhận đã nhận tiền: payment_status = paid.
     */
    public function confirmPayment(int $orderId): Order
    {
        return $this->orders->updatePaymentStatus($orderId, PaymentStatus::Paid);
    }
}
