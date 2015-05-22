<?php namespace Jimbolino\Laravel\ModelBuilder;

/**
 * Class ArrayHelpers, a few handy array helper functions.
 * @package Jimbolino\Laravel\ModelBuilder
 */
abstract class ArrayHelpers
{
    /**
     * Reindex an existing array by a value from the array
     * @param $input
     * @param $value
     * @return array
     */
    public static function indexArrayByValue($input, $value)
    {
        $output = array();
        foreach ($input as $row) {
            $output[$row->$value] = $row;
        }
        return $output;
    }

    /**
     * @param $input
     * @param $value
     * @return array
     */
    public static function orderArrayByValue($input, $value)
    {
        $output = array();
        foreach ($input as $row) {
            $output[$row->$value][] = $row;
        }
        return $output;
    }
}
