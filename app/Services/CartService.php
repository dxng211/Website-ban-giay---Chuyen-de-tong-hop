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
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);

        if (! $cart) {
            return null;
        }

        $total = 0;
        $totalQuantity = 0;

        foreach ($cart->items as $item) {
            $product = $item->variant?->product;
            $unitPrice = 0;

            if ($product) {
                $hasDiscount = $product->sale_price && $product->sale_price > 0 && $product->sale_price < $product->base_price;
                $unitPrice = (int) ($hasDiscount ? $product->sale_price : $product->base_price);
            }

            $subtotal = $unitPrice * (int) $item->quantity;
            $item->setAttribute('unit_price', $unitPrice);
            $item->setAttribute('subtotal', $subtotal);

            $total += $subtotal;
            $totalQuantity += (int) $item->quantity;
        }

        $cart->setAttribute('total', $total);
        $cart->setAttribute('total_quantity', $totalQuantity);

        return $cart;
    }

    /**
     * Thêm sản phẩm vào giỏ (Seq03).
     */
    public function addItem(?int $userId, ?string $guestToken, int $variantId, int $quantity): Cart
    {
        // TODO: $this->products->checkStock($variantId); (số đã có trong giỏ + quantity) > tồn kho -> throw InsufficientException
        // TODO: chưa có giỏ -> $this->carts->createCart(...); $this->carts->saveItem(...)
        $stock = $this->products->checkStock($variantId);
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);

        $existingQty = 0;
        if ($cart) {
            $existingItem = $cart->items->firstWhere('product_variant_id', $variantId);
            $existingQty = $existingItem ? (int) $existingItem->quantity : 0;
        }

        $newQty = $existingQty + $quantity;
        if ($stock <= 0 || $newQty > $stock) {
            throw new \DomainException("Số lượng đặt vượt quá tồn kho hiện có (còn {$stock} sản phẩm).");
        }

        if (! $cart) {
            $cart = $this->carts->createCart($userId, $guestToken);
        }

        $this->carts->saveItem($cart, $variantId, $newQty);

        return $this->getMine($userId, $guestToken);
    }

    /**
     * Sửa số lượng dòng giỏ (BR1-UC05: không vượt tồn kho).
     */
    public function updateItem(?int $userId, ?string $guestToken, int $itemId, int $quantity): Cart
    {
        // TODO: kiểm tra tồn kho rồi $this->carts->saveItem(...)
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);
        $item = $cart?->items->firstWhere('id', $itemId);

        if (! $cart || ! $item) {
            throw new \DomainException('Không tìm thấy sản phẩm trong giỏ hàng.');
        }

        $stock = $this->products->checkStock((int) $item->product_variant_id);
        if ($quantity > $stock) {
            throw new \DomainException("Số lượng tối đa có thể đặt cho phân loại này là {$stock}.");
        }

        $this->carts->saveItem($cart, (int) $item->product_variant_id, $quantity);

        return $this->getMine($userId, $guestToken);
    }

    /**
     * Xóa dòng khỏi giỏ.
     */
    public function removeItem(?int $userId, ?string $guestToken, int $itemId): Cart
    {
        // TODO: $this->carts->removeItem(...)
        $cart = $this->carts->findByUserOrSession($userId, $guestToken);

        if ($cart) {
            $this->carts->removeItem($cart, $itemId);
        }

        return $this->getMine($userId, $guestToken) ?? new Cart;
    }

    /**
     * Gộp giỏ Guest vào giỏ tài khoản khi đăng nhập hoặc đăng ký (do AuthService gọi).
     */
    public function mergeCart(int $userId, ?string $guestToken): void
    {
        // TODO: chưa có giỏ user -> gán user_id cho giỏ Guest; đã có -> cộng số lượng cùng biến thể (không vượt tồn kho)
        // TODO: xóa giỏ Guest sau khi gộp
        if (! $guestToken) {
            return;
        }

        $guestCart = $this->carts->findByUserOrSession(null, $guestToken);
        if (! $guestCart || $guestCart->items->isEmpty()) {
            if ($guestCart) {
                $this->carts->deleteCart($guestCart);
            }

            return;
        }

        $userCart = $this->carts->findByUserOrSession($userId, null);

        if (! $userCart) {
            $guestCart->update([
                'user_id' => $userId,
                'session_token' => null,
            ]);

            return;
        }

        foreach ($guestCart->items as $guestItem) {
            $variantId = (int) $guestItem->product_variant_id;
            $stock = $this->products->checkStock($variantId);

            if ($stock <= 0) {
                continue;
            }

            $existing = $userCart->items->firstWhere('product_variant_id', $variantId);
            $mergedQty = min($stock, ($existing ? (int) $existing->quantity : 0) + (int) $guestItem->quantity);

            $this->carts->saveItem($userCart, $variantId, $mergedQty);
        }

        $this->carts->deleteCart($guestCart);
    }
}
