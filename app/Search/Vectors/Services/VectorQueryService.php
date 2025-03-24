<?php

namespace BookStack\Search\Vectors\Services;

interface VectorQueryService
{
    /**
     * Generate embedding vectors from the given chunk of text.
     * @return float[]
     */
    public function generateEmbeddings(string $text): array;

    /**
     * Query the LLM service using the given user input, and
     * relevant context text retrieved locally via a vector search.
     * Returns the response output text from the LLM.
     *
     * @param string[] $context
     */
    public function query(string $input, array $context): string;
}
