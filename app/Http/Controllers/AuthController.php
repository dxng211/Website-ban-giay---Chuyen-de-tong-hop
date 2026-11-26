<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use App\Services\AuthService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    public function __construct(
        private readonly AuthService $authService,
    ) {}

    public function login(LoginRequest $request): RedirectResponse
    {
        $guestToken = $request->cookie('cart_token');
        $user = $this->authService->login(
            $request->string('email')->toString(),
            $request->string('password')->toString(),
            $guestToken
        );

        if ($guestToken) {
            Cookie::queue(Cookie::forget('cart_token'));
        }

        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended');
        if ($intended && ! str_contains($intended, '/admin')) {
            return redirect()->to($intended)->with('success', 'Đăng nhập thành công.');
        }

        if ($request->input('redirect') === 'checkout' || str_contains((string) $request->headers->get('referer'), 'redirect=checkout')) {
            return redirect()->route('checkout')->with('success', 'Đăng nhập thành công.');
        }

        return redirect()->route('account')->with('success', 'Chào mừng ' . $user->full_name . ' quay trở lại!');
    }

    public function register(RegisterRequest $request): RedirectResponse
    {
        $guestToken = $request->cookie('cart_token');
        $this->authService->register($request->validated(), $guestToken);

        if ($guestToken) {
            Cookie::queue(Cookie::forget('cart_token'));
        }

        $request->session()->regenerate();

        $intended = $request->session()->pull('url.intended');
        if ($intended && ! str_contains($intended, '/admin')) {
            return redirect()->to($intended)->with('success', 'Đăng ký tài khoản thành công.');
        }

        if ($request->input('redirect') === 'checkout' || str_contains((string) $request->headers->get('referer'), 'redirect=checkout')) {
            return redirect()->route('checkout')->with('success', 'Đăng ký tài khoản thành công.');
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