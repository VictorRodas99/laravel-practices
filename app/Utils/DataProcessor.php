<?php

namespace App\Utils;

use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Model;


class DataProcessor
{
    /**
     * @return array<Model>
     */
    public static function get_data_by(array $query, Model $Model, array $independent_params): array
    {
        $results = [];

        foreach ($query as $param => $value) {
            if (in_array($param, $independent_params)) {
                continue;
            }

            $db_query_result = $Model::where($param, $value)->get();
            $results[] = $db_query_result;
        }

        return Arr::flatten($results);
    }

    /**
     * Get unique values parsing everything into strings and set uniques
     */
    public static function get_unique_values(array $initial_values)
    {
        $stringify_values = collect(array_map(fn ($value) => json_encode($value), $initial_values));
        $unique_values = $stringify_values->unique()->values()->all();

        return array_map(fn ($value) => json_decode($value), $unique_values);
    }

    /**
     * Filter elements that does not meet every condition of the query
     */
    public static function filter_by_conditions(array $query, array $data)
    {
        if (count($query) <= 1) {
            return $data;
        }

        // Implement AND operator 
        $filtered_values = [];

        foreach ($data as $db_result) {
            foreach ($query as $param => $value) {
                $meet_condition = $db_result[$param] == $value;

                if (!$meet_condition) {
                    $filtered_values = array_filter(
                        $data,
                        fn ($e) => $e !== $db_result
                    );

                    $data = $filtered_values;
                }
            }
        }

        return $filtered_values;
    }
}
