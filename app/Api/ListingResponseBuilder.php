<?php namespace BookStack\Api;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ListingResponseBuilder
{

    protected $query;
    protected $fields;

    /**
     * ListingResponseBuilder constructor.
     */
    public function __construct(Builder $query, array $fields)
    {
        $this->query = $query;
        $this->fields = $fields;
    }

    /**
     * Get the response from this builder.
     */
    public function toResponse()
    {
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
        // TODO - Apply filtering

        return $this->query->get($this->fields);
    }

    /**
     * Apply sorting operations to the query from given parameters
     * otherwise falling back to the first given field, ascending.
     */
    protected function applySorting(Builder $query)
    {
        $defaultSortName = $this->fields[0];
        $direction = 'asc';

        $sort = request()->get('sort', '');
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
        $offset = max(0, request()->get('offset', 0));
        $maxCount = config('api.max_item_count');
        $count = request()->get('count', config('api.default_item_count'));
        $count = max(min($maxCount, $count), 1);

        $query->skip($offset)->take($count);
    }
}
