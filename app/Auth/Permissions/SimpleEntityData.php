<?php

namespace BookStack\Auth\Permissions;

class SimpleEntityData
{
    public int $id;
    public string $type;
    public ?int $book_id;
    public ?int $chapter_id;
}
