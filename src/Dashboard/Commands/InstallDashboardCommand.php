<?php

namespace Akarumey95\Dashboard\Commands;

use Illuminate\Console\Command;
use InvalidArgumentException;

class InstallDashboardCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:install
                    {--views : Only scaffold the dashboard views}
                    {--force : Overwrite existing views by default}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Scaffold basic dashboard views and routes';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'views/user/index.stub' => 'dashboard/views/user/index.blade.php',
        'views/user/show.stub' => 'dashboard/views/user/show.blade.php',
        'views/user/form.stub' => 'dashboard/views/user/form.blade.php',
        'components/actions.stub' => 'dashboard/components/actions.blade.php',
        'components/dashboard-element.stub' => 'dashboard/components/dashboard-element.blade.php',
        'components/flash-message.stub' => 'dashboard/components/flash-message.blade.php',
        'components/form-errors.stub' => 'dashboard/components/form-errors.blade.php',
        'components/form-fields.stub' => 'dashboard/components/form-fields.blade.php',
        'components/sidebar.stub' => 'dashboard/components/sidebar.blade.php',
        'layouts/app.stub' => 'dashboard/layouts/app.blade.php',
    ];

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        $this->ensureDirectoriesExist();
        $this->exportViews();

        if (! $this->option('views')) {
            $this->exportConfig();
            $this->exportBackend();
        }

        $this->info('Dashboard scaffolding generated successfully.');
    }

    /**
     * Create the directories for the files.
     *
     * @return void
     */
    protected function ensureDirectoriesExist()
    {
        if (! is_dir($directory = $this->getViewPath('dashboard/components'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath('dashboard/layouts'))) {
            mkdir($directory, 0755, true);
        }

        if (! is_dir($directory = $this->getViewPath('dashboard/views/user'))) {
            mkdir($directory, 0755, true);
        }
    }

    /**
     * Export the dashboard views.
     *
     * @return void
     */
    protected function exportViews()
    {
        foreach ($this->views as $key => $value) {
            if (file_exists($view = $this->getViewPath($value)) && ! $this->option('force')) {
                if (! $this->confirm("The [{$value}] view already exists. Do you want to replace it?")) {
                    continue;
                }
            }

            copy(
                __DIR__ . '/../Stubs/default/views/' .$key,
                $view
            );
        }
    }

    /**
     * Export the dashboard config.
     *
     * @return void
     */
    protected function exportConfig()
    {
        $config = config_path('/dashboard.php');
        if (file_exists($config) && ! $this->option('force')) {
            if ($this->confirm("The [dashboard.php] file already exists. Do you want to replace it?")) {
                file_put_contents($config, $this->getConfigStub());
            }
        } else {
            file_put_contents($config, $this->getConfigStub());
        }
    }

    /**
     * Export the dashboard backend.
     *
     * @return void
     */
    protected function exportBackend()
    {
        $path = 'Http/Controllers/Application/Dashboard/Controllers/User';
        $controller = app_path($path. '/UserController.php');

        if (file_exists($controller) && ! $this->option('force')) {
            if ($this->confirm("The [UserController.php] file already exists. Do you want to replace it?")) {
                file_put_contents($controller, $this->compileControllerStub());
            }
        } else {
            if(!file_exists($controller)){
                mkdir(app_path($path), 0755, true);
            }
            file_put_contents($controller, $this->compileControllerStub());
        }

        if(! $this->option('force')){
            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__ . '/../Stubs/default/routes/routes.stub'),
                FILE_APPEND
            );
        }
    }

    /**
     * Compiles the "UserController" stub.
     *
     * @return string
     */
    protected function getConfigStub()
    {
        return file_get_contents(__DIR__ . '/../Stubs/default/config/dashboard.stub');
    }

    /**
     * Compiles the "UserController" stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            '{{namespace}}',
            $this->laravel->getNamespace(),
            file_get_contents(__DIR__ . '/../Stubs/default/controllers/UserController.stub')
        );
    }

    /**
     * Get full view path relative to the application's configured view path.
     *
     * @param  string  $path
     * @return string
     */
    protected function getViewPath($path)
    {
        return implode(DIRECTORY_SEPARATOR, [
            config('view.paths')[0] ?? resource_path('views'), $path,
        ]);
    }
}
