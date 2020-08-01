<?php

namespace Akarumey95\Dashboard\Providers;

use Akarumey95\Dashboard\Dashboard;
use Akarumey95\Dashboard\DashboardRouteMethods;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DashboardServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        App::bind('dashboard',function() {
            return new Dashboard;
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Route::mixin(new DashboardRouteMethods);
    }
}
