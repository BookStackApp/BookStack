<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Model;
use BookStack\Traits\HasCreatorAndUpdater;
use BookStack\Traits\HasOwner;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use InvalidArgumentException;

class PermissionApplicator
{
    /**
     * Checks if an entity has a restriction set upon it.
     *
     * @param HasCreatorAndUpdater|HasOwner $ownable
     */
    public function checkOwnableUserAccess(Model $ownable, string $permission): bool
    {
        $explodedPermission = explode('-', $permission);
        $action = $explodedPermission[1] ?? $explodedPermission[0];
        $fullPermission = count($explodedPermission) > 1 ? $permission : $ownable->getMorphClass() . '-' . $permission;

        $user = $this->currentUser();
        $userRoleIds = $this->getCurrentUserRoleIds();

        $allRolePermission = $user->can($fullPermission . '-all');
        $ownRolePermission = $user->can($fullPermission . '-own');
        $nonJointPermissions = ['restrictions', 'image', 'attachment', 'comment'];
        $ownerField = ($ownable instanceof Entity) ? 'owned_by' : 'created_by';
        $ownableFieldVal = $ownable->getAttribute($ownerField);

        if (is_null($ownableFieldVal)) {
            throw new InvalidArgumentException("{$ownerField} field used but has not been loaded");
        }

        $isOwner = $user->id === $ownableFieldVal;
        $hasRolePermission = $allRolePermission || ($isOwner && $ownRolePermission);

        // Handle non entity specific jointPermissions
        if (in_array($explodedPermission[0], $nonJointPermissions)) {
            return $hasRolePermission;
        }

        $hasApplicableEntityPermissions = $this->hasEntityPermission($ownable, $userRoleIds, $action);

        return is_null($hasApplicableEntityPermissions) ? $hasRolePermission : $hasApplicableEntityPermissions;
    }

    /**
     * Check if there are permissions that are applicable for the given entity item, action and roles.
     * Returns null when no entity permissions are in force.
     */
    protected function hasEntityPermission(Entity $entity, array $userRoleIds, string $action): ?bool
    {
        $this->ensureValidEntityAction($action);

        $adminRoleId = Role::getSystemRole('admin')->id;
        if (in_array($adminRoleId, $userRoleIds)) {
            return true;
        }

        // The chain order here is very important due to the fact we walk up the chain
        // in the loop below. Earlier items in the chain have higher priority.
        $chain = [$entity];
        if ($entity instanceof Page && $entity->chapter_id) {
            $chain[] = $entity->chapter;
        }

        if ($entity instanceof Page || $entity instanceof Chapter) {
            $chain[] = $entity->book;
        }

        foreach ($chain as $currentEntity) {
            $allowedByRoleId = $currentEntity->permissions()
                ->whereIn('role_id', [0, ...$userRoleIds])
                ->pluck($action, 'role_id');

            // Continue up the chain if no applicable entity permission overrides.
            if ($allowedByRoleId->isEmpty()) {
                continue;
            }

            // If we have user-role-specific permissions set, allow if any of those
            // role permissions allow access.
            $hasDefault = $allowedByRoleId->has(0);
            if (!$hasDefault || $allowedByRoleId->count() > 1) {
                return $allowedByRoleId->search(function (bool $allowed, int $roleId) {
                        return $roleId !== 0 && $allowed;
                }) !== false;
            }

            // Otherwise, return the default "Other roles" fallback value.
            return $allowedByRoleId->get(0);
        }

        return null;
    }

    /**
     * Checks if a user has the given permission for any items in the system.
     * Can be passed an entity instance to filter on a specific type.
     */
    public function checkUserHasEntityPermissionOnAny(string $action, string $entityClass = ''): bool
    {
        $this->ensureValidEntityAction($action);

        $permissionQuery = EntityPermission::query()
            ->where($action, '=', true)
            ->whereIn('role_id', $this->getCurrentUserRoleIds());

        if (!empty($entityClass)) {
            /** @var Entity $entityInstance */
            $entityInstance = app()->make($entityClass);
            $permissionQuery = $permissionQuery->where('entity_type', '=', $entityInstance->getMorphClass());
        }

        $hasPermission = $permissionQuery->count() > 0;

        return $hasPermission;
    }

