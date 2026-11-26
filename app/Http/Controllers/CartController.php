<?php

namespace App\Http\Controllers;

use App\Http\Requests\CartItemRequest;
use App\Services\CartService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Str;

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
        $userId = $request->user()?->id;
        $guestToken = $userId ? null : $request->cookie('cart_token');

        $cart = $this->cartService->getMine($userId, $guestToken);

        if ($request->expectsJson()) {
            return response()->json([
                'cart' => $cart,
                'cart_count' => (int) ($cart?->total_quantity ?? 0),
                'total' => (int) ($cart?->total ?? 0),
            ]);
        }

        return view('cart.index', compact('cart'));
    }

    /**
     * POST /cart/add (Seq03)
     */
    public function addItem(CartItemRequest $request): JsonResponse
    {
        // TODO: Guest chưa có cookie cart_token -> tạo token, gắn Cookie::queue vào response
        // TODO: gọi $this->cartService->addItem(...); trả giỏ mới để cập nhật badge
        $userId = $request->user()?->id;
        $guestToken = null;

        if (! $userId) {
            $guestToken = $request->cookie('cart_token');
            if (! $guestToken) {
                $guestToken = Str::random(40);
                Cookie::queue('cart_token', $guestToken, 60 * 24 * 30);
            }
        }

        $validated = $request->validated();

        try {
            $cart = $this->cartService->addItem(
                $userId,
                $guestToken,
                (int) $validated['product_variant_id'],
                (int) $validated['quantity']
            );

            return response()->json([
                'message' => 'Đã thêm sản phẩm vào giỏ hàng!',
                'cart_count' => (int) ($cart->total_quantity ?? 0),
                'total' => (int) ($cart->total ?? 0),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * PATCH /cart/items/{id}
     */
    public function updateItem(CartItemRequest $request, int $id): JsonResponse
    {
        // TODO: gọi $this->cartService->updateItem(...)
        $userId = $request->user()?->id;
        $guestToken = $userId ? null : $request->cookie('cart_token');

        try {
            $cart = $this->cartService->updateItem(
                $userId,
                $guestToken,
                $id,
                (int) $request->validated()['quantity']
            );

            $updatedItem = $cart->items->firstWhere('id', $id);

            return response()->json([
                'message' => 'Đã cập nhật số lượng.',
                'subtotal' => (int) ($updatedItem?->subtotal ?? 0),
                'total' => (int) ($cart->total ?? 0),
                'cart_count' => (int) ($cart->total_quantity ?? 0),
            ]);
        } catch (\DomainException $e) {
            return response()->json(['message' => $e->getMessage()], 422);
        }
    }

    /**
     * DELETE /cart/items/{id}
     */
    public function removeItem(Request $request, int $id): JsonResponse
    {
        // TODO: gọi $this->cartService->removeItem(...)
        $userId = $request->user()?->id;
        $guestToken = $userId ? null : $request->cookie('cart_token');

        $cart = $this->cartService->removeItem($userId, $guestToken, $id);

        return response()->json([
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
            'total' => (int) ($cart->total ?? 0),
            'cart_count' => (int) ($cart->total_quantity ?? 0),
        ]);
    }
}
