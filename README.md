## Dashboard Requires

You must have already installed laravel auth or edit `middleware` in `routes/web.php` after completed installation.

## Dashboard Installation

1)In file `composer.json` add `"Dashboard\\": "akarumey95/dashboard/src/Dashboard"`:

Before:
```json
{
"autoload": {
        "psr-4": {
            "App\\": "app/"
        }
    }
} 
```
After:
```json
{
"autoload": {
        "psr-4": {
            "App\\": "app/",
            "Dashboard\\": "akarumey95/dashboard/src/Dashboard"
        }
    }
}
```
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
composer du
php artisan dashboard:install
```

## Dashboard Use

Generate Controller
```shell script
php artisan dashboard:controller {ModelName or ControllerName}
```
Generate Views
```shell script
php artisan dashboard:view {ModelName or DirectoreName}
```
After Created Controller and Views add all configurations for them in `config/dashboard.php` 
