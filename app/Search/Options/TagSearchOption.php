<?php

namespace BookStack\Search\Options;

class TagSearchOption extends SearchOption
{
    /**
     * Acceptable operators to be used within a tag search option.
     *
     * @var string[]
     */
    protected array $queryOperators = ['<=', '>=', '=', '<', '>', 'like', '!='];

    public function toString(): string
    {
        return ($this->negated ? '-' : '') . "[{$this->value}]";
    }

    /**
     * @return array{name: string, operator: string, value: string}
     */
    public function getParts(): array
    {
        $operatorRegex = implode('|', array_map(fn($op) => preg_quote($op), $this->queryOperators));
        preg_match('/^(.*?)((' . $operatorRegex . ')(.*?))?$/', $this->value, $tagSplit);

        $extractedOperator = count($tagSplit) > 2 ? $tagSplit[3] : '';
        $tagOperator = in_array($extractedOperator, $this->queryOperators) ? $extractedOperator : '=';
        $tagValue = count($tagSplit) > 3 ? $tagSplit[4] : '';

        return [
            'name' => $tagSplit[1],
            'operator' => $tagOperator,
            'value' => $tagValue,
        ];
    }
}
