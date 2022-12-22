<?php

namespace BookStack\Auth\Permissions;

class EntityPermissionMap
{
    protected array $map = [];

    /**
     * @param EntityPermission[] $permissions
     */
    public function __construct(array $permissions = [])
    {
        foreach ($permissions as $entityPermission) {
            $this->addPermission($entityPermission);
        }
    }

    protected function addPermission(EntityPermission $permission)
    {
        $entityCombinedId = $permission->entity_type . ':' . $permission->entity_id;

        if (!isset($this->map[$entityCombinedId])) {
            $this->map[$entityCombinedId] = [];
        }

        $this->map[$entityCombinedId][] = $permission;
    }

    /**
     * @return EntityPermission[]
     */
    public function getForEntity(string $typeIdString): array
    {
        return $this->map[$typeIdString] ?? [];
    }
}
