<?php


namespace Akarumey95\Dashboard\Filters;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

abstract class AbstractFilters
{
    public function handle($request, Closure $next)
    {
        return !request()->has($this->filterName()) ?
                $next($request) :
                $this->applyFilter($next($request));

    }
    protected function filterName()
    {
        return Str::snake(class_basename($this));
    }
    protected abstract function applyFilter(Builder $builders);

}
