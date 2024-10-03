<?php

namespace BookStack\Search\Options;

class ExactSearchOption extends SearchOption
{
    public function toString(): string
    {
        $escaped = str_replace('\\', '\\\\', $this->value);
        $escaped = str_replace('"', '\"', $escaped);
        return ($this->negated ? '-' : '') . '"' . $escaped . '"';
    }
}
