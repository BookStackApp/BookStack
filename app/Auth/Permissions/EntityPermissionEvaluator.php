<?php

namespace BookStack\Auth\Permissions;

use BookStack\Auth\Role;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Illuminate\Database\Eloquent\Builder;

class EntityPermissionEvaluator
{
    protected Entity $entity;
    protected array $userRoleIds;
    protected string $action;
    protected int $userId;

    public function __construct(Entity $entity, int $userId, array $userRoleIds, string $action)
    {
        $this->entity = $entity;
        $this->userId = $userId;
        $this->userRoleIds = $userRoleIds;
        $this->action = $action;
    }

    public function evaluate(): ?bool
    {
        if ($this->isUserSystemAdmin()) {
            return true;
        }

        $typeIdChain = $this->gatherEntityChainTypeIds();
        $relevantPermissions = $this->getRelevantPermissionsMapByTypeId($typeIdChain);
        $permitsByType = $this->collapseAndCategorisePermissions($typeIdChain, $relevantPermissions);

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
        }

        return $permitsByType;
    }

    /**
     * @param string[] $typeIdChain
     * @return array<string, EntityPermission[]>
     */
    protected function getRelevantPermissionsMapByTypeId(array $typeIdChain): array
    {
        $relevantPermissions = EntityPermission::query()
            ->where(function (Builder $query) use ($typeIdChain) {
                foreach ($typeIdChain as $typeId) {
                    $query->orWhere(function (Builder $query) use ($typeId) {
                        [$type, $id] = explode(':', $typeId);
                        $query->where('entity_type', '=', $type)
                            ->where('entity_id', '=', $id);
                    });
                }
            })->where(function (Builder $query) {
                $query->whereIn('role_id', [...$this->userRoleIds, 0]);
            })->get(['entity_id', 'entity_type', 'role_id', $this->action])
            ->all();

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
     * @return string[]
     */
    protected function gatherEntityChainTypeIds(): array
    {
        // The array order here is very important due to the fact we walk up the chain
        // elsewhere in the class. Earlier items in the chain have higher priority.

        $chain = [$this->entity->getMorphClass() . ':' . $this->entity->id];

        if ($this->entity instanceof Page && $this->entity->chapter_id) {
            $chain[] = 'chapter:' . $this->entity->chapter_id;
        }

        if ($this->entity instanceof Page || $this->entity instanceof Chapter) {
            $chain[] = 'book:' . $this->entity->book_id;
        }

        return $chain;
    }

    protected function isUserSystemAdmin(): bool
    {
        $adminRoleId = Role::getSystemRole('admin')->id;
        return in_array($adminRoleId, $this->userRoleIds);
    }
}
