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
        'components/t-header.stub' => 'dashboard/components/t-header.blade.php',
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
        $this->exportAssets();

        $this->ensureDirectoriesExist();
        $this->exportViews();
        $this->exportFilters();
        $this->exportConfig();
        $this->exportBackend();
        $this->exportRequests();
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
     * Export the dashboard assets.
     *
     * @return void
     */
    protected function exportAssets()
    {
        if (! is_dir($directory = base_path('public/src'))) {
            mkdir($directory, 0755, true);
        }

        function recurse_copy($src, $dst)
        {
            $dir = opendir($src);
            @mkdir($dst);
            while (false !== ($file = readdir($dir))) {
                if (($file != '.') && ($file != '..')) {
                    if (is_dir($src . '/' . $file)) {
                        recurse_copy($src . '/' . $file, $dst . '/' . $file);
                    } else {
                        copy($src . '/' . $file, $dst . '/' . $file);
                    }
                }
            }
            closedir($dir);
        }

        recurse_copy(__DIR__ . '/../Stubs/default/src', $directory);
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

    /**
     * Export the dashboard Filters.
     *
     * @return void
     */
    protected function exportFilters()
    {
        $path = 'Filters/UserFilters';
        $this->export(
            app_path($path. '/Search.php'),
            file_get_contents(__DIR__ . '/../Stubs/default/filters/Search.stub'),
            'dashboard.php',
            $path
        );
    }

    /**
     * Export the dashboard Config.
     *
     * @return void
     */
    protected function exportConfig()
    {
        $this->export(
            config_path('/dashboard.php'),
            file_get_contents(__DIR__ . '/../Stubs/default/config/dashboard.stub'),
            'dashboard.php'
        );
    }

    /**
     * Export the dashboard backend.
     *
     * @return void
     */
    protected function exportBackend()
    {
        $path = 'Http/Controllers/Application/Dashboard/Controllers/User';

        $this->export(
            app_path($path. '/UserController.php'),
            $this->compileControllerStub(),
            'UserController.php',
            $path
        );

        if(! $this->option('force')){
            file_put_contents(
                base_path('routes/web.php'),
                file_get_contents(__DIR__ . '/../Stubs/default/routes/routes.stub'),
                FILE_APPEND
            );
        }
    }

    /**
     * Export the dashboard backend.
     *
     * @return void
     */
    protected function exportRequests()
    {
        $path = 'Http/Requests';

        $requests = [
            'CreateUserRequest.stub'  =>'CreateUserRequest.php',
            'UpdateUserRequest.stub'  =>'UpdateUserRequest.php'
        ];

        foreach ($requests as $k => $request) {
            $this->export(
                app_path($path. '/' . $request),
                file_get_contents(__DIR__ . '/../Stubs/default/requests/' . $k),
                $request,
                $path
            );
        }
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

    protected function export(string $file, string $content, string $name, string $path=null)
    {
        if (file_exists($file) && ! $this->option('force')) {
            if ($this->confirm("The [".$name."] file already exists. Do you want to replace it?")) {
                file_put_contents($file, $content);
            }
        } else {
            if(!file_exists($file) && !is_null($path)){
                try {
                    mkdir(app_path($path), 0755, true);
                }catch (\Exception $e){}
            }
            file_put_contents($file, $content);
        }
    }
}
