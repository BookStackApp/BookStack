<?php namespace BookStack\Services;


use BookStack\Entity;
use BookStack\View;

class ViewService
{

    protected $view;
    protected $user;

    /**
     * ViewService constructor.
     * @param $view
     */
    public function __construct(View $view)
    {
        $this->view = $view;
        $this->user = auth()->user();
    }

    /**
     * Add a view to the given entity.
     * @param Entity $entity
     * @return int
     */
    public function add(Entity $entity)
    {
        $view = $entity->views()->where('user_id', '=', $this->user->id)->first();
        // Add view if model exists
        if ($view) {
            $view->increment('views');
            return $view->views;
        }

        // Otherwise create new view count
        $entity->views()->save($this->view->create([
            'user_id' => $this->user->id,
            'views' => 1
        ]));

        return 1;
    }

    /**
     * Get all recently viewed entities for the current user.
     * @param int         $count
     * @param int         $page
     * @param Entity|bool $filterModel
     * @return mixed
     */
    public function getUserRecentlyViewed($count = 10, $page = 0, $filterModel = false)
    {
        $skipCount = $count * $page;
        $query = $this->view->where('user_id', '=', auth()->user()->id);

        if ($filterModel) $query->where('viewable_type', '=', get_class($filterModel));

        $views = $query->with('viewable')->orderBy('updated_at', 'desc')->skip($skipCount)->take($count)->get();
        $viewedEntities = $views->map(function ($item) {
            return $item->viewable()->getResults();
        });
        return $viewedEntities;
    }


    /**
     * Reset all view counts by deleting all views.
     */
    public function resetAll()
    {
        $this->view->truncate();
    }


}