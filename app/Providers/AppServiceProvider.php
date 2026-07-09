<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\BinderByteService;
use App\Services\RajaOngkirService;
use App\Services\ShippingService;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // BinderByte tetap dipakai khusus untuk fitur lacak resi (tracking)
        $this->app->singleton(BinderByteService::class, fn() => new BinderByteService());
        // RajaOngkir dipakai untuk data wilayah & cek ongkir
        $this->app->singleton(RajaOngkirService::class, fn() => new RajaOngkirService());
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
