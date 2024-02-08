<?php

namespace BookStack\Entities\Queries;

use BookStack\Activity\Models\Favourite;
use BookStack\Entities\Tools\MixedEntityListLoader;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Database\Query\JoinClause;

class QueryTopFavourites
{
    public function __construct(
        protected PermissionApplicator $permissions,
        protected MixedEntityListLoader $listLoader,
    ) {
    }

    public function run(int $count, int $skip = 0)
    {
        $user = user();
        if ($user->isGuest()) {
            return collect();
        }

        $query = $this->permissions
            ->restrictEntityRelationQuery(Favourite::query(), 'favourites', 'favouritable_id', 'favouritable_type')
            ->select('favourites.*')
            ->leftJoin('views', function (JoinClause $join) {
                $join->on('favourites.favouritable_id', '=', 'views.viewable_id');
                $join->on('favourites.favouritable_type', '=', 'views.viewable_type');
                $join->where('views.user_id', '=', user()->id);
            })
            ->orderBy('views.views', 'desc')
            ->where('favourites.user_id', '=', user()->id);

        $favourites = $query
            ->skip($skip)
            ->take($count)
            ->get();

        $this->listLoader->loadIntoRelations($favourites->all(), 'favouritable', false);

        return $favourites->pluck('favouritable')->filter();
    }
}
