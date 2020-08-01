<?php

namespace Akarumey95\Dashboard;

class Dashboard
{
    public function getControllers()
    {
        return config('dashboard.controllers');
    }

    public function getController($name)
    {
        return config('dashboard.controllers.' . $name);
    }

    public function getMenu()
    {
        return config('dashboard.menu');
    }

    public function getNamespace()
    {
        return config('dashboard.namespace');
    }
}
