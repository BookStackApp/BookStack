<?php namespace BookStack\Services;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\JointPermission;
use BookStack\Page;
use BookStack\Role;
use BookStack\User;
use Illuminate\Database\Eloquent\Collection;

class PermissionService
{

    protected $userRoles;
    protected $isAdmin;
    protected $currentAction;
    protected $currentUser;

    public $book;
    public $chapter;
    public $page;

    protected $jointPermission;
    protected $role;

    /**
     * PermissionService constructor.
     * @param JointPermission $jointPermission
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param Role $role
     */
    public function __construct(JointPermission $jointPermission, Book $book, Chapter $chapter, Page $page, Role $role)
    {
        $this->currentUser = auth()->user();
        $userSet = $this->currentUser !== null;
        $this->userRoles = false;
        $this->isAdmin = $userSet ? $this->currentUser->hasRole('admin') : false;
        if (!$userSet) $this->currentUser = new User();

        $this->jointPermission = $jointPermission;
        $this->role = $role;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
    }

    /**
     * Get the roles for the current user;
     * @return array|bool
     */
    protected function getRoles()
    {
        if ($this->userRoles !== false) return $this->userRoles;

        $roles = [];

        if (auth()->guest()) {
            $roles[] = $this->role->getSystemRole('public')->id;
            return $roles;
        }


        foreach ($this->currentUser->roles as $role) {
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

        // Get all roles (Should be the most limited dimension)
        $roles = $this->role->with('permissions')->get();

        // Chunk through all books
        $this->book->with('permissions')->chunk(500, function ($books) use ($roles) {
            $this->createManyJointPermissions($books, $roles);
        });

        // Chunk through all chapters
        $this->chapter->with('book', 'permissions')->chunk(500, function ($chapters) use ($roles) {
            $this->createManyJointPermissions($chapters, $roles);
        });

        // Chunk through all pages
        $this->page->with('book', 'chapter', 'permissions')->chunk(500, function ($pages) use ($roles) {
            $this->createManyJointPermissions($pages, $roles);
        });
    }

    /**
     * Create the entity jointPermissions for a particular entity.
     * @param Entity $entity
     */
    public function buildJointPermissionsForEntity(Entity $entity)
    {
        $roles = $this->role->with('jointPermissions')->get();
        $entities = collect([$entity]);

        if ($entity->isA('book')) {
            $entities = $entities->merge($entity->chapters);
            $entities = $entities->merge($entity->pages);
        } elseif ($entity->isA('chapter')) {
            $entities = $entities->merge($entity->pages);
        }

        $this->deleteManyJointPermissionsForEntities($entities);
        $this->createManyJointPermissions($entities, $roles);
    }

    /**
     * Build the entity jointPermissions for a particular role.
     * @param Role $role
     */
    public function buildJointPermissionForRole(Role $role)
    {
        $roles = collect([$role]);

        $this->deleteManyJointPermissionsForRoles($roles);

        // Chunk through all books
        $this->book->with('permissions')->chunk(500, function ($books) use ($roles) {
            $this->createManyJointPermissions($books, $roles);
        });

        // Chunk through all chapters
        $this->chapter->with('book', 'permissions')->chunk(500, function ($books) use ($roles) {
            $this->createManyJointPermissions($books, $roles);
        });

        // Chunk through all pages
        $this->page->with('book', 'chapter', 'permissions')->chunk(500, function ($books) use ($roles) {
            $this->createManyJointPermissions($books, $roles);
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
        foreach ($roles as $role) {
            $role->jointPermissions()->delete();
        }
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
        foreach ($entities as $entity) {
            $entity->jointPermissions()->delete();
        }
    }

    /**
     * Create & Save entity jointPermissions for many entities and jointPermissions.
     * @param Collection $entities
     * @param Collection $roles
     */
    protected function createManyJointPermissions($entities, $roles)
    {
        $jointPermissions = [];
        foreach ($entities as $entity) {
            foreach ($roles as $role) {
                foreach ($this->getActions($entity) as $action) {
                    $jointPermissions[] = $this->createJointPermissionData($entity, $role, $action);
                }
            }
        }
        $this->jointPermission->insert($jointPermissions);
    }


    /**
     * Get the actions related to an entity.
     * @param $entity
     * @return array
     */
    protected function getActions($entity)
    {
        $baseActions = ['view', 'update', 'delete'];

        if ($entity->isA('chapter')) {
            $baseActions[] = 'page-create';
        } else if ($entity->isA('book')) {
            $baseActions[] = 'page-create';
            $baseActions[] = 'chapter-create';
        }

         return $baseActions;
    }

    /**
     * Create entity permission data for an entity and role
     * for a particular action.
     * @param Entity $entity
     * @param Role $role
     * @param $action
     * @return array
     */
    protected function createJointPermissionData(Entity $entity, Role $role, $action)
    {
        $permissionPrefix = (strpos($action, '-') === false ? ($entity->getType() . '-') : '') . $action;
        $roleHasPermission = $role->hasPermission($permissionPrefix . '-all');
        $roleHasPermissionOwn = $role->hasPermission($permissionPrefix . '-own');
        $explodedAction = explode('-', $action);
        $restrictionAction = end($explodedAction);

        if ($entity->isA('book')) {

            if (!$entity->restricted) {
                return $this->createJointPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
            } else {
                $hasAccess = $entity->hasActiveRestriction($role->id, $restrictionAction);
                return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        } elseif ($entity->isA('chapter')) {

            if (!$entity->restricted) {
                $hasExplicitAccessToBook = $entity->book->hasActiveRestriction($role->id, $restrictionAction);
                $hasPermissiveAccessToBook = !$entity->book->restricted;
                return $this->createJointPermissionDataArray($entity, $role, $action,
                    ($hasExplicitAccessToBook || ($roleHasPermission && $hasPermissiveAccessToBook)),
                    ($hasExplicitAccessToBook || ($roleHasPermissionOwn && $hasPermissiveAccessToBook)));
            } else {
                $hasAccess = $entity->hasActiveRestriction($role->id, $restrictionAction);
                return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        } elseif ($entity->isA('page')) {

            if (!$entity->restricted) {
                $hasExplicitAccessToBook = $entity->book->hasActiveRestriction($role->id, $restrictionAction);
                $hasPermissiveAccessToBook = !$entity->book->restricted;
                $hasExplicitAccessToChapter = $entity->chapter && $entity->chapter->hasActiveRestriction($role->id, $restrictionAction);
                $hasPermissiveAccessToChapter = $entity->chapter && !$entity->chapter->restricted;
                $acknowledgeChapter = ($entity->chapter && $entity->chapter->restricted);

                $hasExplicitAccessToParents = $acknowledgeChapter ? $hasExplicitAccessToChapter : $hasExplicitAccessToBook;
                $hasPermissiveAccessToParents = $acknowledgeChapter ? $hasPermissiveAccessToChapter : $hasPermissiveAccessToBook;

                return $this->createJointPermissionDataArray($entity, $role, $action,
                    ($hasExplicitAccessToParents || ($roleHasPermission && $hasPermissiveAccessToParents)),
                    ($hasExplicitAccessToParents || ($roleHasPermissionOwn && $hasPermissiveAccessToParents))
                );
            } else {
                $hasAccess = $entity->hasRestriction($role->id, $action);
                return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        }
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
        $entityClass = get_class($entity);
        return [
            'role_id'            => $role->getRawAttribute('id'),
            'entity_id'          => $entity->getRawAttribute('id'),
            'entity_type'        => $entityClass,
            'action'             => $action,
            'has_permission'     => $permissionAll,
            'has_permission_own' => $permissionOwn,
            'created_by'         => $entity->getRawAttribute('created_by')
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
        $action = end($explodedPermission);
        $this->currentAction = $action;

        $nonJointPermissions = ['restrictions'];

        // Handle non entity specific jointPermissions
        if (in_array($explodedPermission[0], $nonJointPermissions)) {
            $allPermission = $this->currentUser && $this->currentUser->can($permission . '-all');
            $ownPermission = $this->currentUser && $this->currentUser->can($permission . '-own');
            $this->currentAction = 'view';
            $isOwner = $this->currentUser && $this->currentUser->id === $entity->created_by;
            return ($allPermission || ($isOwner && $ownPermission));
        }

        // Handle abnormal create jointPermissions
        if ($action === 'create') {
            $this->currentAction = $permission;
        }


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
            $parentQuery->whereHas('jointPermissions', function ($permissionQuery) {
                $permissionQuery->whereIn('role_id', $this->getRoles())
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

        return $this->enforceEntityRestrictions($query, $action);
    }

    /**
     * Add on permission restrictions to a chapter query.
     * @param $query
     * @param string $action
     * @return mixed
     */
    public function enforceChapterRestrictions($query, $action = 'view')
    {
        return $this->enforceEntityRestrictions($query, $action);
    }

    /**
     * Add restrictions to a book query.
     * @param $query
     * @param string $action
     * @return mixed
     */
    public function enforceBookRestrictions($query, $action = 'view')
    {
        $this->enforceEntityRestrictions($query, $action);
    }

    /**
     * Add restrictions for a generic entity
     * @param $query
     * @param string $action
     * @return mixed
     */
    public function enforceEntityRestrictions($query, $action = 'view')
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
                $permissionQuery->select('id')->from('joint_permissions')
                    ->whereRaw('joint_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                    ->whereRaw('joint_permissions.entity_type=' . $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                    ->where('action', '=', $this->currentAction)
                    ->whereIn('role_id', $this->getRoles())
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
                    $permissionQuery->select('id')->from('joint_permissions')
                        ->whereRaw('joint_permissions.entity_id=' . $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where('entity_type', '=', 'Bookstack\\Page')
                        ->where('action', '=', $this->currentAction)
                        ->whereIn('role_id', $this->getRoles())
                        ->where(function ($query) {
                            $query->where('has_permission', '=', true)->orWhere(function ($query) {
                                $query->where('has_permission_own', '=', true)
                                    ->where('created_by', '=', $this->currentUser->id);
                            });
                        });
                });
            })->orWhere($tableDetails['entityIdColumn'], '=', 0);
        });
    }

}