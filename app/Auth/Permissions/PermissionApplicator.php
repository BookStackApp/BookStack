<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;
use BookStack\Traits\HasOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;

class PermissionApplicator
{
    /**
     * @var ?array<int>
     */
    protected $userRoles = null;

    /**
     * @var ?User
     */
    protected $currentUserModel = null;

    /**
     * Get the roles for the current logged in user.
     */
    protected function getCurrentUserRoles(): array
    {
        if (!is_null($this->userRoles)) {
            return $this->userRoles;
        }

        if (auth()->guest()) {
            $this->userRoles = [Role::getSystemRole('public')->id];
        } else {
            $this->userRoles = $this->currentUser()->roles->pluck('id')->values()->all();
        }

        return $this->userRoles;
    }

    /**
     * Checks if an entity has a restriction set upon it.
     *
     * @param HasCreatorAndUpdater|HasOwner $ownable
     */
    public function checkOwnableUserAccess(Model $ownable, string $permission): bool
    {
        $explodedPermission = explode('-', $permission);

        $baseQuery = $ownable->newQuery()->where('id', '=', $ownable->id);
        $action = end($explodedPermission);
        $user = $this->currentUser();

        $nonJointPermissions = ['restrictions', 'image', 'attachment', 'comment'];

        // Handle non entity specific jointPermissions
        if (in_array($explodedPermission[0], $nonJointPermissions)) {
            $allPermission = $user && $user->can($permission . '-all');
            $ownPermission = $user && $user->can($permission . '-own');
            $ownerField = ($ownable instanceof Entity) ? 'owned_by' : 'created_by';
            $isOwner = $user && $user->id === $ownable->$ownerField;

            return $allPermission || ($isOwner && $ownPermission);
        }

        // Handle abnormal create jointPermissions
        if ($action === 'create') {
            $action = $permission;
        }

        $hasAccess = $this->entityRestrictionQuery($baseQuery, $action)->count() > 0;
        $this->clean();

        return $hasAccess;
    }

    /**
     * Checks if a user has the given permission for any items in the system.
     * Can be passed an entity instance to filter on a specific type.
     */
    public function checkUserHasPermissionOnAnything(string $permission, ?string $entityClass = null): bool
    {
        $userRoleIds = $this->currentUser()->roles()->select('id')->pluck('id')->toArray();
        $userId = $this->currentUser()->id;

        $permissionQuery = JointPermission::query()
            ->where('action', '=', $permission)
            ->whereIn('role_id', $userRoleIds)
            ->where(function (Builder $query) use ($userId) {
                $this->addJointHasPermissionCheck($query, $userId);
            });

        if (!is_null($entityClass)) {
            $entityInstance = app($entityClass);
            $permissionQuery = $permissionQuery->where('entity_type', '=', $entityInstance->getMorphClass());
        }

        $hasPermission = $permissionQuery->count() > 0;
        $this->clean();

        return $hasPermission;
    }

