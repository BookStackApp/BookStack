<?php

namespace BookStack\Entities\Queries;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Entities\EntityProvider;

abstract class EntityQuery
{
    protected function permissionService(): PermissionService
    {
        return app()->make(PermissionService::class);
    }

    protected function entityProvider(): EntityProvider
    {
        return app()->make(EntityProvider::class);
    }
}
