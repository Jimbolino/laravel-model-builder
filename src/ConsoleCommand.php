<?php

namespace Jimbolino\Laravel\ModelBuilder;

use Illuminate\Console\Command;

/**
 * Class ConsoleCommand, Laravel 5 version for the ModelGenerator.
 */
class ConsoleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'build-models {--namespace=App : The Base namespace for the Models } {--output= : Where the Models will be saved }';
    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Eloquent Models and Base Models';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        // This is the model that all your others will extend
        $baseModel = '\Illuminate\Database\Eloquent\Model'; // default laravel 5

        // This is the path where we will store your new models
        $path = storage_path('models');

        if ($this->option('output')) {
            $path = $this->option('output');
        }

        // The namespace of the models
        $namespace = 'App'; // default namespace for clean laravel 5 installation

        if ($this->option('namespace')) {
            $namespace = $this->option('namespace');
        }

        // get the prefix from the config
        $prefix = Database::getTablePrefix();

        $generator = new ModelGenerator($baseModel, $path, $namespace, $prefix);
        $generator->start();
    }
}
