<?php

namespace DataPlay\Services;

use Illuminate\Support\ServiceProvider;

class DataPlayServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton('dataplay.services.querylog', fn () => new QueryLog);
    }

    public function boot(): void
    {
        // config
        $this->mergeConfigFrom(
            __DIR__ . '/../config/dataplay.php',
            'dataplay'
        );

        // optional. publish assets
        $this->publishes([
            __DIR__ . '/../config/dataplay.php' => config_path('dataplay.php'),
        ], 'dataplay-config');
    }
}
