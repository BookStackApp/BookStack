<?php

namespace BookStack\Auth\Queries;


use BookStack\Auth\User;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Get all the users with their permissions in a paginated format.
 * Note: Due to the use of email search this should only be used when
 * user is assumed to be trusted. (Admin users).
 * Email search can be abused to extract email addresses.
 */
class AllUsersPaginatedAndSorted
{

    /**
     * @param array{sort: string, order: string, search: string} $sortData
     */
    public function run(int $count, array $sortData): LengthAwarePaginator
    {
        $sort = $sortData['sort'];

        $query = User::query()->select(['*'])
            ->scopes(['withLastActivityAt'])
            ->with(['roles', 'avatar'])
            ->withCount('mfaValues')
            ->orderBy($sort, $sortData['order']);

        if ($sortData['search']) {
            $term = '%' . $sortData['search'] . '%';
            $query->where(function ($query) use ($term) {
                $query->where('name', 'like', $term)
                    ->orWhere('email', 'like', $term);
            });
        }

        return $query->paginate($count);
    }

}