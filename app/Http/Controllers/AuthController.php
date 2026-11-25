<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): RedirectResponse
    {
        $guestToken = $request->cookie('cart_session_token');
        $user = $this->authService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $guestToken
        );

        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended');
        if ($intended && ! str_contains($intended, '/admin')) {
            return redirect()->to($intended)->with('success', 'Đăng nhập thành công.');
        }

        return redirect()->route('account')->with('success', 'Chào mừng ' . $user->full_name . ' quay trở lại!');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $guestToken = $request->cookie('cart_session_token');
        $this->authService->register($request->validated(), $guestToken);

        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended');
        if ($intended && ! str_contains($intended, '/admin')) {
            return redirect()->to($intended)->with('success', 'Đăng ký tài khoản thành công.');
        }

        return redirect()->route('account')->with('success', 'Đăng ký tài khoản thành công.');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('account')->with('info', 'Bạn đã đăng xuất.');
    }
}