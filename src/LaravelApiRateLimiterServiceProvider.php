<?php

namespace Avnsh1111\LaravelApiRateLimiter;

use Illuminate\Support\ServiceProvider;

class LaravelApiRateLimiterServiceProvider extends ServiceProvider
{
    public function boot()
    {
        $this->publishes([
            __DIR__.'/../config/api_rate_limiter.php' => config_path('api_rate_limiter.php'),
        ], 'config');

        $this->loadRoutesFrom(__DIR__.'/../routes/api.php');
    }

    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/api_rate_limiter.php', 'api_rate_limiter'
        );
    }
}
