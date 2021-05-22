<?php namespace BookStack\Entities\Queries;


use BookStack\Actions\View;
use Illuminate\Support\Facades\DB;

class Popular extends EntityQuery
{
    public function run(int $count, int $page, array $filterModels = null, string $action = 'view')
    {
        $query = $this->permissionService()
            ->filterRestrictedEntityRelations(View::query(), 'views', 'viewable_id', 'viewable_type', $action)
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