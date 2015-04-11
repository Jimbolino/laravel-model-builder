<?php namespace App\Http\Controllers\Model;

use Illuminate\Support\Facades\DB;
use ReflectionClass;
use Exception;

/**
 * ModelGenerator5.
 *
 * Laravel 5 version for the ModelGenerator
 *
 * @author Jimbolino
 * @since 02-2015
 *
 */
class ModelGenerator5 extends ModelGenerator {

    public function __construct() {
        parent::__construct();

        // This is the model that all your others will extend
        $this->baseModel = 'Illuminate\Database\Eloquent\Model'; // default laravel 5

        // This is the path where we will store your new models
        $this->path = '../storage/models'; // laravel 5

    }

}
