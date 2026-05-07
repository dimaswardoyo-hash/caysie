<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BinderByteService;
use App\Services\BiteshipService;
use App\Services\ShippingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(BinderByteService::class, fn() => new BinderByteService());
        $this->app->singleton(BiteshipService::class, fn() => new BiteshipService());
        $this->app->singleton(ShippingService::class, fn() => new ShippingService());
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
