<?php

namespace Jimbolino\Laravel\ModelBuilder;

use Illuminate\Routing\Controller;

/**
 * Class ModelGenerator5, Laravel 5 version for the ModelGenerator.
 */
class ModelGenerator5 extends Controller
{
    public function start()
    {
        // This is the model that all your others will extend
        $baseModel = 'Model'; // default laravel 5

        // This is the path where we will store your new models
        $path = storage_path('Models');

        // The namespace of the models
        $namespace = 'App\Models'; // default namespace for clean laravel 5 installation

        // get the prefix from the config
        $prefix = Database::getTablePrefix();

        $generator = new ModelGenerator($baseModel, $path, $namespace, $prefix);
        $generator->start();
    }
}
