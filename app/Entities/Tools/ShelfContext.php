<?php

namespace BookStack\Entities\Tools;

use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;

class ShelfContext
{
    protected $KEY_SHELF_CONTEXT_ID = 'context_bookshelf_id';

    /**
     * Get the current bookshelf context for the given book.
     */
    public function getContextualShelfForBook(Book $book): ?Bookshelf
    {
        $contextBookshelfId = session()->get($this->KEY_SHELF_CONTEXT_ID, null);

        if (!is_int($contextBookshelfId)) {
            return null;
        }

        /** @var Bookshelf $shelf */
        $shelf = Bookshelf::visible()->find($contextBookshelfId);
        $shelfContainsBook = $shelf && $shelf->contains($book);

        return $shelfContainsBook ? $shelf : null;
    }

    /**
     * Store the current contextual shelf ID.
     */
    public function setShelfContext(int $shelfId)
    {
        session()->put($this->KEY_SHELF_CONTEXT_ID, $shelfId);
    }

    /**
     * Clear the session stored shelf context id.
     */
    public function clearShelfContext()
    {
        session()->forget($this->KEY_SHELF_CONTEXT_ID);
    }
}
