<?php

namespace BookStack\Search\Vectors;

class VectorSearchRunner
{
    public function __construct(
        protected VectorQueryServiceProvider $vectorQueryServiceProvider
    ) {
    }

    public function run(string $query): array
    {
        $queryService = $this->vectorQueryServiceProvider->get();
        $queryVector = $queryService->generateEmbeddings($query);

        // TODO - Apply permissions
        // TODO - Join models
        $topMatches = SearchVector::query()->select('text', 'entity_type', 'entity_id')
            ->selectRaw('VEC_DISTANCE_COSINE(VEC_FROMTEXT("[' . implode(',', $queryVector) . ']"), embedding) as distance')
            ->orderBy('distance', 'asc')
            ->having('distance', '<', 0.6)
            ->limit(10)
            ->get();

        $matchesText = array_values(array_map(fn (SearchVector $match) => $match->text, $topMatches->all()));
        $llmResult = $queryService->query($query, $matchesText);

        return [
            'llm_result' => $llmResult,
            'entity_matches' => $topMatches->toArray()
        ];
    }
}
