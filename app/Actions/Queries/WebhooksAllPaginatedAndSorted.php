<?php

namespace BookStack\Actions\Queries;

use BookStack\Actions\Webhook;
use BookStack\Util\SimpleListOptions;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Get all the webhooks in the system in a paginated format.
 */
class WebhooksAllPaginatedAndSorted
{
    public function run(int $count, SimpleListOptions $listOptions): LengthAwarePaginator
    {
        $query = Webhook::query()->select(['*'])
            ->withCount(['trackedEvents'])
            ->orderBy($listOptions->getSort(), $listOptions->getOrder());

        if ($listOptions->getSearch()) {
            $term = '%' . $listOptions->getSearch() . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('endpoint', 'like', $term);
            });
        }

        return $query->paginate($count);
    }
}
