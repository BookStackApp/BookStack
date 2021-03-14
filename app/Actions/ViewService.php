<?php namespace BookStack\Actions;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\EntityProvider;
use DB;
use Illuminate\Support\Collection;

class ViewService
{
    protected $view;
    protected $permissionService;
    protected $entityProvider;

    /**
     * ViewService constructor.
     * @param View $view
     * @param PermissionService $permissionService
     * @param EntityProvider $entityProvider
     */
    public function __construct(View $view, PermissionService $permissionService, EntityProvider $entityProvider)
    {
        $this->view = $view;
        $this->permissionService = $permissionService;
        $this->entityProvider = $entityProvider;
    }

    /**
     * Add a view to the given entity.
     * @param \BookStack\Entities\Models\Entity $entity
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
        $entity->views()->save($this->view->newInstance([
            'user_id' => $user->id,
            'views' => 1
        ]));

        return 1;
    }

    /**
     * Get the entities with the most views.
     * @param int $count
     * @param int $page
     * @param string|array $filterModels
     * @param string $action - used for permission checking
     * @return Collection
     */
    public function getPopular(int $count = 10, int $page = 0, array $filterModels = null, string $action = 'view')
    {
        $skipCount = $count * $page;
        $query = $this->permissionService
            ->filterRestrictedEntityRelations($this->view->newQuery(), 'views', 'viewable_id', 'viewable_type', $action)
            ->select('*', 'viewable_id', 'viewable_type', DB::raw('SUM(views) as view_count'))
            ->groupBy('viewable_id', 'viewable_type')
            ->orderBy('view_count', 'desc');

        if ($filterModels) {
            $query->whereIn('viewable_type', $this->entityProvider->getMorphClasses($filterModels));
        }

        return $query->with('viewable')
            ->skip($skipCount)
            ->take($count)
            ->get()
            ->pluck('viewable')
            ->filter();
    }

    /**
     * Get all recently viewed entities for the current user.
     */
    public function getUserRecentlyViewed(int $count = 10, int $page = 1)
    {
        $user = user();
        if ($user === null || $user->isDefault()) {
            return collect();
        }

        $all = collect();
        /** @var Entity $instance */
        foreach ($this->entityProvider->all() as $name => $instance) {
            $items = $instance::visible()->withLastView()
                ->orderBy('last_viewed_at', 'desc')
                ->skip($count * ($page - 1))
                ->take($count)
                ->get();
            $all = $all->concat($items);
        }

        return $all->sortByDesc('last_viewed_at')->slice(0, $count);
    }

    /**
     * Reset all view counts by deleting all views.
     */
    public function resetAll()
    {
        $this->view->truncate();
    }
}
