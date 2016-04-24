<?php namespace BookStack\Services;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\EntityPermission;
use BookStack\Page;
use BookStack\Role;
use Illuminate\Database\Eloquent\Collection;

class RestrictionService
{

    protected $userRoles;
    protected $isAdmin;
    protected $currentAction;
    protected $currentUser;

    public $book;
    public $chapter;
    public $page;

    protected $entityPermission;
    protected $role;

    /**
     * The actions that have permissions attached throughout the application.
     * @var array
     */
    protected $actions = ['view', 'create', 'update', 'delete'];

    /**
     * RestrictionService constructor.
     * @param EntityPermission $entityPermission
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param Role $role
     */
    public function __construct(EntityPermission $entityPermission, Book $book, Chapter $chapter, Page $page, Role $role)
    {
        $this->currentUser = auth()->user();
        $this->userRoles = $this->currentUser ? $this->currentUser->roles->pluck('id') : [];
        $this->isAdmin = $this->currentUser ? $this->currentUser->hasRole('admin') : false;

        $this->entityPermission = $entityPermission;
        $this->role = $role;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
    }

    /**
     * Re-generate all entity permission from scratch.
     */
    public function buildEntityPermissions()
    {
        $this->entityPermission->truncate();

        // Get all roles (Should be the most limited dimension)
        $roles = $this->role->load('permissions')->all();

        // Chunk through all books
        $this->book->chunk(500, function ($books) use ($roles) {
            $this->createManyEntityPermissions($books, $roles);
        });

        // Chunk through all chapters
        $this->chapter->with('book')->chunk(500, function ($books) use ($roles) {
            $this->createManyEntityPermissions($books, $roles);
        });

        // Chunk through all pages
        $this->page->with('book', 'chapter')->chunk(500, function ($books) use ($roles) {
            $this->createManyEntityPermissions($books, $roles);
        });
    }

    /**
     * Create the entity permissions for a particular entity.
     * @param Entity $entity
     */
    public function buildEntityPermissionsForEntity(Entity $entity)
    {
        $roles = $this->role->load('permissions')->all();
        $entities = collect([$entity]);

        if ($entity->isA('book')) {
            $entities = $entities->merge($entity->chapters);
            $entities = $entities->merge($entity->pages);
        } elseif ($entity->isA('chapter')) {
            $entities = $entities->merge($entity->pages);
        }

        $this->deleteManyEntityPermissionsForEntities($entities);
        $this->createManyEntityPermissions($entities, $roles);
    }

    /**
     * Build the entity permissions for a particular role.
     * @param Role $role
     */
    public function buildEntityPermissionForRole(Role $role)
    {
        $roles = collect([$role]);

        $this->deleteManyEntityPermissionsForRoles($roles);

        // Chunk through all books
        $this->book->chunk(500, function ($books) use ($roles) {
            $this->createManyEntityPermissions($books, $roles);
        });

        // Chunk through all chapters
        $this->chapter->with('book')->chunk(500, function ($books) use ($roles) {
            $this->createManyEntityPermissions($books, $roles);
        });

        // Chunk through all pages
        $this->page->with('book', 'chapter')->chunk(500, function ($books) use ($roles) {
            $this->createManyEntityPermissions($books, $roles);
        });
    }

    /**
     * Delete the entity permissions attached to a particular role.
     * @param Role $role
     */
    public function deleteEntityPermissionsForRole(Role $role)
    {
        $this->deleteManyEntityPermissionsForRoles([$role]);
    }

    /**
     * Delete all of the entity permissions for a list of entities.
     * @param Role[] $roles
     */
    protected function deleteManyEntityPermissionsForRoles($roles)
    {
        foreach ($roles as $role) {
            $role->entityPermissions()->delete();
        }
    }

    /**
     * Delete the entity permissions for a particular entity.
     * @param Entity $entity
     */
    public function deleteEntityPermissionsForEntity(Entity $entity)
    {
        $this->deleteManyEntityPermissionsForEntities([$entity]);
    }

    /**
     * Delete all of the entity permissions for a list of entities.
     * @param Entity[] $entities
     */
    protected function deleteManyEntityPermissionsForEntities($entities)
    {
        foreach ($entities as $entity) {
            $entity->permissions()->delete();
        }
    }

