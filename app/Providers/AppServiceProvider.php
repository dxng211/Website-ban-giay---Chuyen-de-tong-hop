<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use App\Services\CartService;
use Illuminate\Support\Facades\Auth;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
        Route::pattern('id', '[0-9]+');
        Route::pattern('key', '[a-z0-9_]+');

        View::composer(['partials.header', 'partials._header'], function ($view) {
            $userId = Auth::id();
            $guestToken = $userId ? null : request()->cookie('cart_token');
            $cart = app(CartService::class)->getMine($userId, $guestToken);

            $view->with('cartCount', (int) ($cart?->total_quantity ?? 0));
        });
    }
}
