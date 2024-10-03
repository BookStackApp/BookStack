<?php

namespace BookStack\Search;

use BookStack\Search\Options\SearchOption;

/**
 * @template T of SearchOption
 */
class SearchOptionSet
{
    /**
     * @var T[]
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
     * @return T[]
     */
    public function all(): array
    {
        return $this->options;
    }

    /**
     * @return self<T>
     */
    public function negated(): self
    {
        $values = array_values(array_filter($this->options, fn (SearchOption $option) => $option->negated));
        return new self($values);
    }

    /**
     * @return self<T>
     */
    public function nonNegated(): self
    {
        $values = array_values(array_filter($this->options, fn (SearchOption $option) => !$option->negated));
        return new self($values);
    }
}
