<?php

namespace BookStack\Entities\Queries;

use BookStack\Actions\View;
use Illuminate\Support\Collection;

class RecentlyViewed extends EntityQuery
{
    public function run(int $count, int $page): Collection
    {
        $user = user();
        if ($user === null || $user->isDefault()) {
            return collect();
        }

        $query = $this->permissionService()->filterRestrictedEntityRelations(
            View::query(),
            'views',
            'viewable_id',
            'viewable_type',
            'view'
        )
            ->orderBy('views.updated_at', 'desc')
            ->where('user_id', '=', user()->id);

        return $query->with('viewable')
            ->skip(($page - 1) * $count)
            ->take($count)
            ->get()
            ->pluck('viewable')
            ->filter();
    }
}