    /**
     * The general query filter to remove all entities
     * that the current user does not have access to.
     */
    protected function entityRestrictionQuery(Builder $query, string $action): Builder
    {
        $q = $query->where(function ($parentQuery) use ($action) {
            $parentQuery->whereHas('jointPermissions', function ($permissionQuery) use ($action) {
                $permissionQuery->whereIn('role_id', $this->getCurrentUserRoles())
                    ->where('action', '=', $action)
                    ->where(function (Builder $query) {
                        $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
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
                $permissionQuery->whereIn('role_id', $this->getCurrentUserRoles())
                    ->where('action', '=', $ability)
                    ->where(function (Builder $query) {
                        $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                    });
            });
        });
    }

    /**
     * Extend the given page query to ensure draft items are not visible
     * unless created by the given user.
     */
    public function enforceDraftVisibilityOnQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $query) {
            $query->where('draft', '=', false)
                ->orWhere(function (Builder $query) {
                    $query->where('draft', '=', true)
                        ->where('owned_by', '=', $this->currentUser()->id);
                });
        });
    }

    /**
     * Add restrictions for a generic entity.
     */
    public function enforceEntityRestrictions(Entity $entity, Builder $query, string $action = 'view'): Builder
    {
        if ($entity instanceof Page) {
            // Prevent drafts being visible to others.
            $this->enforceDraftVisibilityOnQuery($query);
        }

        return $this->entityRestrictionQuery($query, $action);
    }

    /**
     * Filter items that have entities set as a polymorphic relation.
     * For simplicity, this will not return results attached to draft pages.
     * Draft pages should never really have related items though.
     *
     * @param Builder|QueryBuilder $query
     */
    public function filterRestrictedEntityRelations($query, string $tableName, string $entityIdColumn, string $entityTypeColumn, string $action = 'view')
    {
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];
        $pageMorphClass = (new Page())->getMorphClass();

        $q = $query->whereExists(function ($permissionQuery) use (&$tableDetails, $action) {
            /** @var Builder $permissionQuery */
            $permissionQuery->select(['role_id'])->from('joint_permissions')
                ->whereColumn('joint_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                ->whereColumn('joint_permissions.entity_type', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                ->where('joint_permissions.action', '=', $action)
                ->whereIn('joint_permissions.role_id', $this->getCurrentUserRoles())
                ->where(function (QueryBuilder $query) {
                    $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                });
        })->where(function ($query) use ($tableDetails, $pageMorphClass) {
            /** @var Builder $query */
            $query->where($tableDetails['entityTypeColumn'], '!=', $pageMorphClass)
                ->orWhereExists(function (QueryBuilder $query) use ($tableDetails, $pageMorphClass) {
                    $query->select('id')->from('pages')
                        ->whereColumn('pages.id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where($tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'], '=', $pageMorphClass)
                        ->where('pages.draft', '=', false);
                });
        });

        $this->clean();

        return $q;
    }

    /**
     * Add conditions to a query to filter the selection to related entities
     * where view permissions are granted.
     */
    public function filterRelatedEntity(string $entityClass, Builder $query, string $tableName, string $entityIdColumn): Builder
    {
        $fullEntityIdColumn = $tableName . '.' . $entityIdColumn;
        $instance = new $entityClass();
        $morphClass = $instance->getMorphClass();

        $existsQuery = function ($permissionQuery) use ($fullEntityIdColumn, $morphClass) {
            /** @var Builder $permissionQuery */
            $permissionQuery->select('joint_permissions.role_id')->from('joint_permissions')
                ->whereColumn('joint_permissions.entity_id', '=', $fullEntityIdColumn)
                ->where('joint_permissions.entity_type', '=', $morphClass)
                ->where('joint_permissions.action', '=', 'view')
                ->whereIn('joint_permissions.role_id', $this->getCurrentUserRoles())
                ->where(function (QueryBuilder $query) {
                    $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                });
        };

        $q = $query->where(function ($query) use ($existsQuery, $fullEntityIdColumn) {
            $query->whereExists($existsQuery)
                ->orWhere($fullEntityIdColumn, '=', 0);
        });

        if ($instance instanceof Page) {
            // Prevent visibility of non-owned draft pages
            $q->whereExists(function (QueryBuilder $query) use ($fullEntityIdColumn) {
                $query->select('id')->from('pages')
                    ->whereColumn('pages.id', '=', $fullEntityIdColumn)
                    ->where(function (QueryBuilder $query) {
                        $query->where('pages.draft', '=', false)
                            ->orWhere('pages.owned_by', '=', $this->currentUser()->id);
                    });
            });
        }

        $this->clean();

        return $q;
    }

    /**
     * Add the query for checking the given user id has permission
     * within the join_permissions table.
     *
     * @param QueryBuilder|Builder $query
     */
    protected function addJointHasPermissionCheck($query, int $userIdToCheck)
    {
        $query->where('joint_permissions.has_permission', '=', true)->orWhere(function ($query) use ($userIdToCheck) {
            $query->where('joint_permissions.has_permission_own', '=', true)
                ->where('joint_permissions.owned_by', '=', $userIdToCheck);
        });
    }

    /**
     * Get the current user.
     */
    private function currentUser(): User
    {
        if (is_null($this->currentUserModel)) {
            $this->currentUserModel = user();
        }

        return $this->currentUserModel;
    }

    /**
     * Clean the cached user elements.
     */
    private function clean(): void
    {
        $this->currentUserModel = null;
        $this->userRoles = null;
    }
}
