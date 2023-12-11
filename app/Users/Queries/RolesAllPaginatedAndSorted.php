<?php

namespace BookStack\Users\Queries;

use BookStack\Users\Models\Role;
use BookStack\Util\SimpleListOptions;
use Illuminate\Pagination\LengthAwarePaginator;

/**
 * Get all the roles in the system in a paginated format.
 */
class RolesAllPaginatedAndSorted
{
    public function run(int $count, SimpleListOptions $listOptions): LengthAwarePaginator
    {
        $sort = $listOptions->getSort();
        if ($sort === 'created_at') {
            $sort = 'roles.created_at';
        }

        $query = Role::query()->select(['*'])
            ->withCount(['users', 'permissions'])
            ->orderBy($sort, $listOptions->getOrder());

        if ($listOptions->getSearch()) {
            $term = '%' . $listOptions->getSearch() . '%';
            $query->where(function ($query) use ($term) {
                $query->where('display_name', 'like', $term)
                    ->orWhere('description', 'like', $term);
            });
        }

        return $query->paginate($count);
    }
}
