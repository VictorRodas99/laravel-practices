<?php

namespace App\Utils;

use Illuminate\Http\Request;

class QueryProcessor
{
    public static function exists_independent_params(array $queries, array $expected_params): bool
    {
        $exists_independent_params = false;

        foreach ($expected_params as $independent_param) {
            if (in_array($independent_param, $queries)) {
                $exists_independent_params = true;
                break;
            }
        }

        return $exists_independent_params;
    }

    public static function get_valid_query_params(array $allowed_params)
    {
        $expected_params = [];

        foreach ($allowed_params as $key => $value) {
            $is_indexed_array = gettype($key) === 'integer';
            $valid_param = !$is_indexed_array ? $key : $value;

            if ($valid_param === 'page') {
                continue;
            }

            $expected_params[] = $valid_param;
        }

        return $expected_params;
    }


    /**
     * @return array<string, string>
     */
    public static function get_db_compatible_params(Request $request, array $allowed_params)
    {
        $valid_queries = [];
        $expected_params = [];

        foreach ($allowed_params as $key => $value) {
            $is_indexed_array = gettype($key) === 'integer';
            $query_value = null;

            $valid_param = !$is_indexed_array ? $key : $value;

            if ($valid_param !== 'page') {
                $expected_params[] = $valid_param;
                $query_value = $request->query($valid_param);
            }

            if (isset($query_value)) {
                $valid_queries += [
                    $value => $query_value // $value is db compatible
                ];
            }
        }

        return $valid_queries;
    }
}
