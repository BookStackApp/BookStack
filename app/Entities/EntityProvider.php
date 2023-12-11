<?php

namespace BookStack\Entities;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\PageRevision;

/**
 * Class EntityProvider.
 *
 * Provides access to the core entity models.
 * Wrapped up in this provider since they are often used together
 * so this is a neater alternative to injecting all in individually.
 */
class EntityProvider
{
    public Bookshelf $bookshelf;
    public Book $book;
    public Chapter $chapter;
    public Page $page;
    public PageRevision $pageRevision;

    public function __construct()
    {
        $this->bookshelf = new Bookshelf();
        $this->book = new Book();
        $this->chapter = new Chapter();
        $this->page = new Page();
        $this->pageRevision = new PageRevision();
    }

    /**
     * Fetch all core entity types as an associated array
     * with their basic names as the keys.
     *
     * @return array<string, Entity>
     */
    public function all(): array
    {
        return [
            'bookshelf' => $this->bookshelf,
            'book'      => $this->book,
            'chapter'   => $this->chapter,
            'page'      => $this->page,
        ];
    }

    /**
     * Get an entity instance by its basic name.
     */
    public function get(string $type): Entity
    {
        $type = strtolower($type);
        $instance = $this->all()[$type] ?? null;

        if (is_null($instance)) {
            throw new \InvalidArgumentException("Provided type \"{$type}\" is not a valid entity type");
        }

        return $instance;
    }

    /**
     * Get the morph classes, as an array, for a single or multiple types.
     */
    public function getMorphClasses(array $types): array
    {
        $morphClasses = [];
        foreach ($types as $type) {
            $model = $this->get($type);
            $morphClasses[] = $model->getMorphClass();
        }

        return $morphClasses;
    }
}
