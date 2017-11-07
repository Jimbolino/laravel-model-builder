<?php

namespace Jimbolino\Laravel\ModelBuilder;

use DB;

/**
 * Class Database, does all queries.
 */
class Database
{
    /**
     * Execute a SHOW TABLES query.
     *
     * @return array with 'tables' and 'views'
     */
    public static function showTables($prefix)
    {
        $results = self::select('SHOW FULL TABLES');
        $tables = [];
        $views = [];
        $first = '';
        foreach ($results as $result) {
            // get the first element (table name)
            foreach ($result as $value) {
                $first = $value;
                break;
            }

            // skip all tables that are not the current prefix
            if (!empty($prefix)) {
                if (!starts_with($first, $prefix)) {
                    continue;
                }
            }

            // separate views from tables
            if ($result->Table_type == 'VIEW') {
                $views[] = $first;
            } else {
                $tables[] = $first;
            }
        }

        return ['tables' => $tables, 'views' => $views];
    }

    /**
     * Execute a describe table query.
     *
     * @param $table
     *
     * @return mixed
     */
    public static function describeTable($table)
    {
        $result = self::select("SHOW FULL COLUMNS FROM `{$table}`");
        $result = ArrayHelpers::indexArrayByValue($result, 'Field');

        return $result;
    }

    /**
     * Return a sql result with all foreign keys (data from information_scheme).
     *
     * @return mixed
     */
    public static function getAllForeignKeys()
    {
        $sql = 'SELECT * FROM information_schema.KEY_COLUMN_USAGE ';
        $sql .= 'WHERE REFERENCED_COLUMN_NAME IS NOT NULL AND REFERENCED_TABLE_SCHEMA = DATABASE()';
        $results = self::select($sql);

        return $results;
    }

    /**
     * Get the table prefix.
     *
     * @return mixed
     */
    public static function getTablePrefix()
    {
        return DB::getTablePrefix();
    }

    public static function select($query)
    {
        return DB::select($query);
    }
}
