<?php

namespace Renderer\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ConfigServiceProvider
 * @package Renderer\Providers
 */
class ConfigServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->configureLogging();
    }

    /**
     *
     */
    public function register()
    {
        foreach (glob(__DIR__ . '/../Helpers/*.php') as $filename) {
            require_once($filename);
        }
    }

    /**
     *
     */
    public function configureLogging()
    {
        $config = config('renderer.logging.channel');
        config(['logging.channels.renderer' => $config]);
    }
}
