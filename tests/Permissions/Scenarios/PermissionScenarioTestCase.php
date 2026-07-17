<?php

namespace Tests\Permissions\Scenarios;

use BookStack\Entities\Models\Entity;
use BookStack\Users\Models\User;
use Tests\TestCase;

// Cases defined in dev/docs/permission-scenario-testing.md

class PermissionScenarioTestCase extends TestCase
{
    protected function assertVisibleToUser(Entity $entity, User $user)
    {
        $this->actingAs($user);
        $funcView = userCan($entity->getMorphClass() . '-view', $entity);
        $queryView = $entity->newQuery()->scopes(['visible'])->find($entity->id) !== null;

        $id = $entity->getMorphClass() . ':' . $entity->id;
        $msg = "Item [{$id}] should be visible but was not found via ";
        $msg .= implode(' and ', array_filter([!$funcView ? 'userCan' : '', !$queryView ? 'query' : '']));

        static::assertTrue($funcView && $queryView, $msg);
    }

    protected function assertNotVisibleToUser(Entity $entity, User $user)
    {
        $this->actingAs($user);
        $funcView = userCan($entity->getMorphClass() . '-view', $entity);
        $queryView = $entity->newQuery()->scopes(['visible'])->find($entity->id) !== null;

        $id = $entity->getMorphClass() . ':' . $entity->id;
        $msg = "Item [{$id}] should not be visible but was found via ";
        $msg .= implode(' and ', array_filter([$funcView ? 'userCan' : '', $queryView ? 'query' : '']));

        static::assertTrue(!$funcView && !$queryView, $msg);
    }
}
