<?php

declare(strict_types=1);

namespace BookStack\Search\Queries;

use BookStack\Entities\Models\Entity;

readonly class VectorSearchResult
{
    public function __construct(
        public Entity $entity,
        public float $distance,
        public string $matchText
    ) {
    }
}
