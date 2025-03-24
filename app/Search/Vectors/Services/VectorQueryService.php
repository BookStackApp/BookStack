<?php

namespace BookStack\Search\Vectors\Services;

interface VectorQueryService
{
    /**
     * Generate embedding vectors from the given chunk of text.
     * @return float[]
     */
    public function generateEmbeddings(string $text): array;
}
