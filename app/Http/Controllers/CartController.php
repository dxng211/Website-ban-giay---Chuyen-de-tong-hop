<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    //
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    /**
     * GET /cart
     */
    public function getMine(Request $request): View|JsonResponse
    {
        // TODO: xác định chủ giỏ: Auth::id() hoặc cookie cart_token; gọi $this->cartService->getMine(...)
    }

    /**
     * POST /cart/add (Seq03)
     */
    public function addItem(CartItemRequest $request): JsonResponse
    {
        // TODO: Guest chưa có cookie cart_token -> tạo token, gắn Cookie::queue vào response
        // TODO: gọi $this->cartService->addItem(...); trả giỏ mới để cập nhật badge
    }

    /**
     * PATCH /cart/items/{id}
     */
    public function updateItem(CartItemRequest $request, int $id): JsonResponse
    {
        // TODO: gọi $this->cartService->updateItem(...)
    }

    /**
     * DELETE /cart/items/{id}
     */
    public function removeItem(Request $request, int $id): JsonResponse
    {
        // TODO: gọi $this->cartService->removeItem(...)
    }
}
