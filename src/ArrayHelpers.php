<?php

namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class ArrayHelpers, a few handy array helper functions.
 */
abstract class ArrayHelpers
{
    /**
     * Reindex an existing array by a value from the array.
     *
     * @param $input
     * @param $value
     *
     * @return array
     */
    public static function indexArrayByValue($input, $value)
    {
        $output = [];
        foreach ($input as $row) {
            $output[$row->$value] = $row;
        }

        return $output;
    }

    /**
     * @param $input
     * @param $value
     *
     * @return array
     */
    public static function orderArrayByValue($input, $value)
    {
        $output = [];
        foreach ($input as $row) {
            $output[$row->$value][] = $row;
        }

        return $output;
    }

    /**
     * @see https://stackoverflow.com/a/4254008/3432720
     * @param array $array
     * @return bool
     */
    public static function hasStringKeys(array $array)
    {
        return count(array_filter(array_keys($array), 'is_string')) > 0;
    }
}
