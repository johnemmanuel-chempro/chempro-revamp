<?php

namespace App\Providers;

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
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
