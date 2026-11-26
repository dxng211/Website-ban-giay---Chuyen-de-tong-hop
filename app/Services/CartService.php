<?php

namespace App\Services;

use App\Exceptions\InsufficientException;
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
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);

        if (!$cart) {
            return null;
        }

        $subtotal = 0;
        foreach ($cart->items ?? [] as $item) {
            $price = (int) ($item->product->price ?? $item->variant->price ?? $item->price ?? 0);
            $qty = (int) ($item->quantity ?? 0);
            $subtotal += $price * $qty;
        }

        $cart->setAttribute('subtotal', $subtotal);
        $cart->setAttribute('total', $subtotal);

        return $cart;
    }

    /**
     * Thêm sản phẩm vào giỏ (Seq03).
     */
    public function addItem(?int $userId, ?string $guestToken, int $variantId, int $quantity): Cart
    {
        $quantity = max(1, $quantity);
        $availableStock = (int) $this->products->checkStock($variantId);

        $cart = $this->carts->findByUserOrSession($userId, $guestToken);
        $existingQty = 0;

        if ($cart && !empty($cart->items)) {
            $existingItem = $cart->items->firstWhere('product_variant_id', $variantId)
                ?? $cart->items->firstWhere('variant_id', $variantId);
            $existingQty = (int) ($existingItem->quantity ?? 0);
        }

        if (($existingQty + $quantity) > $availableStock) {
            throw new InsufficientException('Số lượng vượt quá tồn kho hiện có.');
        }

        if (!$cart) {
            $cart = $this->carts->createCart($userId, $guestToken);
        }

        $this->carts->saveItem($cart->id, $variantId, $existingQty + $quantity);

        return $this->getMine($userId, $guestToken) ?? $cart;
    }

    /**
     * Sửa số lượng dòng giỏ (BR1-UC05: không vượt tồn kho).
     */
    public function updateItem(?int $userId, ?string $guestToken, int $itemId, int $quantity): Cart
    {
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);

        if (!$cart) {
            throw new InsufficientException('Không tìm thấy giỏ hàng.');
        }

        $item = $cart->items->firstWhere('id', $itemId);
        if (!$item) {
            throw new InsufficientException('Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        if ($quantity <= 0) {
            $this->carts->removeItem($cart->id, $itemId);
            return $this->getMine($userId, $guestToken) ?? $cart;
        }

        $variantId = (int) ($item->product_variant_id ?? $item->variant_id ?? 0);
        $availableStock = (int) $this->products->checkStock($variantId);

        if ($quantity > $availableStock) {
            throw new InsufficientException('Số lượng vượt quá tồn kho hiện có.');
        }

        $this->carts->saveItem($cart->id, $variantId, $quantity);

        return $this->getMine($userId, $guestToken) ?? $cart;
    }

    /**
     * Xóa dòng khỏi giỏ.
     */
    public function removeItem(?int $userId, ?string $guestToken, int $itemId): Cart
    {
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);

        if ($cart) {
            $this->carts->removeItem($cart->id, $itemId);
        }

        return $this->getMine($userId, $guestToken) ?? $cart;
    }

    /**
     * Gộp giỏ Guest vào giỏ tài khoản khi đăng nhập hoặc đăng ký (do AuthService gọi).
     */
    public function mergeCart(int $userId, ?string $guestToken): void
    {
        if (!$guestToken) {
            return;
        }

        $guestCart = $this->carts->findByUserOrSession(null, $guestToken);
        if (!$guestCart) {
            return;
        }

        $userCart = $this->carts->findByUserOrSession($userId, null);

        if (!$userCart) {
            $guestCart->user_id = $userId;
            $guestCart->session_token = null;
            $guestCart->save();
            return;
        }

        foreach ($guestCart->items ?? [] as $item) {
            $variantId = (int) ($item->product_variant_id ?? $item->variant_id ?? 0);
            $guestQty = (int) ($item->quantity ?? 0);
            if ($variantId <= 0 || $guestQty <= 0) {
                continue;
            }

            $availableStock = (int) $this->products->checkStock($variantId);
            $existingItem = $userCart->items->firstWhere('product_variant_id', $variantId)
                ?? $userCart->items->firstWhere('variant_id', $variantId);
            $currentQty = (int) ($existingItem->quantity ?? 0);
            $mergedQty = min($availableStock, $currentQty + $guestQty);

            if ($mergedQty > 0) {
                $this->carts->saveItem($userCart->id, $variantId, $mergedQty);
            }
        }

        $this->carts->deleteCart($guestCart->id);
    }
}