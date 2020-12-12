<?php

namespace Api\Providers;

use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\ServiceProvider;

/**
 * Class ApiRelationsServiceProvider
 * @package Api\Providers
 */
class ApiRelationsServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
         Relation::morphMap([
             
         ]);
    }
}
