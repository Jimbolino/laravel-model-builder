<?php namespace App\Http\Controllers\Model;

use App\Http\Controllers\Controller;
use DB;
use ReflectionClass;

/**
 * ModelGenerator.
 *
 * This is a basic class that analyzes your current database with SHOW TABLES and DESCRIBE.
 * The result will be written in Laravel Model files.
 * Warning: all files are overwritten, so do not let this one write in your current model directory.
 *
 * @author Jimbolino
 * @since 02-2015
 *
 */
class ModelGenerator extends Controller {

    protected $foreignKeys = array();

    protected $junctionTables = array();

    protected $tables = array();

    protected $views = array();

    /**
     * There MUST NOT be a hard limit on line length; the soft limit MUST be 120 characters;
     * https://github.com/php-fig/fig-standards/blob/master/accepted/PSR-2-coding-style-guide.md#1-overview
     * @var int
     */
    public static $lineWrap = 120;


    public function __construct() {

        if (!defined('TAB')) define('TAB', "    "); // Code MUST use 4 spaces for indenting, not tabs.
        if (!defined('LF')) define('LF', "\n");
        if (!defined('CR')) define('CR', "\r");

        // This is the model that all your others will extend
        $this->baseModel = 'Model'; // should be overwritten later

        // This is the path where we will store your new models
        $this->path = '../app/storage/models'; // should be overwritten later
    }

    public function start() {
        echo '<pre>';
        $tablesAndViews = $this->showTables();
        $this->tables = $tablesAndViews['tables'];
        $this->views = $tablesAndViews['views'];

        $this->foreignKeys['all'] = $this->getAllForeignKeys();
        $this->foreignKeys['ordered'] = $this->getAllForeignKeysOrderedByTable();

        foreach($this->tables as $key => $table) {
            $this->describes[$table] = $this->describeTable($table);

            if($this->isManyToMany($table, true)) {
                $this->junctionTables[] = $table;
                unset($this->tables[$key]);
            }
        }
        unset($table);


        foreach($this->tables as $table) {
            $model = new Model();
            $model->buildModel($table, $this->baseModel, $this->describes, $this->foreignKeys);

            $model->createModel();

            $result = $this->writeFile($table, $model);

            echo 'file written: '.$result['filename'].' - '.$result['result'] . ' bytes'.LF;
        }
        echo 'done';
    }

    protected function isManyToMany($table, $checkForeignKey = true) {
        $describe = $this->describeTable($table);

        $count = 0;
        foreach($describe as $field) {
            if(count($describe) < 3) {
                $type = $this->parseType($field->Type);
                if($type['type']=='int' && $field->Key == 'PRI') {
                    // should be a foreign key
                    if($checkForeignKey && $this->isForeignKey($table, $field->Field)) {
                        $count++;
                    }
                    if(!$checkForeignKey) {
                        $count++;
                    }
                }
            }
        }
        if($count == 2) return true;
    }

    protected function writeFile($table, $model) {
        $filename = StringUtils::prettifyTableName($table).'.php';

        $path = realpath($_SERVER['DOCUMENT_ROOT']).'/'.$this->path.'/';
        if(!is_dir($path)) {
            $oldumask = umask(0);
            echo 'creating path: '.$path.LF;
            mkdir($path, 0777, true);
            umask($oldumask);
            if(!is_dir($path)) throw new Exception('dir '. $path .' could not be created');
        }
        $result = file_put_contents($path.$filename, $model);
        return array('filename' => $path.$filename, 'result' => $result);
    }




    /**
     * Parse int(10) unsigned to something useful
     * @param string $type
     */
    protected function parseType($type) {
        $result = array();

        // get unsigned
        $result['unsigned'] = false;
        $type = explode(' ',$type);

        if(isset($type[1]) && $type[1] === 'unsigned') {
            $result['unsigned'] = true;
        }

        // int(11) + varchar(255) = $type = varchar, $size = 255
        $type = explode('(', $type[0]);
        $result['type'] = $type[0];
        if(isset($type[1])) {
            $result['size'] = intval($type[1]);
        }

        return $result;
    }

    /**
     * Execute a SHOW TABLES query
     * @return array with 'tables' and 'views'
     */
    protected function showTables() {
        $results = DB::select('SHOW FULL TABLES');
        $tables = array();
        $views = array();
        foreach($results as $result) {
            foreach($result as $value) {
                $first = $value;
                break;
            }
            if($result->Table_type == 'VIEW') {
                $views[] = $first;
            }
            else {
                $tables[] = $first;
            }
        }
        return array('tables' => $tables, 'views' => $views);
    }


    /**
     * Execute a describe table query
     * @param $table
     * @return mixed
     */
    protected function describeTable($table) {
        $result = DB::select('SHOW FULL COLUMNS FROM '.$table);
        $result = $this->indexArrayByValue($result, 'Field');
        return $result;
    }

    protected function indexArrayByValue($input, $value) {
        $output = array();
        foreach($input as $row) {
            $output[$row->$value] = $row;
        }
        return $output;
    }

    protected function orderArrayByValue($input, $value) {
        $output = array();
        foreach($input as $row) {
            $output[$row->$value][] = $row;
        }
        return $output;
    }

    /**
     * Return a sql result with all foreign keys (data from information_scheme)
     * @return mixed
     */
    protected function getAllForeignKeys() {
        $sql = 'SELECT * FROM information_schema.KEY_COLUMN_USAGE ';
        $sql .= 'WHERE REFERENCED_COLUMN_NAME IS NOT NULL AND REFERENCED_TABLE_SCHEMA = DATABASE()';
        $results = DB::select($sql);
        return $results;
    }

    /**
     * Return an array with tables, with arrays of foreign keys
     * @return array|mixed
     */
    protected function getAllForeignKeysOrderedByTable() {
        $results = $this->getAllForeignKeys();
        $results = $this->orderArrayByValue($results, 'TABLE_NAME');
        return $results;
    }

    /**
     * Check if a given field in a table is a foreign key
     * @param $table
     * @param $field
     * @return bool
     */
    protected function isForeignKey($table, $field) {
        foreach($this->foreignKeys['all'] as $entry) {
            if($entry->COLUMN_NAME == $field && $entry->TABLE_NAME == $table) return true;
        }
    }




}
