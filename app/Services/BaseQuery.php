<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use App\Utils\{QueryProcessor, DataProcessor};

class BaseQuery
{
    const ERROR = 'error';
    const NO_DATA = 'message';

    protected $Model;
    protected $independent_params;

    public function __construct(Model $Model, array $independent_params = [])
    {
        $this->Model = $Model;
        $this->independent_params = $independent_params;
    }

    public function paginate(Iterable $items, $per_page = 5, $page = null, $options = [])
    {
        $items = json_decode(json_encode($items), true); // Transform to array to convert from stdClass type

        $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
        $items = $items instanceof Collection ? $items : Collection::make($items);

        $items = $items->mapInto($this->Model::class);

        return new LengthAwarePaginator(
            $items->forPage($page, $per_page),
            $items->count(),
            $per_page,
            $options
        );
    }

    public function get_user_query(Request $request, array $allowed_params): array
    {
        if (count($request->query()) === 0) {
            return [];
        }

        $final_query = QueryProcessor::get_db_compatible_params($request, $allowed_params);
        $expected_params = QueryProcessor::get_valid_query_params($allowed_params);

        $error_message = [
            BaseQuery::ERROR => 'Invalid params where given',
            'expected params' => $expected_params
        ];

        $exists_independent_params = QueryProcessor::exists_independent_params(
            array_keys($request->query()),
            $this->independent_params
        );

        return count($final_query) === 0 && !$exists_independent_params
            ? $error_message
            : $final_query;
    }

    public function get_query_result(array $query): Collection
    {
        $raw_values = DataProcessor::get_data_by(
            $query,
            $this->Model,
            $this->independent_params
        );

        $filtered_values = DataProcessor::filter_by_conditions($query, $raw_values);
        $final_values = DataProcessor::get_unique_values($filtered_values);

        if (count($final_values) === 0) {
            return collect([
                BaseQuery::NO_DATA => "No data"
            ]);
        }

        return collect($final_values);
    }
}
