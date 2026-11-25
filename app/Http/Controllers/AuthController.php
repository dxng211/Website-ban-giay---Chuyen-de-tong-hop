<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    //
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    /**
     * POST /register
     */
    public function register(RegisterRequest $request): RedirectResponse
    {
        // TODO: gọi $this->authService->register($request->validated()), truyền cookie cart_token của Guest để gộp giỏ
        // TODO: chuyển hướng: Admin -> route('admin.dashboard'); Customer -> redirect()->intended(route('account'))
    }

    /**
     * POST /login
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        // TODO: gọi $this->authService->login(...), truyền cookie cart_token để gộp giỏ; lỗi sai mật khẩu hoặc tài khoản bị khóa -> withErrors
        // TODO: Guest bấm Thanh toán rồi đăng nhập xong phải quay lại /checkout (redirect()->intended)
    }

    /**
     * POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        // TODO: gọi $this->authService->logout(); invalidate session và regenerateToken; chuyển về route('home')
    }
}
