<?php

namespace BookStack\Auth\Queries;

use BookStack\Auth\User;
use BookStack\Util\SimpleListOptions;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Get all the users with their permissions in a paginated format.
 * Note: Due to the use of email search this should only be used when
 * user is assumed to be trusted. (Admin users).
 * Email search can be abused to extract email addresses.
 */
class UsersAllPaginatedAndSorted
{
    public function run(int $count, SimpleListOptions $listOptions): LengthAwarePaginator
    {
        $sort = $listOptions->getSort();
        if ($sort === 'created_at') {
            $sort = 'users.created_at';
        }

        $query = User::query()->select(['*'])
            ->scopes(['withLastActivityAt'])
            ->with(['roles', 'avatar'])
            ->withCount('mfaValues')
            ->orderBy($sort, $listOptions->getOrder());

        if ($listOptions->getSearch()) {
            $term = '%' . $listOptions->getSearch() . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->paginate($count);
    }
}
