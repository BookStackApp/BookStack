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

        // The array order here is very important due to the fact we walk up the chain
        // in the flattening loop below. Earlier items in the chain have higher priority.
        $typeIdList = [$entity->getMorphClass() . ':' . $entity->id];
        if ($entity instanceof Page && $entity->chapter_id) {
            $typeIdList[] = 'chapter:' . $entity->chapter_id;
        }

        if ($entity instanceof Page || $entity instanceof Chapter) {
            $typeIdList[] = 'book:' . $entity->book_id;
        }

        $relevantPermissions = EntityPermission::query()
            ->where(function (Builder $query) use ($typeIdList) {
                foreach ($typeIdList as $typeId) {
                    $query->orWhere(function (Builder $query) use ($typeId) {
                        [$type, $id] = explode(':', $typeId);
                        $query->where('entity_type', '=', $type)
                            ->where('entity_id', '=', $id);
                    });
                }
            })->where(function (Builder $query) use ($userRoleIds, $userId) {
                $query->whereIn('role_id', $userRoleIds)
                    ->orWhere('user_id', '=', $userId)
                    ->orWhere(function (Builder $query) {
                        $query->whereNull(['role_id', 'user_id']);
                    });
            })->get(['entity_id', 'entity_type', 'role_id', 'user_id', $action])
            ->all();

        $permissionMap = new EntityPermissionMap($relevantPermissions);
        $permitsByType = ['user' => [], 'fallback' => [], 'role' => []];

        // Collapse and simplify permission structure
        foreach ($typeIdList as $typeId) {
            $permissions = $permissionMap->getForEntity($typeId);
            foreach ($permissions as $permission) {
                $related = $permission->getAssignedType();
                $relatedId = $permission->getAssignedTypeId();
                if (!isset($permitsByType[$related][$relatedId])) {
                    $permitsByType[$related][$relatedId] = $permission->$action;
                }
            }
        }

        // Return user-level permission if exists
        if (count($permitsByType['user']) > 0) {
            return boolval(array_values($permitsByType['user'])[0]);
        }

        // Return grant or reject from role-level if exists
        if (count($permitsByType['role']) > 0) {
            return boolval(max($permitsByType['role']));
        }

        // Return fallback permission if exists
        if (count($permitsByType['fallback']) > 0) {
            return boolval($permitsByType['fallback'][0]);
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
    public function restrictEntityQuery(Builder $query, string $morphClass): Builder
    {
        $this->getCurrentUserRoleIds();
        $this->currentUser()->id;

        $userViewAll = userCan($morphClass . '-view-all');
        $userViewOwn = userCan($morphClass . '-view-own');

        // TODO - Leave this as the new admin workaround?
        //   Or auto generate collapsed role permissions for admins?
        if (\user()->hasSystemRole('admin')) {
            return $query;
        }

        // Fallback permission join
        $query->joinSub(function (QueryBuilder $joinQuery) use ($morphClass) {
            $joinQuery->select(['entity_id'])->selectRaw('max(view) as perms_fallback')
                ->from('entity_permissions_collapsed')
                ->where('entity_type', '=', $morphClass)
                ->whereNull(['role_id', 'user_id'])
                ->groupBy('entity_id');
        }, 'p_f', 'id', '=', 'p_f.entity_id', 'left');

        // Role permission join
        $query->joinSub(function (QueryBuilder $joinQuery) use ($morphClass) {
            $joinQuery->select(['entity_id'])->selectRaw('max(view) as perms_role')
                ->from('entity_permissions_collapsed')
                ->where('entity_type', '=', $morphClass)
                ->whereIn('role_id', $this->getCurrentUserRoleIds())
                ->groupBy('entity_id');
        }, 'p_r', 'id', '=', 'p_r.entity_id', 'left');

        // User permission join
        $query->joinSub(function (QueryBuilder $joinQuery) use ($morphClass) {
            $joinQuery->select(['entity_id'])->selectRaw('max(view) as perms_user')
                ->from('entity_permissions_collapsed')
                ->where('entity_type', '=', $morphClass)
                ->where('user_id', '=', $this->currentUser()->id)
                ->groupBy('entity_id');
        }, 'p_u', 'id', '=', 'p_u.entity_id', 'left');

        // Where permissions apply
        $query->where(function (Builder $query) use ($userViewOwn, $userViewAll) {
            $query->where('perms_user', '=', 1)
                ->orWhere(function (Builder $query) {
                    $query->whereNull('perms_user')->where('perms_role', '=', 1);
                })->orWhere(function (Builder $query) {
                    $query->whereNull(['perms_user', 'perms_role'])
                        ->where('perms_fallback', '=', 1);
                });

            if ($userViewAll) {
                $query->orWhere(function (Builder $query) {
                    $query->whereNull(['perms_user', 'perms_role', 'perms_fallback']);
                });
            } else if ($userViewOwn) {
                $query->orWhere(function (Builder $query) {
                    $query->whereNull(['perms_user', 'perms_role', 'perms_fallback'])
                        ->where('owned_by', '=', $this->currentUser()->id);
                });
            }
        });

        return $query;
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

        // TODO;
        return $query;

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

        // TODO
        return $query;

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
