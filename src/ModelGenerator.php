<?php

namespace Jimbolino\Laravel\ModelBuilder;

use Exception;

/**
 * Class ModelGenerator.
 *
 * This is a basic class that analyzes your current database with SHOW TABLES and DESCRIBE.
 * The result will be written in Laravel Model files.
 * Warning: all files are overwritten, so do not let this one write in your current model directory.
 *
 * @author Jimbolino
 *
 * @since 02-2015
 */
class ModelGenerator
{
    protected $foreignKeys = [];

    protected $junctionTables = [];

    protected $tables = [];

    protected $views = [];

    protected $describes = [];

    /**
     * There MUST NOT be a hard limit on line length; the soft limit MUST be 120 characters;
     * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md#1-overview.
     *
     * @var int
     */
    public static $lineWrap = 120;

    protected $prefix = '';

    protected $namespace = 'app';

    protected $path = '';

    protected $baseModel = 'Eloquent';

    /**
     * @param string $baseModel (the model that all your others will extend)
     * @param string $path      (the path where we will store your new models)
     * @param string $namespace (the namespace of the models)
     * @param string $prefix    (the configured table prefix)
     */
    public function __construct($baseModel = '', $path = '', $namespace = '', $prefix = '')
    {
        if (!defined('TAB')) {
            define('TAB', '    '); // Code MUST use 4 spaces for indenting, not tabs.
        }
        if (!defined('LF')) {
            define('LF', "\n");
        }
        if (!defined('CR')) {
            define('CR', "\r");
        }
        if (PHP_SAPI == 'cli') {
            define('BR', "\n");
        } else {
            define('BR', '<br />');
        }

        $this->baseModel = $baseModel;
        $this->path = $path;
        $this->namespace = $namespace;
        $this->prefix = $prefix;
    }

    /**
     * This is where we start.
     *
     * @throws Exception
     */
    public function start()
    {
        $tablesAndViews = Database::showTables($this->prefix);
        $this->tables = $tablesAndViews['tables'];
        $this->views = $tablesAndViews['views'];

        $this->foreignKeys['all'] = Database::getAllForeignKeys();
        $this->foreignKeys['ordered'] = $this->getAllForeignKeysOrderedByTable();

        foreach ($this->tables as $key => $table) {
            $this->describes[$table] = Database::describeTable($table);

            if ($this->isManyToMany($table, true)) {
                $this->junctionTables[] = $table;
                unset($this->tables[$key]);
            }
        }
        unset($table);

        foreach ($this->tables as $table) {
            $model = new Model();
            $model->buildModel(
                $table,
                $this->baseModel,
                $this->describes,
                $this->foreignKeys,
                $this->namespace,
                $this->prefix
            );

            $model->createModel();

            $result = $this->writeFile($table, $model);

            echo 'file written: '.$result['filename'].' - '.$result['result'].' bytes'.BR;
            flush();
        }
        echo 'done'.BR;
    }

    /**
     * Detect many to many tables.
     *
     * @param $table
     * @param bool $checkForeignKey
     *
     * @return bool
     */
    protected function isManyToMany($table, $checkForeignKey = true)
    {
        $describe = Database::describeTable($table);

        $count = 0;
        foreach ($describe as $field) {
            if (count($describe) < 3) {
                $type = $this->parseType($field->Type);
                if ($type['type'] == 'int' && $field->Key == 'PRI') {
                    // should be a foreign key
                    if ($checkForeignKey && $this->isForeignKey($table, $field->Field)) {
                        $count++;
                    }
                    if (!$checkForeignKey) {
                        $count++;
                    }
                }
            }
        }
        if ($count == 2) {
            return true;
        }

        return false;
    }

    /**
     * Write the actual TableName.php file.
     *
     * @param $table
     * @param Model $model
     *
     * @throws Exception
     *
     * @return array
     */
    protected function writeFile($table, $model)
    {
        $filename = StringUtils::prettifyTableName($table, $this->prefix).'.php';

        if (!is_dir($this->path)) {
            $oldUMask = umask(0);
            echo 'creating path: '.$this->path.LF;
            mkdir($this->path, 0777, true);
            umask($oldUMask);
            if (!is_dir($this->path)) {
                throw new Exception('dir '.$this->path.' could not be created');
            }
        }
        $result = file_put_contents($this->path.'/'.$filename, $model);

        return ['filename' => $this->path.'/'.$filename, 'result' => $result];
    }

    /**
     * Parse int(10) unsigned to something useful.
     *
     * @param string $type
     *
     * @return array
     */
    protected function parseType($type)
    {
        $result = [];

        // get unsigned
        $result['unsigned'] = false;
        $type = explode(' ', $type);

        if (isset($type[1]) && $type[1] === 'unsigned') {
            $result['unsigned'] = true;
        }

        // int(11) + varchar(255) = $type = varchar, $size = 255
        $type = explode('(', $type[0]);
        $result['type'] = $type[0];
        if (isset($type[1])) {
            $result['size'] = intval($type[1]);
        }

        return $result;
    }

    /**
     * Return an array with tables, with arrays of foreign keys.
     *
     * @return array|mixed
     */
    protected function getAllForeignKeysOrderedByTable()
    {
        $results = Database::getAllForeignKeys();
        $results = ArrayHelpers::orderArrayByValue($results, 'TABLE_NAME');

        return $results;
    }

    /**
     * Check if a given field in a table is a foreign key.
     *
     * @param $table
     * @param $field
     *
     * @return bool
     */
    protected function isForeignKey($table, $field)
    {
        foreach ($this->foreignKeys['all'] as $entry) {
            if ($entry->COLUMN_NAME == $field && $entry->TABLE_NAME == $table) {
                return true;
            }
        }

        return false;
    }

    /**
     * Check if the given name starts with the current prefix.
     *
     * @param $name
     *
     * @return bool
     */
    protected function isPrefix($name)
    {
        if (empty($this->prefix)) {
            return true;
        }

        return starts_with($name, $this->prefix);
    }
}
