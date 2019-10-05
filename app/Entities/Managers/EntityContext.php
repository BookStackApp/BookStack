<?php namespace BookStack\Entities\Managers;

use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use Illuminate\Session\Store;

class EntityContext
{
    protected $session;

    protected $KEY_SHELF_CONTEXT_ID = 'context_bookshelf_id';

    /**
     * EntityContextManager constructor.
     */
    public function __construct(Store $session)
    {
        $this->session = $session;
    }

    /**
     * Get the current bookshelf context for the given book.
     */
    public function getContextualShelfForBook(Book $book): ?Bookshelf
    {
        $contextBookshelfId = $this->session->get($this->KEY_SHELF_CONTEXT_ID, null);

        if (!is_int($contextBookshelfId)) {
            return null;
        }

        $shelf = Bookshelf::visible()->find($contextBookshelfId);
        $shelfContainsBook = $shelf && $shelf->contains($book);

        return $shelfContainsBook ? $shelf : null;
    }

    /**
     * Store the current contextual shelf ID.
     * @param int $shelfId
     */
    public function setShelfContext(int $shelfId)
    {
        $this->session->put($this->KEY_SHELF_CONTEXT_ID, $shelfId);
    }

    /**
     * Clear the session stored shelf context id.
     */
    public function clearShelfContext()
    {
        $this->session->forget($this->KEY_SHELF_CONTEXT_ID);
    }
}
