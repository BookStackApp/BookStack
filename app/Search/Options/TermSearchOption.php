<?php

namespace BookStack\Search\Options;

class TermSearchOption extends SearchOption
{
    public function toString(): string
    {
        return $this->value;
    }
}
