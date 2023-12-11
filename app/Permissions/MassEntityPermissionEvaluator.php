<?php

namespace BookStack\Permissions;

use BookStack\Permissions\Models\EntityPermission;

class MassEntityPermissionEvaluator extends EntityPermissionEvaluator
{
    /**
     * @var SimpleEntityData[]
     */
    protected array $entitiesInvolved;
    protected array $permissionMapCache;

    public function __construct(array $entitiesInvolved, string $action)
    {
        $this->entitiesInvolved = $entitiesInvolved;
        parent::__construct($action);
    }

    public function evaluateEntityForRole(SimpleEntityData $entity, int $roleId): ?int
    {
        $typeIdChain = $this->gatherEntityChainTypeIds($entity);
        $relevantPermissions = $this->getPermissionMapByTypeIdForChainAndRole($typeIdChain, $roleId);
        $permitsByType = $this->collapseAndCategorisePermissions($typeIdChain, $relevantPermissions);

        return $this->evaluatePermitsByType($permitsByType);
    }

    /**
     * @param string[] $typeIdChain
     * @return array<string, EntityPermission[]>
     */
    protected function getPermissionMapByTypeIdForChainAndRole(array $typeIdChain, int $roleId): array
    {
        $allPermissions = $this->getPermissionMapByTypeIdAndRoleForAllInvolved();
        $relevantPermissions = [];

        // Filter down permissions to just those for current typeId
        // and current roleID or fallback permissions.
        foreach ($typeIdChain as $typeId) {
            $relevantPermissions[$typeId] = [
                ...($allPermissions[$typeId][$roleId] ?? []),
                ...($allPermissions[$typeId][0] ?? [])
            ];
        }

        return $relevantPermissions;
    }

    /**
     * @return array<string, array<int, EntityPermission[]>>
     */
    protected function getPermissionMapByTypeIdAndRoleForAllInvolved(): array
    {
        if (isset($this->permissionMapCache)) {
            return $this->permissionMapCache;
        }

        $entityTypeIdChain = [];
        foreach ($this->entitiesInvolved as $entity) {
            $entityTypeIdChain[] = $entity->type . ':' . $entity->id;
        }

        $permissionMap = $this->getPermissionsMapByTypeId($entityTypeIdChain, []);

       // Manipulate permission map to also be keyed by roleId.
        foreach ($permissionMap as $typeId => $permissions) {
            $permissionMap[$typeId] = [];
            foreach ($permissions as $permission) {
                $roleId = $permission->getRawAttribute('role_id');
                if (!isset($permissionMap[$typeId][$roleId])) {
                    $permissionMap[$typeId][$roleId] = [];
                }
                $permissionMap[$typeId][$roleId][] = $permission;
            }
        }

        $this->permissionMapCache = $permissionMap;

        return $this->permissionMapCache;
    }
}
