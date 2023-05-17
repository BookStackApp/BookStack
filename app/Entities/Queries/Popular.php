<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\View;
use Illuminate\Support\Facades\DB;

class Popular extends EntityQuery
{
    public function run(int $count, int $page, array $filterModels = null)
    {
        $query = $this->permissionService()
            ->restrictEntityRelationQuery(View::query(), 'views', 'viewable_id', 'viewable_type')
            ->select('*', 'viewable_id', 'viewable_type', DB::raw('SUM(views) as view_count'))
            ->groupBy('viewable_id', 'viewable_type')
            ->orderBy('view_count', 'desc');

        if ($filterModels) {
            $query->whereIn('viewable_type', $this->entityProvider()->getMorphClasses($filterModels));
        }

        return $query->with('viewable')
            ->skip($count * ($page - 1))
            ->take($count)
            ->get()
            ->pluck('viewable')
            ->filter();
    }
}
