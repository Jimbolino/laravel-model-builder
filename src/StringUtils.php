<?php

namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class StringUtils, some handy string functions.
 */
abstract class StringUtils
{
    /**
     * @param $data
     * @param string $indent
     * @return mixed|string
     */
    public static function export($data, $indent = '')
    {
        if ($data === false) {
            return 'false';
        }
        if ($data === true) {
            return 'true';
        }
        if ($data === null) {
            return 'null';
        }
        if (is_string($data)) {
            return self::singleQuote($data);
        }
        if (is_array($data)) {
            if (ArrayHelpers::hasStringKeys($data)) {
                return '['.LF.self::exportAssocArray($data, $indent.$indent).$indent.']';
            } else {
                return '['.self::exportIndexedArray($data, ', ').']';
            }
        }
    }

    /**
     * Add single quotes around a string.
     *
     * @param $string
     *
     * @return string
     */
    public static function singleQuote($string)
    {
        return "'".$string."'";
    }

    /**
     * Convert a mysql table name to a class name, optionally removing a prefix.
     *
     * @param $table
     * @param string $prefix
     *
     * @return mixed
     */
    public static function prettifyTableName($table, $prefix = '')
    {
        if ($prefix) {
            $table = self::removePrefix($table, $prefix);
        }

        return self::underscoresToCamelCase($table, true);
    }

    /**
     * Convert underscores to CamelCase
     * borrowed from http://stackoverflow.com/a/2792045.
     *
     * @param $string
     * @param bool $capitalizeFirstChar
     *
     * @return mixed
     */
    public static function underscoresToCamelCase($string, $capitalizeFirstChar = false)
    {
        $str = str_replace(' ', '', ucwords(str_replace('_', ' ', $string)));
        if (!empty($str) && !$capitalizeFirstChar) {
            $str[0] = strtolower($str[0]);
        }

        return $str;
    }

    /**
     * Check if a string (haystack) contains one or more words (needles).
     *
     * @param $needles
     * @param $haystack
     *
     * @return bool
     */
    public static function strContains($needles, $haystack)
    {
        if (!is_array($needles)) {
            $needles = (array) $needles;
        }

        foreach ($needles as $needle) {
            if (strpos($haystack, $needle) !== false) {
                return true;
            }
        }

        return false;
    }

    public static function exportAssocArray(array $data, $indent)
    {
        foreach ($data as $key => &$value) {
            $value = $indent.self::singleQuote($key) .' => '.self::singleQuote($value).','.LF;
        }
        unset($value);
        return implode($data);
    }

    /**
     * Add a single quote to all pieces, then implode with the given glue.
     *
     * @param $data
     * @param $glue
     *
     * @return string
     */
    public static function exportIndexedArray(array $data, $glue = ', ')
    {
        foreach ($data as &$piece) {
            $piece = self::singleQuote($piece);
        }
        unset($piece);

        return implode($glue, $data);
    }

    /**
     * Remove a prefix from a table name.
     *
     * @param $table
     * @param $prefix
     *
     * @return string
     */
    public static function removePrefix($table, $prefix)
    {
        return substr($table, strlen($prefix));
    }

    /**
     * Use laravel pluralization, and if that one fails give a warning and just append "s".
     *
     * @param $value
     *
     * @return string
     */
    public static function safePlural($value)
    {
        $plural = str_plural($value);
        if ($plural == $value) {
            $plural = $value.'s';
            echo 'warning: automatic pluralization of '.$value.' failed, using '.$plural.LF;
        }

        return $plural;
    }
}
