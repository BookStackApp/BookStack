<?php namespace BookStack\Entities;

use BookStack\Entities\Repos\EntityRepo;
use Illuminate\Session\Store;

class EntityContextManager
{
    protected $session;
    protected $entityRepo;

    protected $KEY_SHELF_CONTEXT_ID = 'context_bookshelf_id';

    /**
     * EntityContextManager constructor.
     * @param Store $session
     * @param EntityRepo $entityRepo
     */
    public function __construct(Store $session, EntityRepo $entityRepo)
    {
        $this->session = $session;
        $this->entityRepo = $entityRepo;
    }

    /**
     * Get the current bookshelf context for the given book.
     * @param Book $book
     * @return Bookshelf|null
     */
    public function getContextualShelfForBook(Book $book)
    {
        $contextBookshelfId = $this->session->get($this->KEY_SHELF_CONTEXT_ID, null);
        if (is_int($contextBookshelfId)) {

            /** @var Bookshelf $shelf */
            $shelf = $this->entityRepo->getById('bookshelf', $contextBookshelfId);

            if ($shelf && $shelf->contains($book)) {
                return $shelf;
            }
        }
        return null;
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
