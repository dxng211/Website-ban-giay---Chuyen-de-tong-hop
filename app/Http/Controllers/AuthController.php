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

    public function showAdminLogin()
    {
        if (view()->exists('admin.auth.login')) {
            return view('admin.auth.login');
        }

        if (view()->exists('admin.login')) {
            return view('admin.login');
        }

        return view('auth.login');
    }

    public function adminLogin(Request $request): RedirectResponse
{
    $credentials = $request->validate([
        'email' => ['required', 'email'],
        'password' => ['required', 'string'],
    ]);

    $remember = $request->boolean('remember');

    if (!Auth::attempt($credentials, $remember)) {
        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Email hoặc mật khẩu không chính xác.']);
    }

    $request->session()->regenerate();

    $user = Auth::user();
    $roleValue = $user?->role instanceof \BackedEnum
        ? (string) $user->role->value
        : (string) ($user?->role ?? '');

    $isAdmin = $user && ((method_exists($user, 'isAdmin') && $user->isAdmin()) || $roleValue === 'admin');

    if (!$isAdmin) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Tài khoản này không có quyền truy cập khu vực quản trị.']);
    }

    if ($user->is_locked ?? false) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return back()
            ->withInput($request->only('email', 'remember'))
            ->withErrors(['email' => 'Tài khoản đã bị khóa.']);
    }

    return redirect()->intended(route('admin.dashboard'));
}
    /**
     * POST /login
     */
    public function login(LoginRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $cartToken = $request->cookie('cart_token');
        $remember = $request->boolean('remember');

        try {
            $user = $this->authService->login($data, $cartToken);
        } catch (\Throwable $e) {
            if (!Auth::attempt(['email' => $data['email'], 'password' => $data['password']], $remember)) {
                return back()
                    ->withInput($request->only('email', 'remember'))
                    ->withErrors(['email' => $e->getMessage() ?: 'Thông tin đăng nhập không hợp lệ.']);
            }
            $user = $request->user();
        }

        if ($user && !Auth::check()) {
            Auth::login($user, $remember);
        }

        $request->session()->regenerate();

        if ($user && ((method_exists($user, 'isAdmin') && $user->isAdmin()) || (($user->role ?? null) === 'admin'))) {
            return redirect()->intended(route('admin.dashboard'));
        }

        return redirect()->intended('/account');
    }

    /**
     * POST /logout
     */
    public function logout(Request $request): RedirectResponse
    {
        if (method_exists($this->authService, 'logout')) {
            $this->authService->logout();
        } else {
            Auth::logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
