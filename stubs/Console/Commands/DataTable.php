<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;

class DataTable extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'datatable:generate {model}
    {--m|migration} {--c|controller} {--N|no_model}
    {--r|resource} {--R|model_resource} {--C|model_collection} {--Q|request}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $model = $this->argument('model');
        $migration = $this->option('migration');
        $controller = $this->option('controller');
        $resource = $this->option('resource');
        $model_resource = $this->option('model_resource');
        $model_collection = $this->option('model_collection');
        $request = $this->option('request');
        $no_model = $this->option('no_model');
//        $this->call('make:model '.' -mcr');
        if ($model) {
            $options = "";
            if ($migration) {
                $options .= 'm';
            }
            if ($controller) {
                $options .= 'c';
            }
            if ($resource) {
                $options .= 'r';
            }
            $_option = $options ? " -" . $options : "";

            if (!$no_model) {
                Artisan::call("make:model {$model}" . $_option);
            } else {
                if ($controller) {
                    $_controller = "{$model}Controller";
                    $_resource = $resource ? " -r" : "";
                    Artisan::call("make:controller {$_controller}" . $_resource);
                }
            }

            if ($model_resource) {
                $resource = "{$model}Resource";
                Artisan::call("make:resource {$resource}");
            }
            if ($model_collection) {
                $collection = "{$model}Collection";
                Artisan::call("make:resource {$collection} -c");
            }
            if ($request) {
                $request_c = "{$model}StoreRequest";
                $request_u = "{$model}UpdateRequest";
                Artisan::call("make:request {$request_c}");
                Artisan::call("make:request {$request_u}");
            }
        }
        return Command::SUCCESS;
    }
}
