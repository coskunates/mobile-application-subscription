<?php

namespace App\Helpers;

/**
 * Class ArrayHelper
 * @package App\Helpers
 */
class ArrayHelper
{
    /**
     * @param array $array
     * @param string $key
     * @return array
     */
    public static function valueToKey(array $array, string $key): array
    {
        $result = [];

        foreach ($array as $value) {
            $result[$value[$key]] = $value;
        }

        return $result;
    }
}
