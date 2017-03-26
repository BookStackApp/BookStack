<?php namespace BookStack\Services;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\JointPermission;
use BookStack\Ownable;
use BookStack\Page;
use BookStack\Role;
use BookStack\User;
use Illuminate\Database\Connection;
use Illuminate\Database\Eloquent\Builder;
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

    protected $entityCache;

    /**
     * PermissionService constructor.
     * @param JointPermission $jointPermission
     * @param Connection $db
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param Role $role
     */
    public function __construct(JointPermission $jointPermission, Connection $db, Book $book, Chapter $chapter, Page $page, Role $role)
    {
        $this->db = $db;
        $this->jointPermission = $jointPermission;
        $this->role = $role;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        // TODO - Update so admin still goes through filters
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
        if ($book === null) $book = false;
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
        if ($chapter === null) $chapter = false;
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
        if ($this->userRoles !== false) return $this->userRoles;

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
     * Rebuild the entity jointPermissions for a particular entity.
     * @param Entity $entity
     */
    public function buildJointPermissionsForEntity(Entity $entity)
    {
        $roles = $this->role->get();
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
     * Rebuild the entity jointPermissions for a collection of entities.
     * @param Collection $entities
     */
    public function buildJointPermissionsForEntities(Collection $entities)
    {
        $roles = $this->role->get();
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
        if (count($entities) === 0) return;
        $query = $this->jointPermission->newQuery();
            foreach ($entities as $entity) {
                $query->orWhere(function($query) use ($entity) {
                    $query->where('entity_id', '=', $entity->id)
                        ->where('entity_type', '=', $entity->getMorphClass());
                });
            }
        $query->delete();
    }

    /**
     * Create & Save entity jointPermissions for many entities and jointPermissions.
     * @param Collection $entities
     * @param Collection $roles
     */
    protected function createManyJointPermissions($entities, $roles)
    {
        $this->readyEntityCache();
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

        if ($role->system_name === 'admin') {
            return $this->createJointPermissionDataArray($entity, $role, $action, true, true);
        }

        if ($entity->isA('book')) {

            if (!$entity->restricted) {
                return $this->createJointPermissionDataArray($entity, $role, $action, $roleHasPermission, $roleHasPermissionOwn);
            } else {
                $hasAccess = $entity->hasActiveRestriction($role->id, $restrictionAction);
                return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        } elseif ($entity->isA('chapter')) {

            if (!$entity->restricted) {
                $book = $this->getBook($entity->book_id);
                $hasExplicitAccessToBook = $book->hasActiveRestriction($role->id, $restrictionAction);
                $hasPermissiveAccessToBook = !$book->restricted;
                return $this->createJointPermissionDataArray($entity, $role, $action,
                    ($hasExplicitAccessToBook || ($roleHasPermission && $hasPermissiveAccessToBook)),
                    ($hasExplicitAccessToBook || ($roleHasPermissionOwn && $hasPermissiveAccessToBook)));
            } else {
                $hasAccess = $entity->hasActiveRestriction($role->id, $restrictionAction);
                return $this->createJointPermissionDataArray($entity, $role, $action, $hasAccess, $hasAccess);
            }

        } elseif ($entity->isA('page')) {

            if (!$entity->restricted) {
                $book = $this->getBook($entity->book_id);
                $hasExplicitAccessToBook = $book->hasActiveRestriction($role->id, $restrictionAction);
                $hasPermissiveAccessToBook = !$book->restricted;

                $chapter = $this->getChapter($entity->chapter_id);
                $hasExplicitAccessToChapter = $chapter && $chapter->hasActiveRestriction($role->id, $restrictionAction);
                $hasPermissiveAccessToChapter = $chapter && !$chapter->restricted;
                $acknowledgeChapter = ($chapter && $chapter->restricted);

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

        $nonJointPermissions = ['restrictions', 'image', 'attachment'];

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
     * @return \Illuminate\Database\Query\Builder
     */
    public function bookChildrenQuery($book_id, $filterDrafts = false, $fetchPageContent = false) {
        $pageSelect = $this->db->table('pages')->selectRaw($this->page->entityRawQuery($fetchPageContent))->where('book_id', '=', $book_id)->where(function($query) use ($filterDrafts) {
            $query->where('draft', '=', 0);
            if (!$filterDrafts) {
                $query->orWhere(function($query) {
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
                ->where(function($query) {
                    $query->where('jp.has_permission', '=', 1)->orWhere(function($query) {
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
     * @return mixed
     */
    public function filterRestrictedEntityRelations($query, $tableName, $entityIdColumn, $entityTypeColumn)
    {
        if ($this->isAdmin()) {
            $this->clean();
            return $query;
        }

        $this->currentAction = 'view';
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