## Dashboard

[![Stable version](https://img.shields.io/badge/version-v1.0.5-green)](https://packagist.org/packages/akarumey95/dashboard)


1)For install `composer require akarumey95/dashboard`

2)Open your `config/app.php` and add the following to the `providers` array:
```php
Dashboard\Providers\DashboardServiceProvider::class,
```
and to the `aliases` array:
```php
'Dashboard' => Dashboard\Facades\Dashboard::class,
```
3)Run in console
```shell script
php artisan dashboard:install
```

## Dashboard Use

Generate Controller
```shell script
php artisan dashboard:controller {Path to Model after App\\}
```
Generate Views
```shell script
php artisan dashboard:view {ModelName}
```
Or Generate Controller with views
```shell script
php artisan dashboard:controller {Path to Model after App\\} --view
```
