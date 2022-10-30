<?php

namespace BookStack\Actions\Queries;

use BookStack\Actions\Webhook;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Get all the webhooks in the system in a paginated format.
 */
class WebhooksAllPaginatedAndSorted
{
    /**
     * @param array{sort: string, order: string, search: string} $sortData
     */
    public function run(int $count, array $sortData): LengthAwarePaginator
    {
        $sort = $sortData['sort'];

        $query = Webhook::query()->select(['*'])
            ->withCount(['trackedEvents'])
            ->orderBy($sort, $sortData['order']);

        if ($sortData['search']) {
            $term = '%' . $sortData['search'] . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('endpoint', 'like', $term);
            });
        }

        return $query->paginate($count);
    }
}
