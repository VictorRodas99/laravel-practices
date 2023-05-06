<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Arr;
use Illuminate\Http\Request;

class BaseQuery
{
    const ERROR = "Error";
    const NO_DATA = "Message";

    protected $Model;

    public function __construct(Model $Model)
    {
        $this->Model = $Model;
    }

    public static function paginate($items, $per_page = 5, $page = null, $options = [])
    {
        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        return new LengthAwarePaginator(
            $items->forPage($page, $per_page),
            $items->count(),
            $per_page,
            $options
        );
    }

    public static function get_user_query(Request $request, array $allowed_params): array
    {
        if (count($request->query()) === 0) {
            return [];
        }

        $error_message = [
            BaseQuery::ERROR => "Invalid params where given"
        ];

        $final_query = [];

        if (Arr::isAssoc($allowed_params)) {
            foreach ($allowed_params as $key => $db_compatible) {
                $valid_param = gettype($key) !== "integer"
                    ? $key
                    : $db_compatible;

                $value = $request->query($valid_param);

                if (!isset($value) || $valid_param === "page") {
                    continue;
                }

                $final_query += [
                    $db_compatible => $value
                ];
            }
        } else {
            foreach ($allowed_params as $valid_param) {
                $value = $request->query($valid_param);

                if (!isset($value) || $valid_param === "page") {
                    continue;
                }

                $final_query += [
                    $valid_param => $value
                ];
            }
        }

        return count($final_query) === 0
            ? $error_message
            : $final_query;
    }

    public function get_query_result(array $query): array
    {
        $final_values = [];

        foreach ($query as $param => $value) {
            $db_query_result = $this->Model::where($param, $value)->get();
            array_push($final_values, $db_query_result);
        }

        $raw_values = Arr::flatten($final_values);
        $filtered_values = [];

        if (count($query) > 1) {
            // Implement AND operator (filter elements that does not meet every condition of the query)
            foreach ($raw_values as $db_result) {
                foreach ($query as $param => $value) {
                    $meet_condition = $db_result[$param] == $value;

                    if (!$meet_condition) {
                        $filtered_values = array_filter(
                            $raw_values,
                            fn ($e) => $e !== $db_result
                        );

                        $raw_values = $filtered_values;
                    }
                }
            }
        } else {
            $filtered_values = $raw_values;
        }

        // Get unique values parsing everything into strings and set uniques
        $stringify_values = collect(array_map(fn ($value) => json_encode($value), $filtered_values));
        $stringify_values = $stringify_values->unique()->values()->all();

        $final_values = array_map(fn ($value) => json_decode($value), $stringify_values);

        if (count($final_values) === 0) {
            return [
                BaseQuery::NO_DATA => "No data"
            ];
        }

        return $final_values;
    }
}
