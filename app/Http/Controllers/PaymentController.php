<?php

namespace App\Http\Controllers;


use App\Services\PaymentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{

    public function __construct(
        private readonly PaymentService $paymentService,
    ) {}


    public function confirmPayment(int $id): RedirectResponse|JsonResponse
    {
        $result = $this->paymentService->confirmPayment($id);

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'message' => 'Đã xác nhận đã nhận tiền cho đơn hàng.',
                'data'    => $result,
            ]);
        }

        return back()->with('success', 'Đã xác nhận đã nhận tiền cho đơn hàng #' . $id . '.');
    }
}
