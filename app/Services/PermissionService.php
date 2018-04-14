<?php namespace BookStack\Services;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\EntityPermission;
use BookStack\JointPermission;
use BookStack\Ownable;
use BookStack\Page;
use BookStack\Role;
use BookStack\User;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Support\Collection;

class PermissionService
{

    protected $currentAction;
    protected $isAdminUser;
    protected $userRoles = false;
    protected $currentUserModel = false;

    public $book;
    public $chapter;
    public $page;

    protected $db;

    protected $jointPermission;
    protected $role;
    protected $entityPermission;

    protected $entityCache;

    /**
     * PermissionService constructor.
     * @param JointPermission $jointPermission
     * @param EntityPermission $entityPermission
     * @param Connection $db
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param Role $role
     */
    public function __construct(JointPermission $jointPermission, EntityPermission $entityPermission, Connection $db, Book $book, Chapter $chapter, Page $page, Role $role)
    {
        $this->db = $db;
        $this->jointPermission = $jointPermission;
        $this->entityPermission = $entityPermission;
        $this->role = $role;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        // TODO - Update so admin still goes through filters
    }

    /**
     * Set the database connection
     * @param Connection $connection
     */
    public function setConnection(Connection $connection)
    {
        $this->db = $connection;
    }

    /**
     * Prepare the local entity cache and ensure it's empty
     */
    protected function readyEntityCache()
    {
        $this->entityCache = [
            'books' => collect(),
            'chapters' => collect()
        ];
    }

    /**
     * Get a book via ID, Checks local cache
     * @param $bookId
     * @return Book
     */
    protected function getBook($bookId)
    {
        if (isset($this->entityCache['books']) && $this->entityCache['books']->has($bookId)) {
            return $this->entityCache['books']->get($bookId);
        }

        $book = $this->book->find($bookId);
        if ($book === null) {
            $book = false;
        }
        if (isset($this->entityCache['books'])) {
            $this->entityCache['books']->put($bookId, $book);
        }

        return $book;
    }

    /**
     * Get a chapter via ID, Checks local cache
     * @param $chapterId
     * @return Book
     */
    protected function getChapter($chapterId)
    {
        if (isset($this->entityCache['chapters']) && $this->entityCache['chapters']->has($chapterId)) {
            return $this->entityCache['chapters']->get($chapterId);
        }

        $chapter = $this->chapter->find($chapterId);
        if ($chapter === null) {
            $chapter = false;
        }
        if (isset($this->entityCache['chapters'])) {
            $this->entityCache['chapters']->put($chapterId, $chapter);
        }

        return $chapter;
    }

    /**
     * Get the roles for the current user;
     * @return array|bool
     */
    protected function getRoles()
    {
        if ($this->userRoles !== false) {
            return $this->userRoles;
        }

        $roles = [];

        if (auth()->guest()) {
            $roles[] = $this->role->getSystemRole('public')->id;
            return $roles;
        }


        foreach ($this->currentUser()->roles as $role) {
            $roles[] = $role->id;
        }
        return $roles;
    }

    /**
     * Re-generate all entity permission from scratch.
     */
    public function buildJointPermissions()
    {
        $this->jointPermission->truncate();
        $this->readyEntityCache();

        // Get all roles (Should be the most limited dimension)
        $roles = $this->role->with('permissions')->get()->all();

        // Chunk through all books
        $this->bookFetchQuery()->chunk(5, function ($books) use ($roles) {
            $this->buildJointPermissionsForBooks($books, $roles);
        });
    }

    /**
     * Get a query for fetching a book with it's children.
     * @return QueryBuilder
     */
    protected function bookFetchQuery()
    {
        return $this->book->newQuery()->select(['id', 'restricted', 'created_by'])->with(['chapters' => function ($query) {
            $query->select(['id', 'restricted', 'created_by', 'book_id']);
        }, 'pages'  => function ($query) {
            $query->select(['id', 'restricted', 'created_by', 'book_id', 'chapter_id']);
        }]);
    }

