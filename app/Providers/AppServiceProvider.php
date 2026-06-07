<?php

namespace App\Providers;

use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\StripePaymentGateway;
use App\Services\Payments\TapPaymentGateway;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PaymentGatewayInterface::class, function () {
            return match (config('store.payment_gateway')) {
                'tap' => new TapPaymentGateway(),
                default => new StripePaymentGateway(),
            };
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('checkout', function (Request $request) {
            return Limit::perMinute(8)->by($request->user()?->id ?: $request->ip());
        });
    }
}
