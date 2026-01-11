<?php

namespace BookStack\Search\Queries\Services;

use BookStack\Entities\Models\Entity;

interface LlmQueryService
{
    /**
     * Generate embedding vectors from the given chunk of text.
     * @return float[]
     */
    public function generateEmbeddings(string $text): array;

    public function queryToSearchTerms(string $text): array;

    /**
     * Query the LLM service using the given user input, and
     * relevant entity content retrieved locally via a search.
     * Returns the response output text from the LLM.
     *
     * @param Entity[] $context
     */
    public function query(string $input, array $context): string;
}
