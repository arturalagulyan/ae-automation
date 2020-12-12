<?php

namespace Api\Providers;

use Illuminate\Support\ServiceProvider;

/**
 * Class ApiServiceProvider
 * @package Api\Providers
 */
class ApiServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any api services.
     *
     * @return void
     */
    public function boot()
    {

    }

    /**
     * Register any api services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->register(ApiAuthServiceProvider::class);
        $this->app->register(ApiEventServiceProvider::class);
        $this->app->register(ApiRoutesServiceProvider::class);
        $this->app->register(ApiHelpersServiceProvider::class);
        $this->app->register(ApiRelationsServiceProvider::class);
    }
}
