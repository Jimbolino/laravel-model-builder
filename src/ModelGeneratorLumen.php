<?php
/**
 * Created by PhpStorm.
 * User: jim
 * Date: 20-8-18
 * Time: 12:36
 */

namespace Jimbolino\Laravel\ModelBuilder;

class ModelGeneratorLumen
{
    public function start()
    {
        // This is the model that all your others will extend
        $baseModel = '\Illuminate\Database\Eloquent\Model';

        // This is the path where we will store your new models
        $path = storage_path('models');

        // The namespace of the models
        $namespace = 'App';

        // get the prefix from the config
        $prefix = app('db')->getTablePrefix();

        $generator = new ModelGenerator($baseModel, $path, $namespace, $prefix);
        $generator->start();
    }
}
