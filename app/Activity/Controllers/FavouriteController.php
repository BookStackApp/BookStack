<?php

namespace BookStack\Activity\Controllers;

use BookStack\Entities\Queries\TopFavourites;
use BookStack\Entities\Tools\MixedEntityRequestHelper;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class FavouriteController extends Controller
{
    public function __construct(
        protected MixedEntityRequestHelper $entityHelper,
    ) {
    }

    /**
     * Show a listing of all favourite items for the current user.
     */
    public function index(Request $request)
    {
        $viewCount = 20;
        $page = intval($request->get('page', 1));
        $favourites = (new TopFavourites())->run($viewCount + 1, (($page - 1) * $viewCount));

        $hasMoreLink = ($favourites->count() > $viewCount) ? url('/favourites?page=' . ($page + 1)) : null;

        $this->setPageTitle(trans('entities.my_favourites'));

        return view('common.detailed-listing-with-more', [
            'title'       => trans('entities.my_favourites'),
            'entities'    => $favourites->slice(0, $viewCount),
            'hasMoreLink' => $hasMoreLink,
        ]);
    }

    /**
     * Add a new item as a favourite.
     */
    public function add(Request $request)
    {
        $modelInfo = $this->validate($request, $this->entityHelper->validationRules());
        $entity = $this->entityHelper->getVisibleEntityFromRequestData($modelInfo);
        $entity->favourites()->firstOrCreate([
            'user_id' => user()->id,
        ]);

        $this->showSuccessNotification(trans('activities.favourite_add_notification', [
            'name' => $entity->name,
        ]));

        return redirect($entity->getUrl());
    }

    /**
     * Remove an item as a favourite.
     */
    public function remove(Request $request)
    {
        $modelInfo = $this->validate($request, $this->entityHelper->validationRules());
        $entity = $this->entityHelper->getVisibleEntityFromRequestData($modelInfo);
        $entity->favourites()->where([
            'user_id' => user()->id,
        ])->delete();

        $this->showSuccessNotification(trans('activities.favourite_remove_notification', [
            'name' => $entity->name,
        ]));

        return redirect($entity->getUrl());
    }
}
