<?php

namespace App\Services;

use App\Models\Cart;
use App\Repositories\CartRepository;
use App\Repositories\ProductRepository;

/**
 * Phụ thuộc: CartRepository, ProductRepository.
 * Chủ giỏ được truyền vào rõ ràng: $userId (Customer) hoặc $guestToken (Guest). Sequence chỉ vẽ gọn nên không ghi hai tham số này.
 */
class CartService
{
    public function __construct(
        private readonly CartRepository $carts,
        private readonly ProductRepository $products,
    ) {}

    /**
     * Lấy giỏ hàng kèm Subtotal và Total (UC05).
     */
    public function getMine(?int $userId, ?string $guestToken): ?Cart
    {
        // TODO: $this->carts->findByUserOrSession(...); tính Subtotal, Total
    }

    /**
     * Thêm sản phẩm vào giỏ (Seq03).
     */
    public function addItem(?int $userId, ?string $guestToken, int $variantId, int $quantity): Cart
    {
        // TODO: $this->products->checkStock($variantId); (số đã có trong giỏ + quantity) > tồn kho -> throw InsufficientException
        // TODO: chưa có giỏ -> $this->carts->createCart(...); $this->carts->saveItem(...)
    }

    /**
     * Sửa số lượng dòng giỏ (BR1-UC05: không vượt tồn kho).
     */
    public function updateItem(?int $userId, ?string $guestToken, int $itemId, int $quantity): Cart
    {
        // TODO: kiểm tra tồn kho rồi $this->carts->saveItem(...)
    }

    /**
     * Xóa dòng khỏi giỏ.
     */
    public function removeItem(?int $userId, ?string $guestToken, int $itemId): Cart
    {
        // TODO: $this->carts->removeItem(...)
    }

    /**
     * Gộp giỏ Guest vào giỏ tài khoản khi đăng nhập hoặc đăng ký (do AuthService gọi).
     */
    public function mergeCart(int $userId, ?string $guestToken): void
    {
        // TODO: chưa có giỏ user -> gán user_id cho giỏ Guest; đã có -> cộng số lượng cùng biến thể (không vượt tồn kho)
        // TODO: xóa giỏ Guest sau khi gộp
    }
}
