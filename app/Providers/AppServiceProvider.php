<?php

namespace App\Providers;

use App\Services\CartService;
use App\Services\Payments\ManualPaymentGateway;
use App\Services\Payments\PaymentGatewayInterface;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(CartService::class);
        $this->app->bind(PaymentGatewayInterface::class, ManualPaymentGateway::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
