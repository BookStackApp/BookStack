<?php

namespace BookStack\Api;

use BookStack\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ListingResponseBuilder
{
    protected $query;
    protected $request;
    protected $fields;

    /**
     * @var array<callable>
     */
    protected $resultModifiers = [];

    protected $filterOperators = [
        'eq'   => '=',
        'ne'   => '!=',
        'gt'   => '>',
        'lt'   => '<',
        'gte'  => '>=',
        'lte'  => '<=',
        'like' => 'like',
    ];

    /**
     * ListingResponseBuilder constructor.
     * The given fields will be forced visible within the model results.
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
    public function toResponse(): JsonResponse
    {
        $filteredQuery = $this->filterQuery($this->query);

        $total = $filteredQuery->count();
        $data = $this->fetchData($filteredQuery)->each(function($model) {
            foreach ($this->resultModifiers as $modifier) {
                $modifier($model);
            }
        });

        return response()->json([
            'data'  => $data,
            'total' => $total,
        ]);
    }

    /**
     * Add a callback to modify each element of the results
     * @param (callable(Model)) $modifier
     */
    public function modifyResults($modifier): void
    {
        $this->resultModifiers[] = $modifier;
    }

    /**
     * Fetch the data to return within the response.
     */
    protected function fetchData(Builder $query): Collection
    {
        $query = $this->countAndOffsetQuery($query);
        $query = $this->sortQuery($query);

        return $query->get($this->fields);
    }

    /**
     * Apply any filtering operations found in the request.
     */
    protected function filterQuery(Builder $query): Builder
    {
        $query = clone $query;
        $requestFilters = $this->request->get('filter', []);
        if (!is_array($requestFilters)) {
            return $query;
        }

        $queryFilters = collect($requestFilters)->map(function ($value, $key) {
            return $this->requestFilterToQueryFilter($key, $value);
        })->filter(function ($value) {
            return !is_null($value);
        })->values()->toArray();

        return $query->where($queryFilters);
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
    protected function sortQuery(Builder $query): Builder
    {
        $query = clone $query;
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

        return $query->orderBy($sortName, $direction);
    }

    /**
     * Apply count and offset for paging, based on params from the request while falling
     * back to system defined default, taking the max limit into account.
     */
    protected function countAndOffsetQuery(Builder $query): Builder
    {
        $query = clone $query;
        $offset = max(0, $this->request->get('offset', 0));
        $maxCount = config('api.max_item_count');
        $count = $this->request->get('count', config('api.default_item_count'));
        $count = max(min($maxCount, $count), 1);

        return $query->skip($offset)->take($count);
    }
}