    /**
     * Create & Save entity permissions for many entities and permissions.
     * @param Collection $entities
     * @param Collection $roles
     */
    protected function createManyEntityPermissions($entities, $roles)
    {
        $entityPermissions = [];
        foreach ($entities as $entity) {
            foreach ($roles as $role) {
                foreach ($this->actions as $action) {
                    $entityPermissions[] = $this->createEntityPermissionData($entity, $role, $action);
                }
            }
        }
        \Log::info(collect($entityPermissions)->where('entity_id', 1)->where('entity_type', 'BookStack\\Page')->where('role_id', 2)->all());
        $this->entityPermission->insert($entityPermissions);
    }

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     * @param Entity $entity
     * @param Role $role
     * @param $action
     * @return array
     */
    protected function createEntityPermissionData(Entity $entity, Role $role, $action)
    {
        $permissionPrefix = $entity->getType() . '-' . $action;
        $roleHasPermission = $role->hasPermission($permissionPrefix . '-all');
        $roleHasPermissionOwn = $role->hasPermission($permissionPrefix . '-own');

        if ($entity->isA('book')) {

            if (!$entity->restricted) {
                return $this->createEntityPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
            } else {
                $hasAccess = $entity->hasActiveRestriction($role->id, $action);
                return $this->createEntityPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        } elseif ($entity->isA('chapter')) {

            if (!$entity->restricted) {
                $hasExplicitAccessToBook = $entity->book->hasActiveRestriction($role->id, $action);
                $hasPermissiveAccessToBook = !$entity->book->restricted;
                return $this->createEntityPermissionDataArray($entity, $role, $action,
                    ($hasExplicitAccessToBook || ($roleHasPermission && $hasPermissiveAccessToBook)),
                    ($hasExplicitAccessToBook || ($roleHasPermissionOwn && $hasPermissiveAccessToBook)));
            } else {
                $hasAccess = $entity->hasActiveRestriction($role->id, $action);
                return $this->createEntityPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        } elseif ($entity->isA('page')) {

            if (!$entity->restricted) {
                $hasExplicitAccessToBook = $entity->book->hasActiveRestriction($role->id, $action);
                $hasPermissiveAccessToBook = !$entity->book->restricted;
                $hasExplicitAccessToChapter = $entity->chapter && $entity->chapter->hasActiveRestriction($role->id, $action);
                $hasPermissiveAccessToChapter = $entity->chapter && !$entity->chapter->restricted;
                $acknowledgeChapter = ($entity->chapter && $entity->chapter->restricted);

                $hasExplicitAccessToParents = $acknowledgeChapter ? $hasExplicitAccessToChapter : $hasExplicitAccessToBook;
                $hasPermissiveAccessToParents = $acknowledgeChapter ? $hasPermissiveAccessToChapter : $hasPermissiveAccessToBook;

                return $this->createEntityPermissionDataArray($entity, $role, $action,
                    ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
                    ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
                );
            } else {
                $hasAccess = $entity->hasRestriction($role->id, $action);
                return $this->createEntityPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        }
    }

    /**
     * Create an array of data with the information of an entity permissions.
     * Used to build data for bulk insertion.
     * @param Entity $entity
     * @param Role $role
     * @param $action
     * @param $permissionAll
     * @param $permissionOwn
     * @return array
     */
    protected function createEntityPermissionDataArray(Entity $entity, Role $role, $action, $permissionAll, $permissionOwn)
    {
        $entityClass = get_class($entity);
        return [
            'role_id'            => $role->id,
            'entity_id'          => $entity->id,
            'entity_type'        => $entityClass,
            'action'             => $action,
            'has_permission'     => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'created_by'         => $entity->created_by
        ];
    }

    /**
     * Checks if an entity has a restriction set upon it.
     * @param Entity $entity
     * @param $permission
     * @return bool
     */
    public function checkEntityUserAccess(Entity $entity, $permission)
    {
        if ($this->isAdmin) return true;
        $explodedPermission = explode('-', $permission);

        $baseQuery = $entity->where('id', '=', $entity->id);

        $nonEntityPermissions = ['restrictions'];

        // Handle non entity specific permissions
        if (in_array($explodedPermission[0], $nonEntityPermissions)) {
            $allPermission = $this->currentUser && $this->currentUser->can($permission . '-all');
            $ownPermission = $this->currentUser && $this->currentUser->can($permission . '-own');
            $this->currentAction = 'view';
            $isOwner = $this->currentUser && $this->currentUser->id === $entity->created_by;
            return ($allPermission || ($isOwner && $ownPermission));
        }

        $action = end($explodedPermission);
        $this->currentAction = $action;
        return $this->entityRestrictionQuery($baseQuery)->count() > 0;
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
        return $query->where(function ($parentQuery) {
            $parentQuery->whereHas('permissions', function ($permissionQuery) {
                $permissionQuery->whereIn('role_id', $this->userRoles)
                    ->where('action', '=', $this->currentAction)
                    ->where(function ($query) {
                        $query->where('has_permission', '=', true)
                            ->orWhere(function ($query) {
                                $query->where('has_permission_own', '=', true)
                                    ->where('created_by', '=', $this->currentUser->id);
                            });
                    });
            });
        });
    }

    /**
     * Add restrictions for a page query
     * @param $query
     * @param string $action
     * @return mixed
     */
    public function enforcePageRestrictions($query, $action = 'view')
    {
        // Prevent drafts being visible to others.
        $query = $query->where(function ($query) {
            $query->where('draft', '=', false);
            if ($this->currentUser) {
                $query->orWhere(function ($query) {
                    $query->where('draft', '=', true)->where('created_by', '=', $this->currentUser->id);
                });
            }
        });

        if ($this->isAdmin) return $query;
        $this->currentAction = $action;
        return $this->entityRestrictionQuery($query);
    }

    /**
     * Add on permission restrictions to a chapter query.
     * @param $query
     * @param string $action
     * @return mixed
     */
    public function enforceChapterRestrictions($query, $action = 'view')
    {
        if ($this->isAdmin) return $query;
        $this->currentAction = $action;
        return $this->entityRestrictionQuery($query);
    }

    /**
     * Add restrictions to a book query.
     * @param $query
     * @param string $action
     * @return mixed
     */
    public function enforceBookRestrictions($query, $action = 'view')
    {
        if ($this->isAdmin) return $query;
        $this->currentAction = $action;
        return $this->entityRestrictionQuery($query);
    }

    /**
     * Filter items that have entities set a a polymorphic relation.
     * @param $query
     * @param string $tableName
     * @param string $entityIdColumn
     * @param string $entityTypeColumn
     * @return mixed
     */
    public function filterRestrictedEntityRelations($query, $tableName, $entityIdColumn, $entityTypeColumn)
    {
        if ($this->isAdmin) return $query;
        $this->currentAction = 'view';
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];

        return $query->where(function ($query) use ($tableDetails) {
            $query->whereExists(function ($permissionQuery) use (&$tableDetails) {
                $permissionQuery->select('id')->from('entity_permissions')
                    ->whereRaw('entity_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                    ->whereRaw('entity_permissions.entity_type=' . $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                    ->where('action', '=', $this->currentAction)
                    ->whereIn('role_id', $this->userRoles)
                    ->where(function ($query) {
                        $query->where('has_permission', '=', true)->orWhere(function ($query) {
                            $query->where('has_permission_own', '=', true)
                                ->where('created_by', '=', $this->currentUser->id);
                        });
                    });
            });
        });

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
        if ($this->isAdmin) return $query;
        $this->currentAction = 'view';
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn];

        return $query->where(function ($query) use ($tableDetails) {
            $query->where(function ($query) use (&$tableDetails) {
                $query->whereExists(function ($permissionQuery) use (&$tableDetails) {
                    $permissionQuery->select('id')->from('entity_permissions')
                        ->whereRaw('entity_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where('entity_type', '=', 'Bookstack\\Page')
                        ->where('action', '=', $this->currentAction)
                        ->whereIn('role_id', $this->userRoles)
                        ->where(function ($query) {
                            $query->where('has_permission', '=', true)->orWhere(function ($query) {
                                $query->where('has_permission_own', '=', true)
                                    ->where('created_by', '=', $this->currentUser ? $this->currentUser->id : 0);
                            });
                        });
                });
            })->orWhere($tableDetails['entityIdColumn'], '=', 0);
        });
    }

}