<?php

namespace Uploader;

use Illuminate\Support\ServiceProvider;

/**
 * Class UploaderServiceProvider
 * @package Uploader
 */
class UploaderServiceProvider extends ServiceProvider
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
    public function publishConfigs()
    {
        $this->publishes([
            __DIR__.'../config/uploader.php' => config_path('uploader.php'),
        ]);
    }
}
