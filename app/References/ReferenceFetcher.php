<?php

namespace BookStack\References;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Tools\MixedEntityListLoader;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

class ReferenceFetcher
{
    public function __construct(
        protected PermissionApplicator $permissions,
        protected MixedEntityListLoader $mixedEntityListLoader,
    ) {
    }

    /**
     * Query and return the references pointing to the given entity.
     * Loads the commonly required relations while taking permissions into account.
     */
    public function getReferencesToEntity(Entity $entity): Collection
    {
        $references = $this->queryReferencesToEntity($entity)->get();
        $this->mixedEntityListLoader->loadIntoRelations($references->all(), 'from', true);

        return $references;
    }

    /**
     * Returns the count of references pointing to the given entity.
     * Takes permissions into account.
     */
    public function getReferenceCountToEntity(Entity $entity): int
    {
        return $this->queryReferencesToEntity($entity)->count();
    }

    protected function queryReferencesToEntity(Entity $entity): Builder
    {
        $baseQuery = Reference::query()
            ->where('to_type', '=', $entity->getMorphClass())
            ->where('to_id', '=', $entity->id)
            ->whereHas('from');

        return $this->permissions->restrictEntityRelationQuery(
            $baseQuery,
            'references',
            'from_id',
            'from_type'
        );
    }
}
