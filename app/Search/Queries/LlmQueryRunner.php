<?php

namespace BookStack\Search\Queries;

use Exception;

class LlmQueryRunner
{
    public function __construct(
        protected VectorQueryServiceProvider $vectorQueryServiceProvider,
    ) {
    }

    /**
     * Run a query against the configured LLM to produce a text response.
     * @param VectorSearchResult[] $vectorResults
     * @throws Exception
     */
    public function run(string $query, array $vectorResults): string
    {
        $queryService = $this->vectorQueryServiceProvider->get();

        $matchesText = array_values(array_map(fn (VectorSearchResult $result) => $result->matchText, $vectorResults));
        return $queryService->query($query, $matchesText);
    }
}
