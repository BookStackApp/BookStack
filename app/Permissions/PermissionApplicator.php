<?php

namespace BookStack\Permissions;

use BookStack\App\Model;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\Models\EntityPermission;
use BookStack\Users\Models\OwnableInterface;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Database\Query\JoinClause;
use InvalidArgumentException;

class PermissionApplicator
{
    public function __construct(
        protected ?User $user = null
    ) {
    }

    /**
     * Checks if an entity has a restriction set upon it.
     */
    public function checkOwnableUserAccess(Model&OwnableInterface $ownable, string|Permission $permission): bool
    {
        $permissionName = is_string($permission) ? $permission : $permission->value;
        $explodedPermission = explode('-', $permissionName);
        $action = $explodedPermission[1] ?? $explodedPermission[0];
        $fullPermission = count($explodedPermission) > 1 ? $permissionName : $ownable->getMorphClass() . '-' . $permissionName;

        $user = $this->currentUser();
        $userRoleIds = $this->getCurrentUserRoleIds();

        $allRolePermission = $user->can($fullPermission . '-all');
        $ownRolePermission = $user->can($fullPermission . '-own');
        $nonJointPermissions = ['restrictions', 'image', 'attachment', 'comment'];
        $ownerField = $ownable->getOwnerFieldName();
        $ownableFieldVal = $ownable->getAttribute($ownerField);

        $isOwner = $user->id === $ownableFieldVal;
        $hasRolePermission = $allRolePermission || ($isOwner && $ownRolePermission);

        // Handle non-entity-specific jointPermissions
        if (in_array($explodedPermission[0], $nonJointPermissions)) {
            return $hasRolePermission;
        }

        if (!($ownable instanceof Entity)) {
            return false;
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

        return (new EntityPermissionEvaluator($action))->evaluateEntityForUser($entity, $userRoleIds);
    }

    /**
     * Checks if a user has the given permission for any items in the system.
     * Can be passed an entity instance to filter on a specific type.
     */
    public function checkUserHasEntityPermissionOnAny(string|Permission $action, string $entityClass = ''): bool
    {
        $permissionName = is_string($action) ? $action : $action->value;
        $this->ensureValidEntityAction($permissionName);

        $permissionQuery = EntityPermission::query()
            ->where($permissionName, '=', true)
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
                $permissionQuery->select(['entity_id', 'entity_type'])
                    ->selectRaw('max(owner_id) as owner_id')
                    ->selectRaw('max(status) as status')
                    ->whereIn('role_id', $this->getCurrentUserRoleIds())
                    ->groupBy(['entity_type', 'entity_id'])
                    ->havingRaw('(status IN (1, 3) or (owner_id = ? and status != 2))', [$this->currentUser()->id]);
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
     */
    public function restrictEntityRelationQuery(Builder $query, string $tableName, string $entityIdColumn, string $entityTypeColumn): Builder
    {
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];
        $pageMorphClass = (new Page())->getMorphClass();

        return $this->restrictEntityQuery($query)
            ->where(function ($query) use ($tableDetails, $pageMorphClass) {
                /** @var Builder $query */
                $query->where($tableDetails['entityTypeColumn'], '!=', $pageMorphClass)
                ->orWhereExists(function (QueryBuilder $query) use ($tableDetails, $pageMorphClass) {
                    $query->select('page_id')->from('entity_page_data')
                        ->whereColumn('entity_page_data.page_id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where($tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'], '=', $pageMorphClass)
                        ->where('entity_page_data.draft', '=', false);
                });
            });
    }

    /**
     * Filter out items that have related entity relations where
     * the entity is marked as deleted.
     */
    public function filterDeletedFromEntityRelationQuery(Builder $query, string $tableName, string $entityIdColumn, string $entityTypeColumn): Builder
    {
        $tableDetails = ['tableName' => $tableName, 'entityIdColumn' => $entityIdColumn, 'entityTypeColumn' => $entityTypeColumn];
        $entityProvider = new EntityProvider();

        $joinQuery = function ($query) use ($entityProvider) {
            $first = true;
            foreach ($entityProvider->all() as $entity) {
                /** @var Builder $query */
                $entityQuery = function ($query) use ($entity) {
                    $query->select(['id', 'deleted_at'])
                        ->selectRaw("'{$entity->getMorphClass()}' as type")
                        ->from($entity->getTable())
                        ->whereNotNull('deleted_at');
                };

                if ($first) {
                    $entityQuery($query);
                    $first = false;
                } else {
                    $query->union($entityQuery);
                }
            }
        };

        return $query->leftJoinSub($joinQuery, 'deletions', function (JoinClause $join) use ($tableDetails) {
            $join->on($tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'], '=', 'deletions.id')
                ->on($tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'], '=', 'deletions.type');
        })->whereNull('deletions.deleted_at');
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
        return $this->restrictEntityQuery($query)
            ->whereExists(function (QueryBuilder $query) use ($fullPageIdColumn) {
                $query->select('id')->from('entities')
                    ->leftJoin('entity_page_data', 'entities.id', '=', 'entity_page_data.page_id')
                    ->whereColumn('entities.id', '=', $fullPageIdColumn)
                    ->where('entities.type', '=', 'page')
                    ->where(function (QueryBuilder $query) {
                        $query->where('entity_page_data.draft', '=', false)
                            ->orWhere(function (QueryBuilder $query) {
                                $query->where('entity_page_data.draft', '=', true)
                                    ->where('entities.created_by', '=', $this->currentUser()->id);
                            });
                    });
            });
    }

    /**
     * Get the current user.
     */
    protected function currentUser(): User
    {
        return $this->user ?? user();
    }

    /**
     * Get the roles for the current logged-in user.
     *
     * @return int[]
     */
    protected function getCurrentUserRoleIds(): array
    {
        return $this->currentUser()->roles->pluck('id')->values()->all();
    }

    /**
     * Ensure the given action is a valid and expected entity action.
     * Throws an exception if invalid otherwise does nothing.
     * @throws InvalidArgumentException
     */
    protected function ensureValidEntityAction(string $action): void
    {
        $allowed = Permission::genericForEntity();
        foreach ($allowed as $permission) {
            if ($permission->value === $action) {
                return;
            }
        }

        throw new InvalidArgumentException('Action should be a simple entity permission action, not a role permission');
    }
}
