<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Queries\BookshelfQueries;

class ShelfContext
{
    protected string $KEY_SHELF_CONTEXT_ID = 'context_bookshelf_id';

    public function __construct(
        protected BookshelfQueries $shelfQueries,
    ) {
    }

    /**
     * Get the current bookshelf context for the given book.
     */
    public function getContextualShelfForBook(Book $book): ?Bookshelf
    {
        $contextBookshelfId = session()->get($this->KEY_SHELF_CONTEXT_ID, null);

        if (!is_int($contextBookshelfId)) {
            return null;
        }

        $shelf = $this->shelfQueries->findVisibleById($contextBookshelfId);
        $shelfContainsBook = $shelf && $shelf->contains($book);

        return $shelfContainsBook ? $shelf : null;
    }

    /**
     * Store the current contextual shelf ID.
     */
    public function setShelfContext(int $shelfId): void
    {
        session()->put($this->KEY_SHELF_CONTEXT_ID, $shelfId);
    }

    /**
     * Clear the session stored shelf context id.
     */
    public function clearShelfContext(): void
    {
        session()->forget($this->KEY_SHELF_CONTEXT_ID);
    }
}
