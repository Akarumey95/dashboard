<?php

namespace Akarumey95\Dashboard\Facades;

use Akarumey95\Dashboard\Providers\DashboardServiceProvider;
use Illuminate\Support\Facades\Facade;
use RuntimeException;

class Dashboard extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'dashboard';
    }

    /**
     * Register the typical authentication routes for an application.
     *
     * @return void
     */
    public static function routes()
    {
        if (! static::$app->providerIsLoaded(DashboardServiceProvider::class)) {
            throw new RuntimeException('In order to use the Dashboard::routes() method, please install the akarumey95/dashboard package.');
        }

        static::$app->make('router')->auth();
    }
}
