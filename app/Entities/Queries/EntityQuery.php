<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\EntityProvider;
use BookStack\Permissions\PermissionApplicator;

abstract class EntityQuery
{
    protected function permissionService(): PermissionApplicator
    {
        return app()->make(PermissionApplicator::class);
    }

    protected function entityProvider(): EntityProvider
    {
        return app()->make(EntityProvider::class);
    }
}
