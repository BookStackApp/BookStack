<?php

namespace BookStack\References;

use BookStack\Entities\Models\Entity;

class ReferenceChangeContext
{
    /**
     * Entity pairs where the first is the old entity and the second is the new entity.
     * @var array<array{0: Entity, 1: Entity}>
     */
    protected array $changes = [];

    public function add(Entity $oldEntity, Entity $newEntity): void
    {
        $this->changes[] = [$oldEntity, $newEntity];
    }

    /**
     * Get all the new entities from the changes.
     */
    public function getNewEntities(): array
    {
        return array_column($this->changes, 1);
    }

    /**
     * Get all the old entities from the changes.
     */
    public function getOldEntities(): array
    {
        return array_column($this->changes, 0);
    }

    public function getNewForOld(Entity $oldEntity): ?Entity
    {
        foreach ($this->changes as [$old, $new]) {
            if ($old->id === $oldEntity->id && $old->type === $oldEntity->type) {
                return $new;
            }
        }
        return null;
    }
}
