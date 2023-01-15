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
use Illuminate\Database\Query\JoinClause;
use Illuminate\Support\Facades\DB;
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
        $this->applyPermissionsToQuery($query, $query->getModel()->getTable(), $morphClass, 'id', '');

        return $query;
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyPermissionsToQuery($query, string $queryTable, string $entityTypeLimiter, string $entityIdColumn, string $entityTypeColumn): void
    {
        if ($this->currentUser()->hasSystemRole('admin')) {
            return;
        }

        $this->applyFallbackJoin($query, $queryTable, $entityTypeLimiter, $entityIdColumn, $entityTypeColumn);
        $this->applyRoleJoin($query, $queryTable, $entityTypeLimiter, $entityIdColumn, $entityTypeColumn);
        $this->applyUserJoin($query, $queryTable, $entityTypeLimiter, $entityIdColumn, $entityTypeColumn);
        $this->applyPermissionWhereFilter($query, $queryTable, $entityTypeLimiter, $entityTypeColumn);
    }

    /**
     * Apply the where condition to a permission restricting query, to limit based upon the values of the joined
     * permission data. Query must have joins pre-applied.
     * Either entityTypeLimiter or entityTypeColumn should be supplied, with the other empty.
     * Both should not be applied since that would conflict upon intent.
     * @param Builder|QueryBuilder $query
     */
    protected function applyPermissionWhereFilter($query, string $queryTable, string $entityTypeLimiter, string $entityTypeColumn)
    {
        $abilities = ['all' => [], 'own' => []];
        $types = $entityTypeLimiter ? [$entityTypeLimiter] : ['page', 'chapter', 'bookshelf', 'book'];
        $fullEntityTypeColumn = $queryTable . '.' . $entityTypeColumn;
        foreach ($types as $type) {
            $abilities['all'][$type] = userCan($type . '-view-all');
            $abilities['own'][$type] = userCan($type . '-view-own');
        }

        $abilities['all'] = array_filter($abilities['all']);
        $abilities['own'] = array_filter($abilities['own']);

        $query->where(function (Builder $query) use ($abilities, $fullEntityTypeColumn, $entityTypeColumn) {
            $query->where('perms_user', '=', 1)
                ->orWhere(function (Builder $query) {
                    $query->whereNull('perms_user')->where('perms_role', '=', 1);
                })->orWhere(function (Builder $query) {
                    $query->whereNull(['perms_user', 'perms_role'])
                        ->where('perms_fallback', '=', 1);
                });

            if (count($abilities['all']) > 0) {
                $query->orWhere(function (Builder $query) use ($abilities, $fullEntityTypeColumn, $entityTypeColumn) {
                    $query->whereNull(['perms_user', 'perms_role', 'perms_fallback']);
                    if ($entityTypeColumn) {
                        $query->whereIn($fullEntityTypeColumn, array_keys($abilities['all']));
                    }
                });
            }

            if (count($abilities['own']) > 0) {
                $query->orWhere(function (Builder $query) use ($abilities, $fullEntityTypeColumn, $entityTypeColumn) {
                    $query->whereNull(['perms_user', 'perms_role', 'perms_fallback'])
                        ->where('owned_by', '=', $this->currentUser()->id);
                    if ($entityTypeColumn) {
                        $query->whereIn($fullEntityTypeColumn, array_keys($abilities['all']));
                    }
                });
            }
        });
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyPermissionJoin(callable $joinCallable, string $subAlias, $query, string $queryTable, string $entityTypeLimiter, string $entityIdColumn, string $entityTypeColumn)
    {
        $joinCondition = $this->getJoinCondition($queryTable, $subAlias, $entityIdColumn, $entityTypeColumn);

        $query->joinSub(function (QueryBuilder $joinQuery) use ($joinCallable, $entityTypeLimiter) {
            $joinQuery->select(['entity_id', 'entity_type'])->from('entity_permissions_collapsed')
                ->groupBy('entity_id', 'entity_type');
            $joinCallable($joinQuery);

            if ($entityTypeLimiter) {
                $joinQuery->where('entity_type', '=', $entityTypeLimiter);
            }
        }, $subAlias, $joinCondition, null, null, 'left');
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyUserJoin($query, string $queryTable, string $entityTypeLimiter, string $entityIdColumn, string $entityTypeColumn)
    {
        $this->applyPermissionJoin(function (QueryBuilder $joinQuery) {
            $joinQuery->selectRaw('max(view) as perms_user')
                ->where('user_id', '=', $this->currentUser()->id);
        }, 'p_u', $query, $queryTable, $entityTypeLimiter, $entityIdColumn, $entityTypeColumn);
    }


    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyRoleJoin($query, string $queryTable, string $entityTypeLimiter, string $entityIdColumn, string $entityTypeColumn)
    {
        $this->applyPermissionJoin(function (QueryBuilder $joinQuery) {
            $joinQuery->selectRaw('max(view) as perms_role')
                ->whereIn('role_id', $this->getCurrentUserRoleIds());
        }, 'p_r', $query, $queryTable, $entityTypeLimiter, $entityIdColumn, $entityTypeColumn);
    }

    /**
     * @param Builder|QueryBuilder $query
     */
    protected function applyFallbackJoin($query, string $queryTable, string $entityTypeLimiter, string $entityIdColumn, string $entityTypeColumn)
    {
        $this->applyPermissionJoin(function (QueryBuilder $joinQuery) {
            $joinQuery->selectRaw('max(view) as perms_fallback')
                ->whereNull(['role_id', 'user_id']);
        }, 'p_f', $query, $queryTable, $entityTypeLimiter, $entityIdColumn, $entityTypeColumn);
    }

    protected function getJoinCondition(string $queryTable, string $joinTableName, string $entityIdColumn, string $entityTypeColumn): callable
    {
        return function (JoinClause $join) use ($queryTable, $joinTableName, $entityIdColumn, $entityTypeColumn) {
            $join->on($queryTable . '.' . $entityIdColumn, '=', $joinTableName . '.entity_id');
            if ($entityTypeColumn) {
                $join->on($queryTable . '.' . $entityTypeColumn, '=', $joinTableName . '.entity_type');
            }
        };
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
        $query->leftJoinSub(function (QueryBuilder $query) {
            $query->select(['id as entity_id', DB::raw("'page' as entity_type"), 'owned_by', 'deleted_at', 'draft'])->from('pages');
            $tablesByType = ['page' => 'pages', 'book' => 'books', 'chapter' => 'chapters', 'bookshelf' => 'bookshelves'];
            foreach ($tablesByType as $type => $table) {
                $query->unionAll(function (QueryBuilder $query) use ($type, $table) {
                    $query->select(['id as entity_id', DB::raw("'{$type}' as entity_type"), 'owned_by', 'deleted_at', DB::raw('0 as draft')])->from($table);
                });
            }
        }, 'entities', function (JoinClause $join) use ($tableName, $entityIdColumn, $entityTypeColumn) {
            $join->on($tableName . '.' . $entityIdColumn, '=', 'entities.entity_id')
                 ->on($tableName . '.' . $entityTypeColumn, '=', 'entities.entity_type');
        });

        $this->applyPermissionsToQuery($query, $tableName, '', $entityIdColumn, $entityTypeColumn);
        // TODO - Test page draft access (Might allow drafts which should not be seen)

        return $query;
    }

    /**
     * Add conditions to a query for a model that's a relation of a page, so only the model results
     * on visible pages are returned by the query.
     * Is effectively the same as "restrictEntityRelationQuery" but takes into account page drafts
     * while not expecting a polymorphic relation, Just a simpler one-page-to-many-relations set-up.
     */
    public function restrictPageRelationQuery(Builder $query, string $tableName, string $pageIdColumn): Builder
    {
        $morphClass = (new Page())->getMorphClass();

        $this->applyPermissionsToQuery($query, $tableName, $morphClass, $pageIdColumn, '');
        // TODO - Draft display
        // TODO - Likely need owned_by entity join workaround as used above
        return $query;
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
