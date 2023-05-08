<?php

namespace App\Utils;

use ErrorException;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;

class ArrayHelper
{
    public static function get_values_by_key(array $array, string $key): array
    {
        if (count($array) === 0) {
            return [];
        }

        if (!Arr::isAssoc($array[0])) {
            throw new ErrorException("Given array is not associative!");
        }

        $values_of_given_key = array_map(fn ($e) => [$key => $e[$key]], $array);

        return $values_of_given_key;
    }

    public static function merge_assoc_arrays($convert_to_collection = false, array ...$arrays): array | collection
    {
        $result = [];

        foreach ($arrays as $array) {
            foreach ($array as $key => $value) {
                $result[$key] = $value;
            }
        }

        return $convert_to_collection
            ? collect($result)
            : $result;
    }

    public static function array_some(array $array, callable $fn): bool
    {
        foreach ($array as $element) {
            if ($fn($element)) {
                return true;
            }
        }

        return false;
    }

    public static function array_every(array $array, callable $fn): bool
    {
        foreach ($array as $element) {
            if (!$fn($element)) {
                return false;
            }
        }

        return true;
    }
}
