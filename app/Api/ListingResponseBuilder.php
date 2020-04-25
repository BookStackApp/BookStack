<?php namespace BookStack\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class ListingResponseBuilder
{

    protected $query;
    protected $request;
    protected $fields;

    protected $filterOperators = [
        'eq'   => '=',
        'ne'   => '!=',
        'gt'   => '>',
        'lt'   => '<',
        'gte'  => '>=',
        'lte'  => '<=',
        'like' => 'like'
    ];

    /**
     * ListingResponseBuilder constructor.
     */
    public function __construct(Builder $query, Request $request, array $fields)
    {
        $this->query = $query;
        $this->request = $request;
        $this->fields = $fields;
    }

    /**
     * Get the response from this builder.
     */
    public function toResponse()
    {
        $this->applyFiltering($this->query);

        $total = $this->query->count();
        $data = $this->fetchData();

        return response()->json([
            'data' => $data,
            'total' => $total,
        ]);
    }

    /**
     * Fetch the data to return in the response.
     */
    protected function fetchData(): Collection
    {
        $this->applyCountAndOffset($this->query);
        $this->applySorting($this->query);

        return $this->query->get($this->fields);
    }

    /**
     * Apply any filtering operations found in the request.
     */
    protected function applyFiltering(Builder $query)
    {
        $requestFilters = $this->request->get('filter', []);
        if (!is_array($requestFilters)) {
            return;
        }

        $queryFilters = collect($requestFilters)->map(function ($value, $key) {
            return $this->requestFilterToQueryFilter($key, $value);
        })->filter(function ($value) {
            return !is_null($value);
        })->values()->toArray();

        $query->where($queryFilters);
    }

    /**
     * Convert a request filter query key/value pair into a [field, op, value] where condition.
     */
    protected function requestFilterToQueryFilter($fieldKey, $value): ?array
    {
        $splitKey = explode(':', $fieldKey);
        $field = $splitKey[0];
        $filterOperator = $splitKey[1] ?? 'eq';

        if (!in_array($field, $this->fields)) {
            return null;
        }

        if (!in_array($filterOperator, array_keys($this->filterOperators))) {
            $filterOperator = 'eq';
        }

        $queryOperator = $this->filterOperators[$filterOperator];
        return [$field, $queryOperator, $value];
    }

    /**
     * Apply sorting operations to the query from given parameters
     * otherwise falling back to the first given field, ascending.
     */
    protected function applySorting(Builder $query)
    {
        $defaultSortName = $this->fields[0];
        $direction = 'asc';

        $sort = $this->request->get('sort', '');
        if (strpos($sort, '-') === 0) {
            $direction = 'desc';
        }

        $sortName = ltrim($sort, '+- ');
        if (!in_array($sortName, $this->fields)) {
            $sortName = $defaultSortName;
        }

        $query->orderBy($sortName, $direction);
    }

    /**
     * Apply count and offset for paging, based on params from the request while falling
     * back to system defined default, taking the max limit into account.
     */
    protected function applyCountAndOffset(Builder $query)
    {
        $offset = max(0, $this->request->get('offset', 0));
        $maxCount = config('api.max_item_count');
        $count = $this->request->get('count', config('api.default_item_count'));
        $count = max(min($maxCount, $count), 1);

        $query->skip($offset)->take($count);
    }
}
