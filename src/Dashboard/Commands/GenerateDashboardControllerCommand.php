<?php

namespace Akarumey95\Dashboard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateDashboardControllerCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:controller
                            {model : Path to model}
                            {--view : Generate with templates}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $model;
    protected $name;

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
        $this->getParams();
        $this->createController();

        if($this->option('view')){
            Artisan::call('dashboard:view ' . $this->name);
            $this->info('Views generated successfully.');
        }
    }

    /**
     * Export the dashboard backend.
     *
     * @return void
     */
    protected function createController()
    {
        $path = 'Http/Controllers/Application/Dashboard/Controllers/' . ucfirst($this->name);
        $controller = app_path($path. '/' . ucfirst($this->name) .'Controller.php');

        if (file_exists($controller)) {
            $this->error('Controller Already Created');
        } else {
            if(!file_exists($controller)){
                mkdir(app_path($path), 0755, true);
            }
            file_put_contents($controller, $this->compileControllerStub());
            $this->updateConfig();
            $this->info('Controller generated successfully.');
        }
    }

    protected function getParams()
    {
        $this->model = $this->argument('model');
        $pars = explode('/', $this->model);
        $this->name = end($pars);
    }

    /**
     * Compiles the "Controller" stub.
     *
     * @return string
     */
    protected function compileControllerStub()
    {
        return str_replace(
            ['{{namespace}}', '{{controller_name}}', '{{config_name}}'],
            [$this->laravel->getNamespace(), ucfirst($this->name), strtolower($this->name)],
            file_get_contents(__DIR__ . '/../Stubs/generator/controllers/Controller.stub')
        );
    }


    protected function updateConfig()
    {
        $config = config('dashboard');
        $config['controllers'][strtolower($this->name)] = [
            "view" => "dashboard.views." . strtolower($this->name),
            "homePage" => "/" . strtolower($this->name) ."s",
            "modelName" => ucfirst($this->name),
            "model" => "App\\" . str_replace('/', '\\', $this->model),
            "controller" => ucfirst($this->name) . "\\" . ucfirst($this->name) . "Controller",
            "paginateBy" => 25,
            "router" => [
                "index",
                "store",
                "show",
                "update",
                "destroy",
            ],
            "filters" => [

            ],
            'sorts' => [

            ],
        ];

        $config['menu'][0]['submenu'][] = [
            'title' => ucfirst($this->name) . 's',
            'url'   => "/" . strtolower($this->name) ."s",
        ];

        file_put_contents(
            config_path('dashboard.php'),
            str_replace(
                ['<pre>', '</pre>', 'array (', ')'], ['', '', '[', ']'],
                '<pre><?php' . PHP_EOL . PHP_EOL . 'return ' . var_export($config, 1) . ';?></pre>'
            )
        );
    }
}
