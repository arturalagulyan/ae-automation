<?php

namespace Api\Providers;

use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Passport;
use Symfony\Component\Finder\Finder;

/**
 * Class ApiRoutesServiceProvider
 * @package Api\Providers
 */
class ApiRoutesServiceProvider extends ServiceProvider
{
    /**
     * @var string
     */
    protected $apiPrefix = 'api/1.0';

    /**
     * @var string
     */
    protected $apiNamespace = 'Api\Routes';

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->map();
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Define the routes for the application.
     *
     * @return void
     */
    public function map()
    {
        $this->mapApiRoutes();

        Passport::routes();
    }

    /**
     * Define the "api" routes for the application.
     *
     * These routes are typically stateless.
     *
     * @return void
     */
    protected function mapApiRoutes()
    {
        Route::prefix($this->apiPrefix)
            ->middleware([
                'before',
                'api',
            ])
            ->group(function ($router) {
                $this->registerApiRoutes();
            });
    }

    /**
     *
     */
    protected function registerApiRoutes()
    {
        $path = app_path('Api/Routes');

        $finder = new Finder();

        $files = $finder->in($path)
            ->files()
            ->name('*.php');

        foreach ($files as $file) {
            $routes = $path . '/' . $file->getFilename();
            require_once $routes;
        }
    }
}