    /**
     * Build joint permissions for an array of books
     * @param Collection $books
     * @param array $roles
     * @param bool $deleteOld
     */
    protected function buildJointPermissionsForBooks($books, $roles, $deleteOld = false)
    {
        $entities = clone $books;

        /** @var Book $book */
        foreach ($books->all() as $book) {
            foreach ($book->getRelation('chapters') as $chapter) {
                $entities->push($chapter);
            }
            foreach ($book->getRelation('pages') as $page) {
                $entities->push($page);
            }
        }

        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($entities->all());
        }
        $this->createManyJointPermissions($entities, $roles);
    }

    /**
     * Rebuild the entity jointPermissions for a particular entity.
     * @param Entity $entity
     */
    public function buildJointPermissionsForEntity(Entity $entity)
    {
        $entities = [$entity];
        if ($entity->isA('book')) {
            $books = $this->bookFetchQuery()->where('id', '=', $entity->id)->get();
            $this->buildJointPermissionsForBooks($books, $this->role->newQuery()->get(), true);
            return;
        }

        $entities[] = $entity->book;

        if ($entity->isA('page') && $entity->chapter_id) {
            $entities[] = $entity->chapter;
        }

        if ($entity->isA('chapter')) {
            foreach ($entity->pages as $page) {
                $entities[] = $page;
            }
        }

        $this->deleteManyJointPermissionsForEntities($entities);
        $this->buildJointPermissionsForEntities(collect($entities));
    }

    /**
     * Rebuild the entity jointPermissions for a collection of entities.
     * @param Collection $entities
     */
    public function buildJointPermissionsForEntities(Collection $entities)
    {
        $roles = $this->role->newQuery()->get();
        $this->deleteManyJointPermissionsForEntities($entities->all());
        $this->createManyJointPermissions($entities, $roles);
    }

    /**
     * Build the entity jointPermissions for a particular role.
     * @param Role $role
     */
    public function buildJointPermissionForRole(Role $role)
    {
        $roles = [$role];
        $this->deleteManyJointPermissionsForRoles($roles);

        // Chunk through all books
        $this->bookFetchQuery()->chunk(5, function ($books) use ($roles) {
            $this->buildJointPermissionsForBooks($books, $roles);
        });
    }

    /**
     * Delete the entity jointPermissions attached to a particular role.
     * @param Role $role
     */
    public function deleteJointPermissionsForRole(Role $role)
    {
        $this->deleteManyJointPermissionsForRoles([$role]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     * @param Role[] $roles
     */
    protected function deleteManyJointPermissionsForRoles($roles)
    {
        $roleIds = array_map(function ($role) {
            return $role->id;
        }, $roles);
        $this->jointPermission->newQuery()->whereIn('role_id', $roleIds)->delete();
    }

    /**
     * Delete the entity jointPermissions for a particular entity.
     * @param Entity $entity
     */
    public function deleteJointPermissionsForEntity(Entity $entity)
    {
        $this->deleteManyJointPermissionsForEntities([$entity]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     * @param Entity[] $entities
     */
    protected function deleteManyJointPermissionsForEntities($entities)
    {
        if (count($entities) === 0) {
            return;
        }

        $this->db->transaction(function () use ($entities) {

            foreach (array_chunk($entities, 1000) as $entityChunk) {
                $query = $this->db->table('joint_permissions');
                foreach ($entityChunk as $entity) {
                    $query->orWhere(function (QueryBuilder $query) use ($entity) {
                        $query->where('entity_id', '=', $entity->id)
                            ->where('entity_type', '=', $entity->getMorphClass());
                    });
                }
                $query->delete();
            }
        });
    }

    /**
     * Create & Save entity jointPermissions for many entities and jointPermissions.
     * @param Collection $entities
     * @param array $roles
     */
    protected function createManyJointPermissions($entities, $roles)
    {
        $this->readyEntityCache();
        $jointPermissions = [];

        // Fetch Entity Permissions and create a mapping of entity restricted statuses
        $entityRestrictedMap = [];
        $permissionFetch = $this->entityPermission->newQuery();
        foreach ($entities as $entity) {
            $entityRestrictedMap[$entity->getMorphClass() . ':' . $entity->id] = boolval($entity->getRawAttribute('restricted'));
            $permissionFetch->orWhere(function ($query) use ($entity) {
                $query->where('restrictable_id', '=', $entity->id)->where('restrictable_type', '=', $entity->getMorphClass());
            });
        }
        $permissions = $permissionFetch->get();

        // Create a mapping of explicit entity permissions
        $permissionMap = [];
        foreach ($permissions as $permission) {
            $key = $permission->restrictable_type . ':' . $permission->restrictable_id . ':' . $permission->role_id . ':' . $permission->action;
            $isRestricted = $entityRestrictedMap[$permission->restrictable_type . ':' . $permission->restrictable_id];
            $permissionMap[$key] = $isRestricted;
        }

        // Create a mapping of role permissions
        $rolePermissionMap = [];
        foreach ($roles as $role) {
            foreach ($role->getRelationValue('permissions') as $permission) {
                $rolePermissionMap[$role->getRawAttribute('id') . ':' . $permission->getRawAttribute('name')] = true;
            }
        }

        // Create Joint Permission Data
        foreach ($entities as $entity) {
            foreach ($roles as $role) {
                foreach ($this->getActions($entity) as $action) {
                    $jointPermissions[] = $this->createJointPermissionData($entity, $role, $action, $permissionMap, $rolePermissionMap);
                }
            }
        }

        $this->db->transaction(function () use ($jointPermissions) {
            foreach (array_chunk($jointPermissions, 1000) as $jointPermissionChunk) {
                $this->db->table('joint_permissions')->insert($jointPermissionChunk);
            }
        });
    }


    /**
     * Get the actions related to an entity.
     * @param Entity $entity
     * @return array
     */
    protected function getActions(Entity $entity)
    {
        $baseActions = ['view', 'update', 'delete'];
        if ($entity->isA('chapter') || $entity->isA('book')) {
            $baseActions[] = 'page-create';
        }
        if ($entity->isA('book')) {
            $baseActions[] = 'chapter-create';
        }
        return $baseActions;
    }

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     * @param Entity $entity
     * @param Role $role
     * @param string $action
     * @param array $permissionMap
     * @param array $rolePermissionMap
     * @return array
     */
    protected function createJointPermissionData(Entity $entity, Role $role, $action, $permissionMap, $rolePermissionMap)
    {
        $permissionPrefix = (strpos($action, '-') === false ? ($entity->getType() . '-') : '') . $action;
        $roleHasPermission = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-all']);
        $roleHasPermissionOwn = isset($rolePermissionMap[$role->getRawAttribute('id') . ':' . $permissionPrefix . '-own']);
        $explodedAction = explode('-', $action);
        $restrictionAction = end($explodedAction);

        if ($role->system_name === 'admin') {
            return $this->createJointPermissionDataArray($entity, $role, $action, true, true);
        }

        if ($entity->restricted) {
            $hasAccess = $this->mapHasActiveRestriction($permissionMap, $entity, $role, $restrictionAction);
            return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
        }

        if ($entity->isA('book')) {
            return $this->createJointPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
        }

        // For chapters and pages, Check if explicit permissions are set on the Book.
        $book = $this->getBook($entity->book_id);
        $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $book, $role, $restrictionAction);
        $hasPermissiveAccessToParents = !$book->restricted;

        // For pages with a chapter, Check if explicit permissions are set on the Chapter
        if ($entity->isA('page') && $entity->chapter_id !== 0 && $entity->chapter_id !== '0') {
            $chapter = $this->getChapter($entity->chapter_id);
            $hasPermissiveAccessToParents = $hasPermissiveAccessToParents && !$chapter->restricted;
            if ($chapter->restricted) {
                $hasExplicitAccessToParents = $this->mapHasActiveRestriction($permissionMap, $chapter, $role, $restrictionAction);
            }
        }

        return $this->createJointPermissionDataArray(
            $entity,
            $role,
            $action,
            ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
            ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
        );
    }

    /**
     * Check for an active restriction in an entity map.
     * @param $entityMap
     * @param Entity $entity
     * @param Role $role
     * @param $action
     * @return bool
     */
    protected function mapHasActiveRestriction($entityMap, Entity $entity, Role $role, $action)
    {
        $key = $entity->getMorphClass() . ':' . $entity->getRawAttribute('id') . ':' . $role->getRawAttribute('id') . ':' . $action;
        return isset($entityMap[$key]) ? $entityMap[$key] : false;
    }

    /**
     * Create an array of data with the information of an entity jointPermissions.
     * Used to build data for bulk insertion.
     * @param Entity $entity
     * @param Role $role
     * @param $action
     * @param $permissionAll
     * @param $permissionOwn
     * @return array
     */
    protected function createJointPermissionDataArray(Entity $entity, Role $role, $action, $permissionAll, $permissionOwn)
    {
        return [
            'role_id'            => $role->getRawAttribute('id'),
            'entity_id'          => $entity->getRawAttribute('id'),
            'entity_type'        => $entity->getMorphClass(),
            'action'             => $action,
            'has_permission'     => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'created_by'         => $entity->getRawAttribute('created_by')
        ];
    }

    /**
     * Checks if an entity has a restriction set upon it.
     * @param Ownable $ownable
     * @param $permission
     * @return bool
     */
    public function checkOwnableUserAccess(Ownable $ownable, $permission)
    {
        if ($this->isAdmin()) {
            $this->clean();
            return true;
        }

        $explodedPermission = explode('-', $permission);

        $baseQuery = $ownable->where('id', '=', $ownable->id);
        $action = end($explodedPermission);
        $this->currentAction = $action;

        $nonJointPermissions = ['restrictions', 'image', 'attachment', 'comment'];

        // Handle non entity specific jointPermissions
        if (in_array($explodedPermission[0], $nonJointPermissions)) {
            $allPermission = $this->currentUser() && $this->currentUser()->can($permission . '-all');
            $ownPermission = $this->currentUser() && $this->currentUser()->can($permission . '-own');
            $this->currentAction = 'view';
            $isOwner = $this->currentUser() && $this->currentUser()->id === $ownable->created_by;
            return ($allPermission || ($isOwner && $ownPermission));
        }

        // Handle abnormal create jointPermissions
        if ($action === 'create') {
            $this->currentAction = $permission;
        }

        $q = $this->entityRestrictionQuery($baseQuery)->count() > 0;
        $this->clean();
        return $q;
    }

    /**
     * Check if an entity has restrictions set on itself or its
     * parent tree.
     * @param Entity $entity
     * @param $action
     * @return bool|mixed
     */
    public function checkIfRestrictionsSet(Entity $entity, $action)
    {
        $this->currentAction = $action;
        if ($entity->isA('page')) {
            return $entity->restricted || ($entity->chapter && $entity->chapter->restricted) || $entity->book->restricted;
        } elseif ($entity->isA('chapter')) {
            return $entity->restricted || $entity->book->restricted;
        } elseif ($entity->isA('book')) {
            return $entity->restricted;
        }
    }

    /**
     * The general query filter to remove all entities
     * that the current user does not have access to.
     * @param $query
     * @return mixed
     */
    protected function entityRestrictionQuery($query)
    {
        $q = $query->where(function ($parentQuery) {
            $parentQuery->whereHas('jointPermissions', function ($permissionQuery) {
                $permissionQuery->whereIn('role_id', $this->getRoles())
                    ->where('action', '=', $this->currentAction)
                    ->where(function ($query) {
                        $query->where('has_permission', '=', true)
                            ->orWhere(function ($query) {
                                $query->where('has_permission_own', '=', true)
                                    ->where('created_by', '=', $this->currentUser()->id);
                            });
                    });
            });
        });
        $this->clean();
        return $q;
    }

    /**
     * Get the children of a book in an efficient single query, Filtered by the permission system.
     * @param integer $book_id
     * @param bool $filterDrafts
     * @param bool $fetchPageContent
     * @return QueryBuilder
     */
    public function bookChildrenQuery($book_id, $filterDrafts = false, $fetchPageContent = false)
    {
        $pageSelect = $this->db->table('pages')->selectRaw($this->page->entityRawQuery($fetchPageContent))->where('book_id', '=', $book_id)->where(function ($query) use ($filterDrafts) {
            $query->where('draft', '=', 0);
            if (!$filterDrafts) {
                $query->orWhere(function ($query) {
                    $query->where('draft', '=', 1)->where('created_by', '=', $this->currentUser()->id);
                });
            }
        });
        $chapterSelect = $this->db->table('chapters')->selectRaw($this->chapter->entityRawQuery())->where('book_id', '=', $book_id);
        $query = $this->db->query()->select('*')->from($this->db->raw("({$pageSelect->toSql()} UNION {$chapterSelect->toSql()}) AS U"))
            ->mergeBindings($pageSelect)->mergeBindings($chapterSelect);

        if (!$this->isAdmin()) {
            $whereQuery = $this->db->table('joint_permissions as jp')->selectRaw('COUNT(*)')
                ->whereRaw('jp.entity_id=U.id')->whereRaw('jp.entity_type=U.entity_type')
                ->where('jp.action', '=', 'view')->whereIn('jp.role_id', $this->getRoles())
                ->where(function ($query) {
                    $query->where('jp.has_permission', '=', 1)->orWhere(function ($query) {
                        $query->where('jp.has_permission_own', '=', 1)->where('jp.created_by', '=', $this->currentUser()->id);
                    });
                });
            $query->whereRaw("({$whereQuery->toSql()}) > 0")->mergeBindings($whereQuery);
        }

        $query->orderBy('draft', 'desc')->orderBy('priority', 'asc');
        $this->clean();
        return  $query;
    }

    /**
     * Add restrictions for a generic entity
     * @param string $entityType
     * @param Builder|Entity $query
     * @param string $action
     * @return Builder
     */
    public function enforceEntityRestrictions($entityType, $query, $action = 'view')
    {
        if (strtolower($entityType) === 'page') {
            // Prevent drafts being visible to others.
            $query = $query->where(function ($query) {
                $query->where('draft', '=', false);
                if ($this->currentUser()) {
                    $query->orWhere(function ($query) {
                        $query->where('draft', '=', true)->where('created_by', '=', $this->currentUser()->id);
                    });
                }
            });
        }

        if ($this->isAdmin()) {
            $this->clean();
            return $query;
        }

        $this->currentAction = $action;
        return $this->entityRestrictionQuery($query);
    }

    /**
     * Filter items that have entities set as a polymorphic relation.
     * @param $query
     * @param string $tableName
     * @param string $entityIdColumn
     * @param string $entityTypeColumn
     * @param string $action
     * @return mixed
     */
    public function filterRestrictedEntityRelations($query, $tableName, $entityIdColumn, $entityTypeColumn, $action = 'view')
    {
        if ($this->isAdmin()) {
            $this->clean();
            return $query;
        }

        $this->currentAction = $action;
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];

        $q = $query->where(function ($query) use ($tableDetails) {
            $query->whereExists(function ($permissionQuery) use (&$tableDetails) {
                $permissionQuery->select('id')->from('joint_permissions')
                    ->whereRaw('joint_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                    ->whereRaw('joint_permissions.entity_type=' . $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                    ->where('action', '=', $this->currentAction)
                    ->whereIn('role_id', $this->getRoles())
                    ->where(function ($query) {
                        $query->where('has_permission', '=', true)->orWhere(function ($query) {
                            $query->where('has_permission_own', '=', true)
                                ->where('created_by', '=', $this->currentUser()->id);
                        });
                    });
            });
        });
        $this->clean();
        return $q;
    }

    /**
     * Filters pages that are a direct relation to another item.
     * @param $query
     * @param $tableName
     * @param $entityIdColumn
     * @return mixed
     */
    public function filterRelatedPages($query, $tableName, $entityIdColumn)
    {
        if ($this->isAdmin()) {
            $this->clean();
            return $query;
        }

        $this->currentAction = 'view';
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn];

        $q = $query->where(function ($query) use ($tableDetails) {
            $query->where(function ($query) use (&$tableDetails) {
                $query->whereExists(function ($permissionQuery) use (&$tableDetails) {
                    $permissionQuery->select('id')->from('joint_permissions')
                        ->whereRaw('joint_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where('entity_type', '=', 'Bookstack\\Page')
                        ->where('action', '=', $this->currentAction)
                        ->whereIn('role_id', $this->getRoles())
                        ->where(function ($query) {
                            $query->where('has_permission', '=', true)->orWhere(function ($query) {
                                $query->where('has_permission_own', '=', true)
                                    ->where('created_by', '=', $this->currentUser()->id);
                            });
                        });
                });
            })->orWhere($tableDetails['entityIdColumn'], '=', 0);
        });
        $this->clean();
        return $q;
    }

    /**
     * Check if the current user is an admin.
     * @return bool
     */
    private function isAdmin()
    {
        if ($this->isAdminUser === null) {
            $this->isAdminUser = ($this->currentUser()->id !== null) ? $this->currentUser()->hasSystemRole('admin') : false;
        }

        return $this->isAdminUser;
    }

    /**
     * Get the current user
     * @return User
     */
    private function currentUser()
    {
        if ($this->currentUserModel === false) {
            $this->currentUserModel = user();
        }

        return $this->currentUserModel;
    }

    /**
     * Clean the cached user elements.
     */
    private function clean()
    {
        $this->currentUserModel = false;
        $this->userRoles = false;
        $this->isAdminUser = null;
    }
}
