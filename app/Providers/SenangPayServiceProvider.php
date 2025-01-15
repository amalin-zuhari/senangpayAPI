<?php

namespace App\Providers;

use App\Services\SenangPayService;
use Illuminate\Support\ServiceProvider;

class SenangPayServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(SenangPayService::class, function ($app) {
            return new SenangPayService();
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
