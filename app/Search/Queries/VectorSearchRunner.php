<?php

namespace BookStack\Search\Queries;

use BookStack\Entities\Tools\MixedEntityListLoader;
use BookStack\Permissions\PermissionApplicator;
use Exception;

class VectorSearchRunner
{
    public function __construct(
        protected VectorQueryServiceProvider $vectorQueryServiceProvider,
        protected PermissionApplicator $permissions,
        protected MixedEntityListLoader $entityLoader,
    ) {
    }

    /**
     * Run a vector search query to find results across entities.
     * @return VectorSearchResult[]
     * @throws Exception
     */
    public function run(string $query): array
    {
        $queryService = $this->vectorQueryServiceProvider->get();
        $queryVector = $queryService->generateEmbeddings($query);

        // TODO - Test permissions applied
        $topMatchesQuery = SearchVector::query()->select('text', 'entity_type', 'entity_id')
            ->selectRaw('VEC_DISTANCE_COSINE(VEC_FROMTEXT("[' . implode(',', $queryVector) . ']"), embedding) as distance')
            ->orderBy('distance', 'asc')
            ->having('distance', '<', 0.6)
            ->limit(10);

        $query = $this->permissions->restrictEntityRelationQuery($topMatchesQuery, 'search_vectors', 'entity_id', 'entity_type');
        $topMatches = $query->get();

        $this->entityLoader->loadIntoRelations($topMatches->all(), 'entity', true);

        $results = [];

        foreach ($topMatches as $match) {
            if ($match->relationLoaded('entity')) {
                $results[] = new VectorSearchResult(
                    $match->getRelation('entity'),
                    $match->getAttribute('distance'),
                    $match->getAttribute('text'),
                );
            }
        }

        return $results;
    }
}
