<?php

namespace Akarumey95\Dashboard\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class GenerateDashboardFilterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:filter
                            {path : Path for filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $path;
    protected $dir;
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
        $this->createFilter();
    }

    /**
     * Export the dashboard backend.
     *
     * @return void
     */
    protected function createFilter()
    {
        $filter = app_path($this->path. '/' . ucfirst($this->name) . '.php');

        if (file_exists($filter)) {
            $this->error('Filter Already Created');
        } else {
            if(!file_exists($filter)){
                try {
                    mkdir(app_path($this->path), 0755, true);
                }catch (\Exception $e){}
            }
            file_put_contents($filter, $this->compileFilterStub());
            $this->info('Filter generated successfully.');
        }
    }

    protected function getParams()
    {
        $path = $this->argument('path');
        $pars = explode('/', $path);

        $this->name = end($pars);
        unset($pars[count($pars)-1]);

        $this->path = implode('/', $pars);
        $this->dir = implode('\\', $pars);
    }

    /**
     * Compiles the "Controller" stub.
     *
     * @return string
     */
    protected function compileFilterStub()
    {
        return str_replace(
            ['{{filter_dir}}', '{{filter_name}}'],
            [ucfirst($this->dir), ucfirst($this->name)],
            file_get_contents(__DIR__ . '/../Stubs/generator/filters/Filter.stub')
        );
    }
}
