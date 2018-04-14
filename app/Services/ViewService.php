<?php namespace BookStack\Services;

use BookStack\Entity;
use BookStack\View;

class ViewService
{
    protected $view;
    protected $permissionService;

    /**
     * ViewService constructor.
     * @param View $view
     * @param PermissionService $permissionService
     */
    public function __construct(View $view, PermissionService $permissionService)
    {
        $this->view = $view;
        $this->permissionService = $permissionService;
    }

    /**
     * Add a view to the given entity.
     * @param Entity $entity
     * @return int
     */
    public function add(Entity $entity)
    {
        $user = user();
        if ($user === null || $user->isDefault()) {
            return 0;
        }
        $view = $entity->views()->where('user_id', '=', $user->id)->first();
        // Add view if model exists
        if ($view) {
            $view->increment('views');
            return $view->views;
        }

        // Otherwise create new view count
        $entity->views()->save($this->view->create([
            'user_id' => $user->id,
            'views' => 1
        ]));

        return 1;
    }

    /**
     * Get the entities with the most views.
     * @param int $count
     * @param int $page
     * @param bool|false|array $filterModel
     * @param string $action - used for permission checking
     * @return
     */
    public function getPopular($count = 10, $page = 0, $filterModel = false, $action = 'view')
    {
        $skipCount = $count * $page;
        $query = $this->permissionService->filterRestrictedEntityRelations($this->view, 'views', 'viewable_id', 'viewable_type', $action)
            ->select('*', 'viewable_id', 'viewable_type', \DB::raw('SUM(views) as view_count'))
            ->groupBy('viewable_id', 'viewable_type')
            ->orderBy('view_count', 'desc');

        if ($filterModel && is_array($filterModel)) {
            $query->whereIn('viewable_type', $filterModel);
        } else if ($filterModel) {
            $query->where('viewable_type', '=', get_class($filterModel));
        }

        return $query->with('viewable')->skip($skipCount)->take($count)->get()->pluck('viewable');
    }

    /**
     * Get all recently viewed entities for the current user.
     * @param int $count
     * @param int $page
     * @param Entity|bool $filterModel
     * @return mixed
     */
    public function getUserRecentlyViewed($count = 10, $page = 0, $filterModel = false)
    {
        $user = user();
        if ($user === null || $user->isDefault()) {
            return collect();
        }

        $query = $this->permissionService
            ->filterRestrictedEntityRelations($this->view, 'views', 'viewable_id', 'viewable_type');

        if ($filterModel) {
            $query = $query->where('viewable_type', '=', get_class($filterModel));
        }
        $query = $query->where('user_id', '=', $user->id);

        $viewables = $query->with('viewable')->orderBy('updated_at', 'desc')
            ->skip($count * $page)->take($count)->get()->pluck('viewable');
        return $viewables;
    }

    /**
     * Reset all view counts by deleting all views.
     */
    public function resetAll()
    {
        $this->view->truncate();
    }
}
