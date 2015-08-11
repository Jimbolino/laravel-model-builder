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
}
