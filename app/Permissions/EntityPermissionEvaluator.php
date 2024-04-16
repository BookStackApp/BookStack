<?php

namespace BookStack\Permissions;

use BookStack\Entities\Models\Entity;
use BookStack\Permissions\Models\EntityPermission;
use BookStack\Users\Models\Role;
use Illuminate\Database\Eloquent\Builder;

class EntityPermissionEvaluator
{
    public function __construct(
        protected string $action
    ) {
    }

    public function evaluateEntityForUser(Entity $entity, array $userRoleIds): ?bool
    {
        if ($this->isUserSystemAdmin($userRoleIds)) {
            return true;
        }

        $typeIdChain = $this->gatherEntityChainTypeIds(SimpleEntityData::fromEntity($entity));
        $relevantPermissions = $this->getPermissionsMapByTypeId($typeIdChain, [...$userRoleIds, 0]);
        $permitsByType = $this->collapseAndCategorisePermissions($typeIdChain, $relevantPermissions);

        $status = $this->evaluatePermitsByType($permitsByType);

        return is_null($status) ? null : $status === PermissionStatus::IMPLICIT_ALLOW || $status === PermissionStatus::EXPLICIT_ALLOW;
    }

    /**
     * @param array<string, array<string, int>> $permitsByType
     */
    protected function evaluatePermitsByType(array $permitsByType): ?int
    {
        // Return grant or reject from role-level if exists
        if (count($permitsByType['role']) > 0) {
            return max($permitsByType['role']) ? PermissionStatus::EXPLICIT_ALLOW : PermissionStatus::EXPLICIT_DENY;
        }

        // Return fallback permission if exists
        if (count($permitsByType['fallback']) > 0) {
            return $permitsByType['fallback'][0] ? PermissionStatus::IMPLICIT_ALLOW : PermissionStatus::IMPLICIT_DENY;
        }

        return null;
    }

    /**
     * @param string[] $typeIdChain
     * @param array<string, EntityPermission[]> $permissionMapByTypeId
     * @return array<string, array<string, int>>
     */
    protected function collapseAndCategorisePermissions(array $typeIdChain, array $permissionMapByTypeId): array
    {
        $permitsByType = ['fallback' => [], 'role' => []];

        foreach ($typeIdChain as $typeId) {
            $permissions = $permissionMapByTypeId[$typeId] ?? [];
            foreach ($permissions as $permission) {
                $roleId = $permission->role_id;
                $type = $roleId === 0 ? 'fallback' : 'role';
                if (!isset($permitsByType[$type][$roleId])) {
                    $permitsByType[$type][$roleId] = $permission->{$this->action};
                }
            }

            if (isset($permitsByType['fallback'][0])) {
                break;
            }
        }

        return $permitsByType;
    }

    /**
     * @param string[] $typeIdChain
     * @return array<string, EntityPermission[]>
     */
    protected function getPermissionsMapByTypeId(array $typeIdChain, array $filterRoleIds): array
    {
        $idsByType = [];
        foreach ($typeIdChain as $typeId) {
            [$type, $id] = explode(':', $typeId);
            if (!isset($idsByType[$type])) {
                $idsByType[$type] = [];
            }

            $idsByType[$type][] = $id;
        }

        $relevantPermissions = [];

        foreach ($idsByType as $type => $ids) {
            $idsChunked = array_chunk($ids, 10000);
            foreach ($idsChunked as $idChunk) {
                $permissions = $this->getPermissionsForEntityIdsOfType($type, $idChunk, $filterRoleIds);
                array_push($relevantPermissions, ...$permissions);
            }
        }

        $map = [];
        foreach ($relevantPermissions as $permission) {
            $key = $permission->entity_type . ':' . $permission->entity_id;
            if (!isset($map[$key])) {
                $map[$key] = [];
            }

            $map[$key][] = $permission;
        }

        return $map;
    }

    /**
     * @param string[] $ids
     * @param int[] $filterRoleIds
     * @return EntityPermission[]
     */
    protected function getPermissionsForEntityIdsOfType(string $type, array $ids, array $filterRoleIds): array
    {
        $query = EntityPermission::query()
            ->where('entity_type', '=', $type)
            ->whereIn('entity_id', $ids);

        if (!empty($filterRoleIds)) {
            $query->where(function (Builder $query) use ($filterRoleIds) {
                $query->whereIn('role_id', [...$filterRoleIds, 0]);
            });
        }

        return $query->get(['entity_id', 'entity_type', 'role_id', $this->action])->all();
    }

    /**
     * @return string[]
     */
    protected function gatherEntityChainTypeIds(SimpleEntityData $entity): array
    {
        // The array order here is very important due to the fact we walk up the chain
        // elsewhere in the class. Earlier items in the chain have higher priority.

        $chain = [$entity->type . ':' . $entity->id];

        if ($entity->type === 'page' && $entity->chapter_id) {
            $chain[] = 'chapter:' . $entity->chapter_id;
        }

        if ($entity->type === 'page' || $entity->type === 'chapter') {
            $chain[] = 'book:' . $entity->book_id;
        }

        return $chain;
    }

    protected function isUserSystemAdmin($userRoleIds): bool
    {
        $adminRoleId = Role::getSystemRole('admin')->id;
        return in_array($adminRoleId, $userRoleIds);
    }
}
