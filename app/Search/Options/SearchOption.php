<?php

namespace BookStack\Search\Options;

abstract class SearchOption
{
    public function __construct(
        public string $value,
        public bool $negated = false,
    ) {
    }

    /**
     * Get the key used for this option when used in a map.
     * Null indicates to use the index of the containing array.
     */
    public function getKey(): string|null
    {
        return null;
    }

    /**
     * Get the search string representation for this search option.
     */
    abstract public function toString(): string;
}
