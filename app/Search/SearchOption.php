<?php

namespace BookStack\Search;

class SearchOption
{
    public function __construct(
        public string $value,
        public bool $negated = false,
    ) {
    }
}
