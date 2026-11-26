<?php

namespace App\Http\Controllers;

use App\Exceptions\InsufficientException;
use App\Exceptions\OrderCantBeCancelledException;
use App\Http\Requests\CheckoutRequest;
use App\Http\Requests\UpdateOrderStatusRequest;
use App\Services\OrderService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class OrderController extends Controller
{

    public function __construct(
        private readonly OrderService $orderService,
    ) {}

    
    public function showCheckout(): View|RedirectResponse
    {
        $cartData = $this->orderService->getCheckoutData(Auth::user());

        if (empty($cartData['items']) || count($cartData['items']) === 0) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng đang trống.');
        }

        return view('checkout.index', array_merge($cartData, [
            'user' => Auth::user(),
        ]));
    }

    public function checkout(CheckoutRequest $request): JsonResponse
    {
        try {
            $result = $this->orderService->checkout(Auth::user(), $request->validated());

            $order = is_array($result) ? ($result['order'] ?? null) : $result;
            $qrCodeUrl = is_array($result) ? ($result['qr_code_url'] ?? $result['qrCodeUrl'] ?? null) : ($result->qr_code_url ?? null);
            $qrError = is_array($result) ? ($result['qr_error'] ?? $result['qrError'] ?? null) : null;

            return response()->json([
                'message'      => 'Đặt hàng thành công.',
                'order_id'     => $order->id ?? null,
                'order_code'   => $order->code ?? ($order->id ?? null),
                'qr_code_url'  => $qrCodeUrl,
                'qr_error'     => $qrError,
                'redirect_url' => route('account.index'),
            ], 201);
        } catch (InsufficientException $e) {
            return response()->json([
                'message'      => $e->getMessage() ?: 'Sản phẩm hoặc size không đủ tồn kho.',
                'redirect_url' => route('cart.index'),
            ], 422);
        }
    }

    public function list(Request $request): View
    {
        $filters = $request->only(['status', 'q']);
        $orders = $this->orderService->list(Auth::user(), $filters);

        return view('admin.orders.index', compact('orders', 'filters'));
    }


    public function getById(int $id): View
    {
        $order = $this->orderService->getById($id, Auth::user());

        return view('admin.orders.show', compact('order'));
    }


    public function updateStatus(UpdateOrderStatusRequest $request, int $id): RedirectResponse|JsonResponse
    {
        $status = $request->validated()['status'];
        $order = $this->orderService->updateStatus($id, $status);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['message' => 'Cập nhật trạng thái thành công.', 'data' => $order]);
        }

        return back()->with('success', 'Cập nhật trạng thái đơn hàng thành công.');
    }

    public function cancel(Request $request, int $id): RedirectResponse|JsonResponse
    {
        try {
            $order = $this->orderService->cancel($id, Auth::user());

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => 'Hủy đơn hàng thành công.', 'data' => $order]);
            }

            return back()->with('success', 'Hủy đơn hàng thành công và đã hoàn tồn kho.');
        } catch (OrderCantBeCancelledException $e) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['message' => $e->getMessage()], 422);
            }

            return back()->with('error', $e->getMessage());
        }
    }
}