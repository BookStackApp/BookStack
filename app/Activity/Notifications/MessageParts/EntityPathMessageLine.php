<?php

namespace BookStack\Activity\Notifications\MessageParts;

use BookStack\Entities\Models\Entity;
use Illuminate\Contracts\Support\Htmlable;
use Stringable;

/**
 * A link to a specific entity in the system, with the text showing its name.
 */
class EntityPathMessageLine implements Htmlable, Stringable
{
    /**
     * @var EntityLinkMessageLine[]
     */
    protected array $entityLinks;

    public function __construct(
        protected array $entities
    ) {
        $this->entityLinks = array_map(fn (Entity $entity) => new EntityLinkMessageLine($entity, 24), $this->entities);
    }

    public function toHtml(): string
    {
        $entityHtmls = array_map(fn (EntityLinkMessageLine $line) => $line->toHtml(), $this->entityLinks);
        return implode(' &gt; ', $entityHtmls);
    }

    public function __toString(): string
    {
        return implode(' > ', $this->entityLinks);
    }
}
