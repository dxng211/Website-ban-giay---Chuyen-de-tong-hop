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
    }

    /**
     * Thêm dòng hoặc cập nhật số lượng nếu biến thể đã có trong giỏ (Seq03).
     */
    public function saveItem(Cart $cart, int $variantId, int $quantity): CartItem
    {
        // TODO: updateOrCreate
    }

    /**
     * Xóa một dòng khỏi giỏ.
     */
    public function removeItem(Cart $cart, int $itemId): void
    {
        // TODO: viết truy vấn
    }

    /**
     * Xóa toàn bộ dòng của giỏ người dùng sau khi đặt hàng (Seq04).
     */
    public function clearCart(int $userId): void
    {
        // TODO: viết truy vấn
    }

    /**
     * Tạo giỏ mới - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function createCart(?int $userId, ?string $guestToken): Cart
    {
        // TODO: Cart::create(...)
    }

    /**
     * Xóa giỏ Guest sau khi gộp - thêm mới: hàm chưa có trong Class Diagram.
     */
    public function deleteCart(Cart $cart): void
    {
        // TODO: $cart->delete()
    }
}
