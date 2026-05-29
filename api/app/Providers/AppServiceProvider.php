<?php

namespace App\Providers;

use App\Services\NavigationMenuService;
use App\Services\ProductPricingService;
use App\Services\SeoUrlService;
use App\Services\SettingsConfigService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(SettingsConfigService::class);
        $this->app->singleton(SeoUrlService::class);
        $this->app->singleton(ProductPricingService::class);
        $this->app->singleton(NavigationMenuService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
