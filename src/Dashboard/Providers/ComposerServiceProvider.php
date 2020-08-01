<?php

namespace Akarumey95\Dashboard\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class ComposerServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
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
