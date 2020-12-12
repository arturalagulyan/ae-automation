<?php

namespace Api\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ApiHelpersServiceProvider
 * @package Api\Providers
 */
class ApiHelpersServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {

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
}
