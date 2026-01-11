<?php

namespace BookStack\Search\Queries;

use BookStack\Entities\Models\Entity;
use BookStack\Search\SearchRunner;
use Exception;

class LlmQueryRunner
{
    public function __construct(
        protected LlmQueryServiceProvider $vectorQueryServiceProvider,
        protected SearchRunner $searchRunner,
    ) {
    }

    /**
     * Transform the given query into an array of terms which can be used
     * to search for documents to help answer that query.
     * @return string[]
     * @throws Exception
     */
    public function queryToSearchTerms(string $query): array
    {
        $queryService = $this->vectorQueryServiceProvider->get();

        return $queryService->queryToSearchTerms($query);
    }

    /**
     * Run a query against the configured LLM to produce a text response.
     * @param Entity[] $searchResults
     * @throws Exception
     */
    public function run(string $query, array $searchResults): string
    {
        $queryService = $this->vectorQueryServiceProvider->get();
        return $queryService->query($query, $searchResults);
    }
}
