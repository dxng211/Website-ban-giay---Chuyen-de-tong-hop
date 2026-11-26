<?php

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartItem;

/**
 * Truy vấn nhóm bảng carts, cart_items.
 */
class CartRepository
{
    /**
     * Tìm giỏ của Customer (user_id) hoặc của Guest (session_token), kèm dòng giỏ.
     */
    public function findByUserOrSession(?int $userId, ?string $guestToken): ?Cart
    {
        // TODO: Cart::with('items.variant.product.images')->...
        if (! $userId && ! $guestToken) {
            return null;
        }

        $query = Cart::query()->with([
            'items' => fn ($q) => $q->orderBy('id'),
            'items.variant.product.images' => fn ($q) => $q->orderBy('sort_order')->orderBy('id'),
        ]);

        if ($userId) {
            return $query->where('user_id', $userId)->first();
        }

        return $query->whereNull('user_id')
            ->where('session_token', $guestToken)
            ->first();
    }

    /**
     * Thêm dòng hoặc cập nhật số lượng nếu biến thể đã có trong giỏ (Seq03).
     */
    public function saveItem(Cart $cart, int $variantId, int $quantity): CartItem
    {
        // TODO: updateOrCreate
        return $cart->items()->updateOrCreate(
            ['product_variant_id' => $variantId],
            ['quantity' => $quantity]
        );
    }

    /**
     * Xóa một dòng khỏi giỏ.
     */
    public function removeItem(Cart $cart, int $itemId): void
    {
        // TODO: viết truy vấn
        $cart->items()->whereKey($itemId)->delete();
    }

    /**
     * Xóa toàn bộ dòng của giỏ người dùng sau khi đặt hàng (Seq04).
     */
    public function clearCart(int $userId): void
    {
        // TODO: viết truy vấn
        $cart = Cart::where('user_id', $userId)->first();

        if ($cart) {
            $cart->items()->delete();
        }
    }

    /**
     * Tạo giỏ mới - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function createCart(?int $userId, ?string $guestToken): Cart
    {
        // TODO: Cart::create(...)
        return Cart::create([
            'user_id' => $userId,
            'session_token' => $userId ? null : $guestToken,
        ]);
    }

    /**
     * Xóa giỏ Guest sau khi gộp - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function deleteCart(Cart $cart): void
    {
        // TODO: $cart->delete()
        $cart->items()->delete();
        $cart->delete();
    }
}