    /**
     * Limit the given entity query so that the query will only
     * return items that the user has view permission for.
     */
    public function restrictEntityQuery(Builder $query): Builder
    {
        return $query->where(function (Builder $parentQuery) {
            $parentQuery->whereHas('jointPermissions', function (Builder $permissionQuery) {
                $permissionQuery->whereIn('role_id', $this->getCurrentUserRoleIds())
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
    public function restrictDraftsOnPageQuery(Builder $query): Builder
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
     * Filter items that have entities set as a polymorphic relation.
     * For simplicity, this will not return results attached to draft pages.
     * Draft pages should never really have related items though.
     *
     * @param Builder|QueryBuilder $query
     */
    public function restrictEntityRelationQuery($query, string $tableName, string $entityIdColumn, string $entityTypeColumn)
    {
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];
        $pageMorphClass = (new Page())->getMorphClass();

        $q = $query->whereExists(function ($permissionQuery) use (&$tableDetails) {
            /** @var Builder $permissionQuery */
            $permissionQuery->select(['role_id'])->from('joint_permissions')
                ->whereColumn('joint_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                ->whereColumn('joint_permissions.entity_type', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                ->whereIn('joint_permissions.role_id', $this->getCurrentUserRoleIds())
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

        return $q;
    }

    /**
     * Add conditions to a query for a model that's a relation of a page, so only the model results
     * on visible pages are returned by the query.
     * Is effectively the same as "restrictEntityRelationQuery" but takes into account page drafts
     * while not expecting a polymorphic relation, Just a simpler one-page-to-many-relations set-up.
     */
    public function restrictPageRelationQuery(Builder $query, string $tableName, string $pageIdColumn): Builder
    {
        $fullPageIdColumn = $tableName . '.' . $pageIdColumn;
        $morphClass = (new Page())->getMorphClass();

        $existsQuery = function ($permissionQuery) use ($fullPageIdColumn, $morphClass) {
            /** @var Builder $permissionQuery */
            $permissionQuery->select('joint_permissions.role_id')->from('joint_permissions')
                ->whereColumn('joint_permissions.entity_id', '=', $fullPageIdColumn)
                ->where('joint_permissions.entity_type', '=', $morphClass)
                ->whereIn('joint_permissions.role_id', $this->getCurrentUserRoleIds())
                ->where(function (QueryBuilder $query) {
                    $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                });
        };

        $q = $query->where(function ($query) use ($existsQuery, $fullPageIdColumn) {
            $query->whereExists($existsQuery)
                ->orWhere($fullPageIdColumn, '=', 0);
        });

        // Prevent visibility of non-owned draft pages
        $q->whereExists(function (QueryBuilder $query) use ($fullPageIdColumn) {
            $query->select('id')->from('pages')
                ->whereColumn('pages.id', '=', $fullPageIdColumn)
                ->where(function (QueryBuilder $query) {
                    $query->where('pages.draft', '=', false)
                        ->orWhere('pages.owned_by', '=', $this->currentUser()->id);
                });
        });

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
    protected function currentUser(): User
    {
        return user();
    }

    /**
     * Get the roles for the current logged-in user.
     *
     * @return int[]
     */
    protected function getCurrentUserRoleIds(): array
    {
        if (auth()->guest()) {
            return [Role::getSystemRole('public')->id];
        }

        return $this->currentUser()->roles->pluck('id')->values()->all();
    }

    /**
     * Ensure the given action is a valid and expected entity action.
     * Throws an exception if invalid otherwise does nothing.
     * @throws InvalidArgumentException
     */
    protected function ensureValidEntityAction(string $action): void
    {
        if (!in_array($action, EntityPermission::PERMISSIONS)) {
            throw new InvalidArgumentException('Action should be a simple entity permission action, not a role permission');
        }
    }
}
