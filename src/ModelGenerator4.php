<?php namespace App\Http\Controllers\Model;

/**
 * ModelGenerator4.
 *
 * Laravel4 version for the ModelGenerator
 *
 * @author Jimbolino
 * @since 02-2015
 *
 */
class ModelGenerator4 extends ModelGenerator {

    public function __construct() {
        parent::__construct();

        // This is the model that all your others will extend
        $this->baseModel = 'BaseModel'; // custom
//        $this->baseModel = 'Model'; // default laravel 4

        // This is the path where we will store your new models
        $this->path = '../app/storage/models'; // laravel 4

    }

}
