<?php

namespace App\Services;

use App\Models\User;

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
        // TODO: kiểm tra email trùng (BR1); User::create(...) với role = Role::Customer
        // TODO: Auth::login($user); gộp giỏ Guest bằng $this->cartService->mergeCart($user->id, $guestToken)
    }

    /**
     * Đăng nhập (UC01).
     */
    public function login(array $credentials, ?string $guestToken = null, bool $remember = false): User
    {
        // TODO: Auth::attempt(...); sai -> báo lỗi; is_locked = true -> logout và báo tài khoản bị khóa
        // TODO: session()->regenerate(); gộp giỏ Guest bằng $this->cartService->mergeCart(...)
    }

    /**
     * Đăng xuất.
     */
    public function logout(): void
    {
        // TODO: Auth::logout()
    }
}
