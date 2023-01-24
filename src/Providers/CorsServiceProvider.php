<?php

namespace Mini\Framework\Providers;

use Illuminate\Support\ServiceProvider;
use Mini\Framework\Http\Middleware\Cors\CorsService;

class CorsServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $this->app->singleton(CorsService::class, function () {
            return new CorsService($this->app['config']->get('cors'));
        });
    }
}
