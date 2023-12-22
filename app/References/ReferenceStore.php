<?php

namespace BookStack\References;

use BookStack\Entities\EntityProvider;
use BookStack\Entities\Models\Entity;
use Illuminate\Database\Eloquent\Collection;

class ReferenceStore
{
    public function __construct(
        protected EntityProvider $entityProvider
    ) {
    }

    /**
     * Update the outgoing references for the given entity.
     */
    public function updateForEntity(Entity $entity): void
    {
        $this->updateForEntities([$entity]);
    }

    /**
     * Update the outgoing references for all entities in the system.
     */
    public function updateForAll(): void
    {
        Reference::query()->delete();

        foreach ($this->entityProvider->all() as $entity) {
            $entity->newQuery()->select(['id', $entity->htmlField])->chunk(100, function (Collection $entities) {
                $this->updateForEntities($entities->all());
            });
        }
    }

    /**
     * Update the outgoing references for the entities in the given array.
     *
     * @param Entity[] $entities
     */
    protected function updateForEntities(array $entities): void
    {
        if (count($entities) === 0) {
            return;
        }

        $parser = CrossLinkParser::createWithEntityResolvers();
        $references = [];

        $this->dropReferencesFromEntities($entities);

        foreach ($entities as $entity) {
            $models = $parser->extractLinkedModels($entity->getAttribute($entity->htmlField));

            foreach ($models as $model) {
                $references[] = [
                    'from_id'   => $entity->id,
                    'from_type' => $entity->getMorphClass(),
                    'to_id'     => $model->id,
                    'to_type'   => $model->getMorphClass(),
                ];
            }
        }

        foreach (array_chunk($references, 1000) as $referenceDataChunk) {
            Reference::query()->insert($referenceDataChunk);
        }
    }

    /**
     * Delete all the existing references originating from the given entities.
     * @param Entity[] $entities
     */
    protected function dropReferencesFromEntities(array $entities): void
    {
        $IdsByType = [];

        foreach ($entities as $entity) {
            $type = $entity->getMorphClass();
            if (!isset($IdsByType[$type])) {
                $IdsByType[$type] = [];
            }

            $IdsByType[$type][] = $entity->id;
        }

        foreach ($IdsByType as $type => $entityIds) {
            Reference::query()
                ->where('from_type', '=', $type)
                ->whereIn('from_id', $entityIds)
                ->delete();
        }
    }
}
