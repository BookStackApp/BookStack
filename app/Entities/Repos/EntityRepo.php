<?php namespace BookStack\Entities\Repos;

use BookStack\Actions\TagRepo;
use BookStack\Actions\ViewService;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\SearchService;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class EntityRepo
{

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var ViewService
     */
    protected $viewService;

    /**
     * @var TagRepo
     */
    protected $tagRepo;

    /**
     * @var SearchService
     */
    protected $searchService;

    /**
     * EntityRepo constructor.
     * @param EntityProvider $entityProvider
     * @param ViewService $viewService
     * @param PermissionService $permissionService
     * @param TagRepo $tagRepo
     * @param SearchService $searchService
     */
    public function __construct(
        EntityProvider $entityProvider,
        ViewService $viewService,
        PermissionService $permissionService,
        TagRepo $tagRepo,
        SearchService $searchService
    ) {
        $this->entityProvider = $entityProvider;
        $this->viewService = $viewService;
        $this->permissionService = $permissionService;
        $this->tagRepo = $tagRepo;
        $this->searchService = $searchService;
    }

    /**
     * Base query for searching entities via permission system
     * @param string $type
     * @param bool $allowDrafts
     * @param string $permission
     * @return QueryBuilder
     */
    protected function entityQuery($type, $allowDrafts = false, $permission = 'view')
    {
        $q = $this->permissionService->enforceEntityRestrictions($type, $this->entityProvider->get($type), $permission);
        if (strtolower($type) === 'page' && !$allowDrafts) {
            $q = $q->where('draft', '=', false);
        }
        return $q;
    }


    /**
     * Get all entities in a paginated format
     * @param $type
     * @param int $count
     * @param string $sort
     * @param string $order
     * @param null|callable $queryAddition
     * @return LengthAwarePaginator
     */
    public function getAllPaginated($type, int $count = 10, string $sort = 'name', string $order = 'asc', $queryAddition = null)
    {
        $query = $this->entityQuery($type);
        $query = $this->addSortToQuery($query, $sort, $order);
        if ($queryAddition) {
            $queryAddition($query);
        }
        return $query->paginate($count);
    }

    /**
     * Add sorting operations to an entity query.
     * @param Builder $query
     * @param string $sort
     * @param string $order
     * @return Builder
     */
    protected function addSortToQuery(Builder $query, string $sort = 'name', string $order = 'asc')
    {
        $order = ($order === 'asc') ? 'asc' : 'desc';
        $propertySorts = ['name', 'created_at', 'updated_at'];

        if (in_array($sort, $propertySorts)) {
            return $query->orderBy($sort, $order);
        }

        return $query;
    }

    /**
     * Get the most recently created entities of the given type.
     * @param string $type
     * @param int $count
     * @param int $page
     * @param bool|callable $additionalQuery
     * @return Collection
     */
    public function getRecentlyCreated($type, $count = 20, $page = 0, $additionalQuery = false)
    {
        $entityModel = $this->entityProvider->get($type);
        $query = $entityModel::visible()->orderBy('created_at', 'desc');

        if ($entityModel->isA('page')) {
            $query->where('draft', '=', false);
        }

        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }

        return $query->skip($page * $count)->take($count)->get();
    }


    /**
     * Get draft pages owned by the current user.
     * @param int $count
     * @param int $page
     * @return Collection
     */
    public function getUserDraftPages($count = 20, $page = 0)
    {
        return $this->entityProvider->page->where('draft', '=', true)
            ->where('created_by', '=', user()->id)
            ->orderBy('updated_at', 'desc')
            ->skip($count * $page)->take($count)->get();
    }

    /**
     * Get the number of entities the given user has created.
     * @param string $type
     * @param User $user
     * @return int
     */
    public function getUserTotalCreated(string $type, User $user)
    {
        return $this->entityProvider->get($type)
            ->where('created_by', '=', $user->id)->count();
    }

}
