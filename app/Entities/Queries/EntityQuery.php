<?php

namespace BookStack\Entities\Queries;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Tools\MixedEntityListLoader;
use BookStack\Permissions\PermissionApplicator;

abstract class EntityQuery
{
    protected function mixedEntityListLoader(): MixedEntityListLoader
    {
        return app()->make(MixedEntityListLoader::class);
    }

    protected function permissionService(): PermissionApplicator
    {
        return app()->make(PermissionApplicator::class);
    }

    protected function entityProvider(): EntityProvider
    {
        return app()->make(EntityProvider::class);
    }
}
