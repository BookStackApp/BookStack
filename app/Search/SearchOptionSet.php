<?php

namespace BookStack\Search;

class SearchOptionSet
{
    /**
     * @var SearchOption[]
     */
    public array $options = [];

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
        foreach ($this->options as $key => $option) {
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
        $filteredOptions = array_filter($this->options, fn (SearchOption $option) => !empty($option->value));
        return new self($filteredOptions);
    }

    public static function fromValueArray(array $values): self
    {
        $options = array_map(fn($val) => new SearchOption($val), $values);
        return new self($options);
    }

    public static function fromMapArray(array $values): self
    {
        $options = [];
        foreach ($values as $key => $value) {
            $options[$key] = new SearchOption($value);
        }
        return new self($options);
    }
}
