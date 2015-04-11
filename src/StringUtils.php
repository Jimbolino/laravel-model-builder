<?php namespace App\Http\Controllers\Model;
/**
 * Created by PhpStorm.
 * User: Jim
 * Date: 4-4-2015
 * Time: 23:13
 */

class StringUtils {

    /**
     * Add single quotes around a string
     * @param $string
     * @return string
     */
    public static function singleQuote($string) {
        if($string === 'null') return $string;
        return "'".$string."'";
    }

    /**
     * Convert a mysql table name to a class name, optionally removing a prefix.
     * @param $table
     * @param bool $removePrefix
     * @return mixed
     */
    public static function prettifyTableName($table, $removePrefix = false) {
        $tableParts = explode('_',$table);

        if($removePrefix) {
            if(strlen($tableParts[0]) < 3) {
                unset($tableParts[0]);
            }
        }

        $table = implode('-',$tableParts);

        return self::dashesToCamelCase($table,true);
    }

    public static function underscoresToCamelCase($string, $capitalizeFirstCharacter = false) {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    // http://stackoverflow.com/a/2792045
    public static function dashesToCamelCase($string, $capitalizeFirstCharacter = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('-', ' ', $string)));
        if (!$capitalizeFirstCharacter) {
            $str[0] = strtolower($str[0]);
        }
        return $str;
    }

    /**
     * Check if a string (haystack) contains one or more words (needles)
     * @param $needles
     * @param $haystack
     * @return bool
     */
    public static function str_contains($needles, $haystack) {
        if(!is_array($needles)) $needles = (array)$needles;

        foreach($needles as $needle) {
            if (strpos($haystack,$needle) !== false) {
                return true;
            }
        }
    }

    /**
     * Add a single quote to all pieces, then implode with the given glue
     * @param $glue
     * @param $pieces
     * @return string
     */
    public static function implodeAndQuote($glue, $pieces) {
        foreach($pieces as &$piece) {
            $piece = self::singleQuote($piece);
        }
        unset($piece);

        return implode($glue,$pieces);
    }

}
