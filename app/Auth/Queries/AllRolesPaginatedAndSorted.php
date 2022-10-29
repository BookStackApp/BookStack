<?php

namespace BookStack\Auth\Queries;

use BookStack\Auth\Role;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Get all the roles in the system in a paginated format.
 */
class AllRolesPaginatedAndSorted
{
    /**
     * @param array{sort: string, order: string, search: string} $sortData
     */
    public function run(int $count, array $sortData): LengthAwarePaginator
    {
        $sort = $sortData['sort'];
        if ($sort === 'created_at') {
            $sort = 'users.created_at';
        }

        $query = Role::query()->select(['*'])
            ->withCount(['users', 'permissions'])
            ->orderBy($sort, $sortData['order']);

        if ($sortData['search']) {
            $term = '%' . $sortData['search'] . '%';
            $query->where(function ($query) use ($term) {
                $query->where('display_name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        return $query->paginate($count);
    }
}
