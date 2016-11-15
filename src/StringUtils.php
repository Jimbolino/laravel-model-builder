<?php

namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class StringUtils, some handy string functions.
 */
abstract class StringUtils
{
    /**
     * Add single quotes around a string.
     *
     * @param $string
     *
     * @return string
     */
    public static function singleQuote($string)
    {
        if ($string === 'null') {
            return $string;
        }

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

    /**
     * Add a single quote to all pieces, then implode with the given glue.
     *
     * @param $glue
     * @param $pieces
     *
     * @return string
     */
    public static function implodeAndQuote($glue, $pieces)
    {
        foreach ($pieces as &$piece) {
            $piece = self::singleQuote($piece);
        }
        unset($piece);

        return implode($glue, $pieces);
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
