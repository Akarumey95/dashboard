<?php

namespace Akarumey95\Dashboard\Providers;

use Akarumey95\Dashboard\Commands\GenerateDashboardControllerCommand;
use Akarumey95\Dashboard\Commands\GenerateDashboardViewsCommand;
use Akarumey95\Dashboard\Commands\InstallDashboardCommand;
use Akarumey95\Dashboard\Dashboard;
use Akarumey95\Dashboard\DashboardRouteMethods;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
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
        $this->commands([
            GenerateDashboardControllerCommand::class,
            GenerateDashboardViewsCommand::class,
            InstallDashboardCommand::class,
        ]);

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

        $controllers = config('dashboard.controllers');

        if($controllers){
            foreach ($controllers as $controller){
                View::composer([
                    $controller['view'] . '.index',
                    $controller['view'] . '.show',
                    $controller['view'] . '.form',
                ], function($view) use ($controller) {
                    $view->with([
                        'homePage' => $controller['homePage'],
                        'modelName' => $controller['modelName'],
                    ]);
                });

                View::composer([
                    $controller['view'] . '.index',
                ], function($view) use ($controller) {
                    $view->with([
                        'modelSorts'=> $controller['sorts'],
                    ]);
                });

                View::composer([
                    $controller['view'] . '.form',
                ], function($view) use ($controller) {
                    $view->with([
                        'fillable' => (new $controller['model'])->getFillable(),
                    ]);
                });
            }
        }
    }
}
