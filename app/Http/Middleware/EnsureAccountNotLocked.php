<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureAccountNotLocked
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && (bool) $user->is_locked) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Tài khoản đã bị khóa.'], 403);
            }

            return redirect()->route('account')
                ->with('error', 'Tài khoản của bạn đã bị khóa. Vui lòng liên hệ cửa hàng để được hỗ trợ.');
        }

        return $next($request);
    }
}
