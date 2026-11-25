<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Phụ thuộc: User (Model, truy cập trực tiếp), CartService (gộp giỏ khi đăng nhập).
 */
class AuthService
{
    public function __construct(
        private readonly CartService $cartService,
    ) {}

    /**
     * Đăng ký (UC01). Mật khẩu để Model tự mã hóa nhờ cast 'hashed', KHÔNG gọi Hash::make.
     */
    public function register(array $data, ?string $guestToken = null): User
    {
        $user = DB::transaction(function () use ($data) {
            return User::query()->create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'phone' => $data['phone'] ?? null,
                'default_address' => $data['default_address'] ?? null,
                'role' => 'customer',
                'is_locked' => false,
            ]);
        });

        Auth::login($user);
        $this->mergeGuestCartToUser($user->id, $guestToken);

        return $user;
    }

    /**
     * Đăng nhập (UC01).
     */
    public function login(string $email, string $password, ?string $guestToken = null): User
    {
        $user = User::query()->where('email', $email)->first();

        if (! $user || ! Hash::check($password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['Email hoặc mật khẩu không chính xác.'],
            ]);
        }

        if ((bool) $user->is_locked) {
            throw ValidationException::withMessages([
                'email' => ['Tài khoản đã bị khóa.'],
            ]);
        }

        Auth::login($user);
        $this->mergeGuestCartToUser($user->id, $guestToken);

        return $user;
    }

    /**
     * Đăng xuất.
     */
    public function logout(): void
    {
        // TODO: Auth::logout()
    }

    public function mergeGuestCartToUser(int $userId, ?string $guestToken): void
    {
        if (! $guestToken) {
            return;
        }

        DB::transaction(function () use ($userId, $guestToken) {
            $guestCart = Cart::query()
                ->whereNull('user_id')
                ->where('session_token', $guestToken)
                ->with('items.variant')
                ->first();

            if (! $guestCart || $guestCart->items->isEmpty()) {
                $guestCart?->delete();
                return;
            }

            $userCart = Cart::query()->firstOrCreate(['user_id' => $userId]);

            foreach ($guestCart->items as $guestItem) {
                $maxStock = (int) ($guestItem->variant?->stock_quantity ?? 0);
                if ($maxStock <= 0) {
                    continue;
                }

                $existingItem = CartItem::query()
                    ->where('cart_id', $userCart->id)
                    ->where('product_variant_id', $guestItem->product_variant_id)
                    ->first();

                if ($existingItem) {
                    $newQty = min($maxStock, $existingItem->quantity + $guestItem->quantity);
                    $existingItem->update(['quantity' => $newQty]);
                } else {
                    $qty = min($maxStock, $guestItem->quantity);
                    CartItem::query()->create([
                        'cart_id' => $userCart->id,
                        'product_variant_id' => $guestItem->product_variant_id,
                        'quantity' => $qty,
                    ]);
                }
            }

            $guestCart->items()->delete();
            $guestCart->delete();
        });
    }
}
