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

        return (new EntityPermissionEvaluator($action))->evaluateEntityForUser($entity, $userRoleIds);
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
                $permissionQuery->select(['entity_id', 'entity_type'])
                    ->selectRaw('max(owner_id) as owner_id')
                    ->selectRaw('max(status) as status')
                    ->whereIn('role_id', $this->getCurrentUserRoleIds())
                    ->groupBy(['entity_type', 'entity_id'])
                    ->havingRaw('(status IN (1, 3) or owner_id = ?)', [$this->currentUser()->id]);
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
                    $query->select('id')->from('pages')
                        ->whereColumn('pages.id', '=', $tableDetails['tableName'] . '.' . $tableDetails['entityIdColumn'])
                        ->where($tableDetails['tableName'] . '.' . $tableDetails['entityTypeColumn'], '=', $pageMorphClass)
                        ->where('pages.draft', '=', false);
                });
            });
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
            ->where(function ($query) use ($fullPageIdColumn) {
                /** @var Builder $query */
                $query->whereExists(function (QueryBuilder $query) use ($fullPageIdColumn) {
                    $query->select('id')->from('pages')
                        ->whereColumn('pages.id', '=', $fullPageIdColumn)
                        ->where('pages.draft', '=', false);
                });
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
