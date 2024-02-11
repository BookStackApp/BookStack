<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\View;
use BookStack\Entities\Tools\MixedEntityListLoader;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Support\Collection;

class QueryRecentlyViewed
{
    public function __construct(
        protected PermissionApplicator $permissions,
        protected MixedEntityListLoader $listLoader,
    ) {
    }

    public function run(int $count, int $page): Collection
    {
        $user = user();
        if ($user->isGuest()) {
            return collect();
        }

        $query = $this->permissions->restrictEntityRelationQuery(
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

        $this->listLoader->loadIntoRelations($views->all(), 'viewable', false);

        return $views->pluck('viewable')->filter();
    }
}
