<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\View;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Tools\MixedEntityListLoader;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

class QueryPopular
{
    public function __construct(
        protected PermissionApplicator $permissions,
        protected EntityProvider $entityProvider,
        protected MixedEntityListLoader $listLoader,
    ) {
    }

    public function run(int $count, int $page, array $filterModels): Collection
    {
        $query = $this->permissions
            ->restrictEntityRelationQuery(View::query(), 'views', 'viewable_id', 'viewable_type')
            ->select('*', 'viewable_id', 'viewable_type', DB::raw('SUM(views) as view_count'))
            ->groupBy('viewable_id', 'viewable_type')
            ->orderBy('view_count', 'desc');

        if (!empty($filterModels)) {
            $query->whereIn('viewable_type', $this->entityProvider->getMorphClasses($filterModels));
        }

        $views = $query
            ->skip($count * ($page - 1))
            ->take($count)
            ->get();

        $this->listLoader->loadIntoRelations($views->all(), 'viewable', true);

        return $views->pluck('viewable')->filter();
    }
}
