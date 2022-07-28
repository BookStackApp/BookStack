<?php

namespace BookStack\Entities\Queries;

use BookStack\Auth\Permissions\PermissionApplicator;
use BookStack\Entities\EntityProvider;

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
