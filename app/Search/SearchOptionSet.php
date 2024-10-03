<?php

namespace BookStack\Search;

use BookStack\Search\Options\SearchOption;

class SearchOptionSet
{
    /**
     * @var SearchOption[]
     */
    protected array $options = [];

    public function __construct(array $options = [])
    {
        $this->options = $options;
    }

    public function toValueArray(): array
    {
        return array_map(fn(SearchOption $option) => $option->value, $this->options);
    }

    public function toValueMap(): array
    {
        $map = [];
        foreach ($this->options as $index => $option) {
            $key = $option->getKey() ?? $index;
            $map[$key] = $option->value;
        }
        return $map;
    }

    public function merge(SearchOptionSet $set): self
    {
        return new self(array_merge($this->options, $set->options));
    }

    public function filterEmpty(): self
    {
        $filteredOptions = array_values(array_filter($this->options, fn (SearchOption $option) => !empty($option->value)));
        return new self($filteredOptions);
    }

    /**
     * @param class-string<SearchOption> $class
     */
    public static function fromValueArray(array $values, string $class): self
    {
        $options = array_map(fn($val) => new $class($val), $values);
        return new self($options);
    }

    /**
     * @return SearchOption[]
     */
    public function all(): array
    {
        return $this->options;
    }

    /**
     * @return SearchOption[]
     */
    public function negated(): array
    {
        return array_values(array_filter($this->options, fn (SearchOption $option) => $option->negated));
    }
}
