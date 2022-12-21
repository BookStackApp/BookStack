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

        $hasApplicableEntityPermissions = $this->hasEntityPermission($ownable, $userRoleIds, $user->id, $action);

        return is_null($hasApplicableEntityPermissions) ? $hasRolePermission : $hasApplicableEntityPermissions;
    }

    /**
     * Check if there are permissions that are applicable for the given entity item, action and roles.
     * Returns null when no entity permissions are in force.
     */
    protected function hasEntityPermission(Entity $entity, array $userRoleIds, int $userId, string $action): ?bool
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

        // Record role access preventions.
        // Used when we encounter a negative role permission where inheritance is active and therefore
        // need to check permissive status on parent items.
        $blockedRoleIds = [];

        foreach ($chain as $currentEntity) {
            $relevantPermissions = $currentEntity->permissions()
                ->where(function (Builder $query) use ($userRoleIds, $userId) {
                    $query->whereIn('role_id', $userRoleIds)
                    ->orWhere('user_id', '=', $userId)
                    ->orWhere(function (Builder $query) {
                        $query->whereNull(['role_id', 'user_id']);
                    });
                })
                ->get(['role_id', 'user_id', $action])
                ->all();

            // See dev/docs/permission-scenario-testing.md for technical details
            // on how permissions should be enforced.

            $allowedByTypeById = ['fallback' => [], 'user' => [], 'role' => []];
            /** @var EntityPermission $permission */
            foreach ($relevantPermissions as $permission) {
                $allowedByTypeById[$permission->getAssignedType()][$permission->getAssignedTypeId()] = boolval($permission->$action);
            }

            $inheriting = !isset($allowedByTypeById['fallback'][0]);

            // Continue up the chain if no applicable entity permission overrides.
            if (count($relevantPermissions) === 0) {
                continue;
            }

            // If we have user-specific permissions set, return the status of that
            // since it's the most specific possible.
            if (isset($allowedByTypeById['user'][$userId])) {
                return $allowedByTypeById['user'][$userId];
            }

            // If we have role-specific permissions set, allow if any of those
            // role permissions allow access. We do not allow if the role has been previously
            // blocked by a high-priority inheriting level.
            // If we're inheriting at this level, and there's an explicit non-allow permission, we record
            // it for checking up the chain.
            foreach ($allowedByTypeById['role'] as $roleId => $allowed) {
                if ($allowed && !in_array($roleId, $blockedRoleIds)) {
                    return true;
                } else if (!$allowed) {
                    $blockedRoleIds[] = $roleId;
                }
            }

            // If we had role permissions, and none of them allowed (via above loop), and
            // we are not inheriting, exit here since we only have role permissions in play blocking access.
            if (count($allowedByTypeById['role']) > 0 && !$inheriting) {
                return false;
            }

            // Continue up the chain if inheriting
            if ($inheriting) {
                continue;
            }

            // Otherwise, return the default "Other roles" fallback value.
            return $allowedByTypeById['fallback'][0];
        }

        // If we have relevant roles conditions that are actively blocking
        // return false since these are more specific than potential role-level permissions.
        if (count($blockedRoleIds) > 0) {
            return false;
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
            ->where(function (Builder $query) {
                $query->whereIn('role_id', $this->getCurrentUserRoleIds())
                ->orWhere('user_id', '=', $this->currentUser()->id);
            });

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
            })->orWhereHas('jointUserPermissions', function (Builder $query) {
                $query->where('user_id', '=', $this->currentUser()->id)->where('has_permission', '=', true);
            });
        })->whereDoesntHave('jointUserPermissions', function (Builder $query) {
            $query->where('user_id', '=', $this->currentUser()->id)->where('has_permission', '=', false);
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

        $q = $query->where(function ($query) use ($tableDetails) {
            $query->whereExists(function ($permissionQuery) use ($tableDetails) {
                /** @var Builder $permissionQuery */
                $permissionQuery->select(['role_id'])->from('joint_permissions')
                    ->whereColumn('joint_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                    ->whereColumn('joint_permissions.entity_type', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                    ->whereIn('joint_permissions.role_id', $this->getCurrentUserRoleIds())
                    ->where(function (QueryBuilder $query) {
                        $this->addJointHasPermissionCheck($query, $this->currentUser()->id);
                    });
            })->orWhereExists(function ($permissionQuery) use ($tableDetails) {
                /** @var Builder $permissionQuery */
                $permissionQuery->select(['user_id'])->from('joint_user_permissions')
                    ->whereColumn('joint_user_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                    ->whereColumn('joint_user_permissions.entity_type', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                    ->where('joint_user_permissions.user_id', '=', $this->currentUser()->id)
                    ->where('joint_user_permissions.has_permission', '=', true);
            });
        })->whereNotExists(function ($query) use ($tableDetails) {
            $query->select(['user_id'])->from('joint_user_permissions')
                ->whereColumn('joint_user_permissions.entity_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                ->whereColumn('joint_user_permissions.entity_type', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'])
                ->where('joint_user_permissions.user_id', '=', $this->currentUser()->id)
                ->where('joint_user_permissions.has_permission', '=', false);
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

        $userExistsQuery = function ($hasPermission) use ($fullPageIdColumn, $morphClass) {
            return function ($permissionQuery) use ($fullPageIdColumn, $morphClass) {
                /** @var Builder $permissionQuery */
                $permissionQuery->select('joint_user_permissions.user_id')->from('joint_user_permissions')
                    ->whereColumn('joint_user_permissions.entity_id', '=', $fullPageIdColumn)
                    ->where('joint_user_permissions.entity_type', '=', $morphClass)
                    ->where('joint_user_permissions.user_id', $this->currentUser()->id)
                    ->where('has_permission', '=', true);
            };
        };

        $q = $query->where(function ($query) use ($existsQuery, $userExistsQuery, $fullPageIdColumn) {
            $query->whereExists($existsQuery)
                ->orWhereExists($userExistsQuery(true))
                ->orWhere($fullPageIdColumn, '=', 0);
        })->whereNotExists($userExistsQuery(false));

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
