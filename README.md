## Dashboard

[![Stable version](https://img.shields.io/badge/version-v1.0.2-green)](https://packagist.org/packages/akarumey95/dashboard)


1)For install `composer require akarumey95/dashboard`

2)Open your `config/app.php` and add the following to the `providers` array:
```php
Dashboard\Providers\ComposerServiceProvider::class,
Dashboard\Providers\DashboardServiceProvider::class,
```
3)And add to the `aliases` array:
```php
'Dashboard' => Dashboard\Facades\Dashboard::class,
```
4)Open `app/Console/Kernel.php` and add to `$commands` array:
```php
GenerateDashboardControllerCommand::class,
GenerateDashboardViewsCommand::class,
InstallDashboardCommand::class,
```
5)Run in console
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
