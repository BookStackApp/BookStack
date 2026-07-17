<?php

namespace BookStack\Entities;

use Illuminate\Validation\Rules\Exists;

class EntityExistsRule implements \Stringable
{
    public function __construct(
        protected string $type,
    ) {
    }

    public function __toString()
    {
        $existsRule = (new Exists('entities', 'id'))
            ->where('type', $this->type);
        return $existsRule->__toString();
    }
}
