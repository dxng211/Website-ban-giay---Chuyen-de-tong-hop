<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    //
    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}

    /**
     * GET /orders/{id}/qr - hiện lại mã QR của đơn chuyển khoản
     */
    public function getQr(int $id): JsonResponse
    {
        // TODO: gọi $this->paymentService->getQr($id); chỉ chủ đơn hàng hoặc Admin được xem
    }

    /**
     * PATCH /admin/orders/{id}/payment - Admin xác nhận đã nhận tiền
     */
    public function confirmPayment(int $id): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->paymentService->confirmPayment($id)
    }
}
