<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\View;
use Illuminate\Support\Collection;

class RecentlyViewed extends EntityQuery
{
    public function run(int $count, int $page): Collection
    {
        $user = user();
        if ($user->isGuest()) {
            return collect();
        }

        $query = $this->permissionService()->restrictEntityRelationQuery(
            View::query(),
            'views',
            'viewable_id',
            'viewable_type'
        )
            ->orderBy('views.updated_at', 'desc')
            ->where('user_id', '=', user()->id);

        $views = $query
            ->skip(($page - 1) * $count)
            ->take($count)
            ->get();

        $this->mixedEntityListLoader()->loadIntoRelations($views->all(), 'viewable', false);

        return $views->pluck('viewable')->filter();
    }
}
