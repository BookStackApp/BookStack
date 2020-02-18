<?php namespace BookStack\Auth\Permissions;

use BookStack\Auth\Permissions;
use BookStack\Auth\Role;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Entity;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Page;
use BookStack\Ownable;
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

    /**
     * @var Connection
     */
    protected $db;

    /**
     * @var JointPermission
     */
    protected $jointPermission;

    /**
     * @var Role
     */
    protected $role;

    /**
     * @var EntityPermission
     */
    protected $entityPermission;

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

    protected $entityCache;

    /**
     * PermissionService constructor.
     * @param JointPermission $jointPermission
     * @param EntityPermission $entityPermission
     * @param Role $role
     * @param Connection $db
     * @param EntityProvider $entityProvider
     */
    public function __construct(
        JointPermission $jointPermission,
        Permissions\EntityPermission $entityPermission,
        Role $role,
        Connection $db,
        EntityProvider $entityProvider
    ) {
        $this->db = $db;
        $this->jointPermission = $jointPermission;
        $this->entityPermission = $entityPermission;
        $this->role = $role;
        $this->entityProvider = $entityProvider;
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
     * @param \BookStack\Entities\Entity[] $entities
     */
    protected function readyEntityCache($entities = [])
    {
        $this->entityCache = [];

        foreach ($entities as $entity) {
            $type = $entity->getType();
            if (!isset($this->entityCache[$type])) {
                $this->entityCache[$type] = collect();
            }
            $this->entityCache[$type]->put($entity->id, $entity);
        }
    }

    /**
     * Get a book via ID, Checks local cache
     * @param $bookId
     * @return Book
     */
    protected function getBook($bookId)
    {
        if (isset($this->entityCache['book']) && $this->entityCache['book']->has($bookId)) {
            return $this->entityCache['book']->get($bookId);
        }

        $book = $this->entityProvider->book->find($bookId);
        if ($book === null) {
            $book = false;
        }

        return $book;
    }

    /**
     * Get a chapter via ID, Checks local cache
     * @param $chapterId
     * @return \BookStack\Entities\Book
     */
    protected function getChapter($chapterId)
    {
        if (isset($this->entityCache['chapter']) && $this->entityCache['chapter']->has($chapterId)) {
            return $this->entityCache['chapter']->get($chapterId);
        }

        $chapter = $this->entityProvider->chapter->find($chapterId);
        if ($chapter === null) {
            $chapter = false;
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

        // Chunk through all bookshelves
        $this->entityProvider->bookshelf->newQuery()->select(['id', 'restricted', 'created_by'])
            ->chunk(50, function ($shelves) use ($roles) {
                $this->buildJointPermissionsForShelves($shelves, $roles);
            });
    }

    /**
     * Get a query for fetching a book with it's children.
     * @return QueryBuilder
     */
    protected function bookFetchQuery()
    {
        return $this->entityProvider->book->newQuery()
            ->select(['id', 'restricted', 'created_by'])->with(['chapters' => function ($query) {
                $query->select(['id', 'restricted', 'created_by', 'book_id']);
            }, 'pages'  => function ($query) {
                $query->select(['id', 'restricted', 'created_by', 'book_id', 'chapter_id']);
            }]);
    }

    /**
     * @param Collection $shelves
     * @param array $roles
     * @param bool $deleteOld
     * @throws \Throwable
     */
    protected function buildJointPermissionsForShelves($shelves, $roles, $deleteOld = false)
    {
        if ($deleteOld) {
            $this->deleteManyJointPermissionsForEntities($shelves->all());
        }
        $this->createManyJointPermissions($shelves, $roles);
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
     * @param \BookStack\Entities\Entity $entity
     * @throws \Throwable
     */
    public function buildJointPermissionsForEntity(Entity $entity)
    {
        $entities = [$entity];
        if ($entity->isA('book')) {
            $books = $this->bookFetchQuery()->where('id', '=', $entity->id)->get();
            $this->buildJointPermissionsForBooks($books, $this->role->newQuery()->get(), true);
            return;
        }

        if ($entity->book) {
            $entities[] = $entity->book;
        }

        if ($entity->isA('page') && $entity->chapter_id) {
            $entities[] = $entity->chapter;
        }

        if ($entity->isA('chapter')) {
            foreach ($entity->pages as $page) {
                $entities[] = $page;
            }
        }

        $this->buildJointPermissionsForEntities(collect($entities));
    }

    /**
     * Rebuild the entity jointPermissions for a collection of entities.
     * @param Collection $entities
     * @throws \Throwable
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
        $this->bookFetchQuery()->chunk(20, function ($books) use ($roles) {
            $this->buildJointPermissionsForBooks($books, $roles);
        });

        // Chunk through all bookshelves
        $this->entityProvider->bookshelf->newQuery()->select(['id', 'restricted', 'created_by'])
            ->chunk(50, function ($shelves) use ($roles) {
                $this->buildJointPermissionsForShelves($shelves, $roles);
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
     * @throws \Throwable
     */
    public function deleteJointPermissionsForEntity(Entity $entity)
    {
        $this->deleteManyJointPermissionsForEntities([$entity]);
    }

    /**
     * Delete all of the entity jointPermissions for a list of entities.
     * @param \BookStack\Entities\Entity[] $entities
     * @throws \Throwable
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
     * @throws \Throwable
     */
    protected function createManyJointPermissions($entities, $roles)
    {
        $this->readyEntityCache($entities);
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
            foreach ($role->permissions as $permission) {
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
     * @param \BookStack\Entities\Entity $entity
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
        if ($entity->isA('page')) {
            $baseActions[] = 'editdraft';
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

        if ($entity->isA('book') || $entity->isA('bookshelf')) {
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
     * @param \BookStack\Entities\Entity $entity
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
     * Checks if a user has the given permission for any items in the system.
     * Can be passed an entity instance to filter on a specific type.
     * @param string $permission
     * @param string $entityClass
     * @return bool
     */
    public function checkUserHasPermissionOnAnything(string $permission, string $entityClass = null)
    {
        $userRoleIds = $this->currentUser()->roles()->select('id')->pluck('id')->toArray();
        $userId = $this->currentUser()->id;

        $permissionQuery = $this->db->table('joint_permissions')
            ->where('action', '=', $permission)
            ->whereIn('role_id', $userRoleIds)
            ->where(function ($query) use ($userId) {
                $query->where('has_permission', '=', 1)
                    ->orWhere(function ($query2) use ($userId) {
                        $query2->where('has_permission_own', '=', 1)
                            ->where('created_by', '=', $userId);
                    });
            });

        if (!is_null($entityClass)) {
            $entityInstance = app()->make($entityClass);
            $permissionQuery = $permissionQuery->where('entity_type', '=', $entityInstance->getMorphClass());
        }

        $hasPermission = $permissionQuery->count() > 0;
        $this->clean();
        return $hasPermission;
    }

    /**
     * Check if an entity has restrictions set on itself or its
     * parent tree.
     * @param \BookStack\Entities\Entity $entity
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
     * Limited the given entity query so that the query will only
     * return items that the user has permission for the given ability.
     */
    public function restrictEntityQuery(Builder $query, string $ability = 'view'): Builder
    {
        $this->clean();
        return $query->where(function (Builder $parentQuery) use ($ability) {
            $parentQuery->whereHas('jointPermissions', function (Builder $permissionQuery) use ($ability) {
                $permissionQuery->whereIn('role_id', $this->getRoles())
                    ->where('action', '=', $ability)
                    ->where(function (Builder $query) {
                        $query->where('has_permission', '=', true)
                            ->orWhere(function (Builder $query) {
                                $query->where('has_permission_own', '=', true)
                                    ->where('created_by', '=', $this->currentUser()->id);
                            });
                    });
            });
        });
    }

    /**
     * Extend the given page query to ensure draft items are not visible
     * unless created by the given user, or editable by the given user if
     * app-shared-drafts setting is enabled.
     */
    public function enforceDraftVisiblityOnQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where('draft', '=', false)->orWhere(function (Builder $query) {
                if (setting('app-shared-drafts')) {
                    $this->clean();
                    $query->where(function (Builder $parentQuery) {
                        $parentQuery->whereHas('jointPermissions', function (Builder $permissionQuery) {
                            $permissionQuery->whereIn('role_id', $this->getRoles())->where(function (Builder $query) {
                                $query->where(function (Builder $query) {
                                    $query->where('action', '=', 'editdraft')->where('has_permission', '=', true);
                                })->orWhere(function (Builder $query) {
                                    $query->where('action', '=', 'update')->where(function (Builder $query) {
                                        $query->where('has_permission', '=', true)->orWhere(function (Builder $query) {
                                            $query->where('has_permission_own', '=', true)->where('created_by', '=', $this->currentUser()->id);
                                        });
                                    });
                                });
                            });
                        });
                    });
                } else {
                    $query->where('created_by', '=', $this->currentUser()->id);
                }
            });
        });
    }

    /**
     * Add restrictions for a generic entity
     * @param string $entityType
     * @param Builder|\BookStack\Entities\Entity $query
     * @param string $action
     * @return Builder
     */
    public function enforceEntityRestrictions($entityType, $query, $action = 'view')
    {
        if (strtolower($entityType) === 'page') {
            // Prevent drafts being visible to others.
            $query = $this->enforceDraftVisiblityOnQuery($query);
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
     * @return QueryBuilder
     */
    public function filterRestrictedEntityRelations($query, $tableName, $entityIdColumn, $entityTypeColumn, $action = 'view')
    {

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
     * Add conditions to a query to filter the selection to related entities
     * where permissions are granted.
     * @param $entityType
     * @param $query
     * @param $tableName
     * @param $entityIdColumn
     * @return mixed
     */
    public function filterRelatedEntity($entityType, $query, $tableName, $entityIdColumn)
    {
        $this->currentAction = 'view';
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn];

        $pageMorphClass = $this->entityProvider->get($entityType)->getMorphClass();

        $q = $query->where(function ($query) use ($tableDetails, $pageMorphClass) {
            $query->where(function ($query) use (&$tableDetails, $pageMorphClass) {
                $query->whereExists(function ($permissionQuery) use (&$tableDetails, $pageMorphClass) {
                    $permissionQuery->select('id')->from('joint_permissions')
                        ->whereRaw('joint_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where('entity_type', '=', $pageMorphClass)
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
     * Get the current user
     * @return \BookStack\Auth\User
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
