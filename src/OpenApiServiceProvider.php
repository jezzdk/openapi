<?php

namespace Arkitechdev\OpenApi;

use Illuminate\Support\ServiceProvider;
use Arkitechdev\OpenApi\Console\OpenApiGenerate;

class OpenApiServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/config.php', 'openapi');

        $this->app->bind('openapi', function ($app) {
            return new OpenApi();
        });
    }

    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                OpenApiGenerate::class,
            ]);

            $this->publishes([
                __DIR__ . '/../config/config.php' => config_path('openapi.php'),
            ], 'config');
        }
    }
}
