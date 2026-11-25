<?php

namespace App\Http\Controllers;

use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    //
    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    /**
     * GET /checkout (thêm mới: hàm chưa có trong Class Diagram)
     */
    public function showCheckout(): View|RedirectResponse
    {
        // TODO: giỏ rỗng -> redirect về giỏ hàng; ngược lại trả view thanh toán (tự điền users.default_address, phone)
    }

    /**
     * POST /checkout (Seq04)
     */
    public function checkout(CheckoutRequest $request): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->orderService->checkout(Auth::user(), $request->validated()); nhận Order + qrCodeUrl (có thể null)
        // TODO: bắt InsufficientException (hết hàng) -> quay lại giỏ hàng kèm thông báo
    }

    /**
     * GET /orders, /admin/orders
     */
    public function list(Request $request): View
    {
        // TODO: gọi $this->orderService->list(Auth::user(), $filters); Service quyết định phạm vi theo vai trò
    }

    /**
     * GET /orders/{id}, /admin/orders/{id}
     */
    public function getById(int $id): View
    {
        // TODO: gọi $this->orderService->getById($id, Auth::user())
    }

    /**
     * PATCH /admin/orders/{id}/status (UC10)
     */
    public function updateStatus(UpdateOrderStatusRequest $request, int $id): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->orderService->updateStatus($id, trạng thái mới)
    }

    /**
     * POST /orders/{id}/cancel, /admin/orders/{id}/cancel (Seq05)
     */
    public function cancel(Request $request, int $id): RedirectResponse|JsonResponse
    {
        // TODO: gọi $this->orderService->cancel($id, Auth::user()); bắt OrderCantBeCancelledException
    }
}
