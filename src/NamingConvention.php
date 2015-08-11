<?php

namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class NamingConvention
 * based on http://laravel.com/docs/5.0/eloquent#basic-usage.
 */
class NamingConvention
{
    /**
     * @param $name
     *
     * @return bool
     */
    public static function primaryKey($name)
    {
        if ($name === 'id') {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @param $table
     * @param $foreignKey
     *
     * @return bool
     */
    public static function foreignKey($name, $table, $foreignKey)
    {
        if ($name === $table.'_'.$foreignKey) {
            return true;
        }

        return false;
    }

    /**
     * @param $name
     * @param $tables
     *
     * @return bool
     */
    public static function junctionTable($name, $tables)
    {
        sort($tables);
        $tables = implode('_', $tables);
        if ($name === $tables) {
            return true;
        }

        return false;
    }
}
