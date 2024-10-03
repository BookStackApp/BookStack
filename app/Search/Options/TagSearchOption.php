<?php

namespace BookStack\Search\Options;

class TagSearchOption extends SearchOption
{
    public function toString(): string
    {
        return ($this->negated ? '-' : '') . "[{$this->value}]";
    }
}
