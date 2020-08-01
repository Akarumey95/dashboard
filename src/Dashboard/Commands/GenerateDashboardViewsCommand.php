<?php

namespace Akarumey95\Dashboard\Commands;

use Illuminate\Console\Command;

class GenerateDashboardViewsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:view
                           {model : Model name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * The views that need to be exported.
     *
     * @var array
     */
    protected $views = [
        'views/index.stub'  => 'index.blade.php',
        'views/show.stub'   => 'show.blade.php',
        'views/form.stub'   => 'form.blade.php',
    ];

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $this->createViews();
    }

    /**
     * Export the dashboard backend.
     *
     * @return void
     */
    protected function createViews()
    {
        $name = $this->argument('model');

        if (! is_dir($directory = $this->getViewPath('dashboard/views/' . strtolower($name)))) {
            mkdir($directory, 0755, true);
        }

        foreach ($this->views as $key => $value) {
            if (file_exists($view = $this->getViewPath('dashboard/views/' . strtolower($name) . '/' . $value))) {
                $this->error('Views Already Created');
            }

            copy(
                __DIR__ . '/../Stubs/generator/' .$key,
                $view
            );
        }

        $this->info('Views generated successfully.');
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
