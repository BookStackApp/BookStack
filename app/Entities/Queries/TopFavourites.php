<?php namespace BookStack\Entities\Queries;

use BookStack\Actions\View;
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;

class TopFavourites extends EntityQuery
{
    public function run(int $count, int $skip = 0)
    {
        $user = user();
        if ($user === null || $user->isDefault()) {
            return collect();
        }

        $query = $this->permissionService()
            ->filterRestrictedEntityRelations(View::query(), 'views', 'viewable_id', 'viewable_type', 'view')
            ->select('*', 'viewable_id', 'viewable_type', DB::raw('SUM(views) as view_count'))
            ->groupBy('viewable_id', 'viewable_type')
            ->rightJoin('favourites', function (JoinClause $join) {
                $join->on('views.viewable_id', '=', 'favourites.favouritable_id');
                $join->on('views.viewable_type', '=', 'favourites.favouritable_type');
                $join->where('favourites.user_id', '=', user()->id);
            })
            ->orderBy('view_count', 'desc');

        return $query->with('viewable')
            ->skip($skip)
            ->take($count)
            ->get()
            ->pluck('viewable')
            ->filter();
    }
}
