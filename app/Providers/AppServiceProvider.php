<?php

namespace App\Providers;

use App\Services\ExpenseService;
use App\Services\InvoiceService;
use App\Services\MikrotikService;
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

        $this->app->singleton(InvoiceService::class, function ($app) {
            return new InvoiceService();
        });

        $this->app->singleton(ExpenseService::class, function ($app) {
            return new ExpenseService();
        });

        // $this->app->singleton(MikrotikService::class, function ($app) {
        //     return new MikrotikService();
        // });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
