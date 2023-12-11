<?php

namespace BookStack\Permissions;

use BookStack\Entities\Models\Entity;

class SimpleEntityData
{
    public int $id;
    public string $type;
    public int $owned_by;
    public ?int $book_id;
    public ?int $chapter_id;

    public static function fromEntity(Entity $entity): self
    {
        $attrs = $entity->getAttributes();
        $simple = new self();

        $simple->id = $attrs['id'];
        $simple->type = $entity->getMorphClass();
        $simple->owned_by = $attrs['owned_by'] ?? 0;
        $simple->book_id = $attrs['book_id'] ?? null;
        $simple->chapter_id = $attrs['chapter_id'] ?? null;

        return $simple;
    }
}
