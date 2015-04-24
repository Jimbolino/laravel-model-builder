<?php namespace Jimbolino\Laravel\ModelBuilder;

use Illuminate\Routing\Controller;

/**
 * ModelGenerator5.
 *
 * Laravel 5 version for the ModelGenerator
 *
 * @author Jimbolino
 * @since 02-2015
 *
 */
class ModelGenerator5 extends Controller {

    public function start() {
        // This is the model that all your others will extend
        $baseModel = '\Illuminate\Database\Eloquent\Model'; // default laravel 5

        // This is the path where we will store your new models
        $path = base_path('storage/models'); // laravel 5

        // The namespace of the models
        $namespace = 'App'; // default namespace for clean laravel 5 installation

        $generator = new ModelGenerator($baseModel, $path, $namespace);
        $generator->start();

    }

}
