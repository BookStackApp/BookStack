<?php

namespace BookStack\Search\Options;

class FilterSearchOption extends SearchOption
{
    protected string $name;

    public function __construct(
        string $value,
        string $name,
        bool $negated = false,
    ) {
        parent::__construct($value, $negated);
        $this->name = $name;
    }

    public function toString(): string
    {
        $valueText = ($this->value ? ':' . $this->value : '');
        $filterBrace = '{' . $this->name .  $valueText . '}';
        return ($this->negated ? '-' : '') . $filterBrace;
    }

    public function getKey(): string
    {
        return $this->name;
    }

    public static function fromContentString(string $value, bool $negated = false): self
    {
        $explodedFilter = explode(':', $value, 2);
        $filterValue = (count($explodedFilter) > 1) ? $explodedFilter[1] : '';
        $filterName = $explodedFilter[0];
        return new self($filterValue, $filterName, $negated);
    }
}
