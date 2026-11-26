<?php

namespace App\Services;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
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
        if (User::query()->where('email', $data['email'])->exists()) {
            throw ValidationException::withMessages([
                'email' => 'Email này đã được sử dụng.',
            ]);
        }

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => $data['password'],
            'phone' => $data['phone'] ?? null,
            'address' => $data['address'] ?? null,
            'role' => Role::Customer,
            'is_locked' => false,
        ]);

        Auth::login($user);

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        $this->cartService->mergeCart($user->id, $guestToken);

        return $user;
    }

    /**
     * Đăng nhập (UC01).
     */
    public function login(array $credentials, ?string $guestToken = null, bool $remember = false): User
    {
        if (!Auth::attempt([
            'email' => $credentials['email'],
            'password' => $credentials['password'],
        ], $remember)) {
            throw ValidationException::withMessages([
                'email' => 'Email hoặc mật khẩu không chính xác.',
            ]);
        }

        /** @var User $user */
        $user = Auth::user();

        if (!empty($user->is_locked)) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => 'Tài khoản của bạn đã bị khóa.',
            ]);
        }

        if (request()->hasSession()) {
            request()->session()->regenerate();
        }

        $this->cartService->mergeCart($user->id, $guestToken);

        return $user;
    }

    /**
     * Đăng xuất.
     */
    public function logout(): void
    {
        Auth::logout();

        if (request()->hasSession()) {
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
    }
}
