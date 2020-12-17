<?php

namespace Renderer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class RendererServiceProvider
 * @package Renderer\Providers
 */
class RendererServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->publishConfigs();
    }

    /**
     *
     */
    public function register()
    {
        $this->app->register(EventServiceProvider::class);
        $this->app->register(ConfigServiceProvider::class);
    }

    /**
     *
     */
    public function publishConfigs()
    {
        $this->publishes([
            __DIR__ . '../../config/renderer.php' => config_path('renderer.php'),
        ]);
    }
}
