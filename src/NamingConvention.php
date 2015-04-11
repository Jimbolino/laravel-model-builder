<?php namespace App\Http\Controllers\Model;
/**
 * Class NamingConvention
 * based on http://laravel.com/docs/5.0/eloquent#basic-usage
 * @package App\Http\Controllers\Model
 */
class NamingConvention {

    /**
     * @param $name
     * @return bool
     */
    public static function primaryKey($name) {
        if($name === 'id') return true;
    }

    /**
     * @param $name
     * @param $table
     * @param $foreignKey
     * @return bool
     */
    public static function foreignKey($name, $table, $foreignKey) {
        if($name === $table.'_'.$foreignKey) return true;
    }

    /**
     * @param $name
     * @param $tables
     * @return bool
     */
    public static function junctionTable($name, $tables) {
        sort($tables);
        $tables = implode('_',$tables);
        if($name === $tables) return true;
    }
}
