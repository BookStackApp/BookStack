<?php

namespace BookStack\Activity\Notifications\MessageParts;

use BookStack\Entities\Models\Entity;
use Illuminate\Contracts\Support\Htmlable;
use Stringable;

/**
 * A link to a specific entity in the system, with the text showing its name.
 */
class EntityLinkMessageLine implements Htmlable, Stringable
{
    public function __construct(
        protected Entity $entity,
        protected int $nameLength = 120,
    ) {
    }

    public function toHtml(): string
    {
        return '<a href="' . e($this->entity->getUrl()) . '">' . e($this->entity->getShortName($this->nameLength)) . '</a>';
    }

    public function __toString(): string
    {
        return "{$this->entity->getShortName($this->nameLength)} ({$this->entity->getUrl()})";
    }
}
