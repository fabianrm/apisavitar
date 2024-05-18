<?php

namespace App\Providers;

use App\Services\CustomerService;
use App\Services\UtilService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(UtilService::class, function ($app) {
            return new UtilService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
