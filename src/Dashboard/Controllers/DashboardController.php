<?php

namespace Akarumey95\Dashboard\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Pipeline\Pipeline;

abstract class DashboardController extends Controller
{
    protected static $config;

    protected static $view;
    protected static $modelName;
    protected static $model;
    protected static $homePage;
    protected static $paginateBy;
    protected static $filters;
    protected static $sorts;


    public function __construct()
    {
        $config = \Dashboard::getController(static::$config);

        static::$view       =   isset($config['view'])      ? $config['view']       : null;
        static::$modelName  =   isset($config['modelName']) ? $config['modelName']  : null;
        static::$model      =   isset($config['model'])     ? $config['model']      : null;
        static::$homePage   =   isset($config['homePage'])  ? $config['homePage']   : null;
        static::$paginateBy =   isset($config['paginateBy'])? $config['paginateBy'] : null;
        static::$filters    =   isset($config['filters'])   ? $config['filters']    : null;
        static::$sorts      =   isset($config['sorts'])     ? $config['sorts']      : null;
    }

    public function index()
    {
        $items =  app(Pipeline::class)
            ->send(static::$model::query())
            ->through(static::$filters)
            ->thenReturn();

        if(!is_null(\request()->sort)){
            $sortValue = explode('|', \request()->sort);
            if(array_search($sortValue[0], static::$sorts) !== false){
                $items->orderBy($sortValue[0], $sortValue[1]);
            }
        }

        return view(static::$view . '.index', [
            'items'     => $items->paginate(static::$paginateBy),
        ]);
    }

    public function show(int $id)
    {
        return view(static::$view . '.show', [
            'item'     => static::$model::findOrFail($id),
        ]);
    }

    public function create()
    {
        return view(static::$view . '.form');
    }

    public function store(Request $request)
    {
        static::$model::create($request->all());
        return redirect(static::$homePage)
            ->with('flash_message', static::$modelName . ' success added!');
    }

    public function edit(int $id){
        return view(static::$view . '.form', [
            'item' => static::$model::findOrFail($id),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $data = array_filter($request->all(),
            function ($item){
                return !is_null($item);
            });

        static::$model::findOrFail($id)->update($data);
        return redirect(static::$homePage)
            ->with('flash_message', static::$modelName . ' success updated!');
    }

    public function destroy(int $id)
    {
        static::$model::findOrFail($id)->delete();
        return redirect(static::$homePage)
            ->with('flash_message', static::$modelName . ' success deleted!');
    }
}
