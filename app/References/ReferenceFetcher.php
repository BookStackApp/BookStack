<?php

namespace BookStack\References;

use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\PermissionApplicator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\Relations\Relation;

class ReferenceFetcher
{
    protected PermissionApplicator $permissions;

    public function __construct(PermissionApplicator $permissions)
    {
        $this->permissions = $permissions;
    }

    /**
     * Query and return the page references pointing to the given entity.
     * Loads the commonly required relations while taking permissions into account.
     */
    public function getPageReferencesToEntity(Entity $entity): Collection
    {
        $baseQuery = $this->queryPageReferencesToEntity($entity)
            ->with([
                'from'         => fn (Relation $query) => $query->select(Page::$listAttributes),
                'from.book'    => fn (Relation $query) => $query->scopes('visible'),
                'from.chapter' => fn (Relation $query) => $query->scopes('visible'),
            ]);

        $references = $this->permissions->restrictEntityRelationQuery(
            $baseQuery,
            'references',
            'from_id',
            'from_type'
        )->get();

        return $references;
    }

    /**
     * Returns the count of page references pointing to the given entity.
     * Takes permissions into account.
     */
    public function getPageReferenceCountToEntity(Entity $entity): int
    {
        $count = $this->permissions->restrictEntityRelationQuery(
            $this->queryPageReferencesToEntity($entity),
            'references',
            'from_id',
            'from_type'
        )->count();

        return $count;
    }

    protected function queryPageReferencesToEntity(Entity $entity): Builder
    {
        return Reference::query()
            ->where('to_type', '=', $entity->getMorphClass())
            ->where('to_id', '=', $entity->id)
            ->where('from_type', '=', (new Page())->getMorphClass());
    }
}
