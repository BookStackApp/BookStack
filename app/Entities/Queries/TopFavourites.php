<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\Favourite;
use Illuminate\Database\Query\JoinClause;

class TopFavourites extends EntityQuery
{
    public function run(int $count, int $skip = 0)
    {
        $user = user();
        if ($user->isDefault()) {
            return collect();
        }

        $query = $this->permissionService()
            ->restrictEntityRelationQuery(Favourite::query(), 'favourites', 'favouritable_id', 'favouritable_type')
            ->select('favourites.*')
            ->leftJoin('views', function (JoinClause $join) {
                $join->on('favourites.favouritable_id', '=', 'views.viewable_id');
                $join->on('favourites.favouritable_type', '=', 'views.viewable_type');
                $join->where('views.user_id', '=', user()->id);
            })
            ->orderBy('views.views', 'desc')
            ->where('favourites.user_id', '=', user()->id);

        return $query->with('favouritable')
            ->skip($skip)
            ->take($count)
            ->get()
            ->pluck('favouritable')
            ->filter();
    }
}
