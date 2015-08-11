<?php

namespace Jimbolino\Laravel\ModelBuilder;

use Illuminate\Routing\Controller;

/**
 * Class ModelGenerator4, Laravel 4 version for the ModelGenerator.
 */
class ModelGenerator4 extends Controller
{
    public function start()
    {
        // This is the model that all your others will extend
        $baseModel = 'Eloquent'; // default laravel 4.2

        // This is the path where we will store your new models
        $path = storage_path('models');

        // The namespace of the models
        $namespace = ''; // by default laravel 4 doesn't use namespaces

        // get the prefix from the config
        $prefix = Database::getTablePrefix();

        $generator = new ModelGenerator($baseModel, $path, $namespace, $prefix);
        $generator->start();
    }
}
