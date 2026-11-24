<?php

namespace App\Services;

use App\Models\Order;
use App\Repositories\OrderRepository;
use App\Services\Clients\VietQrClient;

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
        // TODO: nếu là id -> $this->orders->findById(); lỗi sinh QR -> throw PaymentException
        // TODO: return $this->vietQr->generateQrUrl($order->total_amount, $order->order_code)
    }

    /**
     * Admin xác nhận đã nhận tiền: payment_status = paid.
     */
    public function confirmPayment(int $orderId): Order
    {
        // TODO: $this->orders->updatePaymentStatus($orderId, PaymentStatus::Paid)
    }
}
