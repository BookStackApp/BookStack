<?php

namespace BookStack\Entities\Queries;

use BookStack\Actions\Favourite;
use Illuminate\Database\Query\JoinClause;

class TopFavourites extends EntityQuery
{

    private $types;

    public function __construct($types = ['page', 'chapter', 'book', 'bookshelf'])
    {
        $this->types = $types;
    }

    public function run(int $count, int $skip = 0)
    {
        $user = user();
        if (is_null($user) || $user->isDefault()) {
            return collect();
        }

        $query = $this->permissionService()
            ->filterRestrictedEntityRelations(Favourite::query(), 'favourites', 'favouritable_id', 'favouritable_type', 'view')
            ->whereIn('favouritable_type',  $this->types)
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
