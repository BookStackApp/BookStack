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
}
