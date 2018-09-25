<?php namespace BookStack\Entities;

/**
 * Class EntityProvider
 *
 * Provides access to the core entity models.
 * Wrapped up in this provider since they are often used together
 * so this is a neater alternative to injecting all in individually.
 *
 * @package BookStack\Entities
 */
class EntityProvider
{

    /**
     * @var Bookshelf
     */
    protected $bookshelf;

    /**
     * @var Book
     */
    protected $book;

    /**
     * @var Chapter
     */
    protected $chapter;

    /**
     * @var Page
     */
    protected $page;

    /**
     * @var PageRevision
     */
    protected $pageRevision;

    /**
     * EntityProvider constructor.
     * @param Bookshelf $bookshelf
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param PageRevision $pageRevision
     */
    public function __construct(
        Bookshelf $bookshelf,
        Book $book,
        Chapter $chapter,
        Page $page,
        PageRevision $pageRevision
    ) {
        $this->bookshelf = $bookshelf;
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        $this->pageRevision = $pageRevision;
    }

    /**
     * Fetch all core entity types as an associated array
     * with their basic names as the keys.
     * @return Entity[]
     */
    public function all()
    {
        return [
            'bookshelf' => $this->bookshelf,
            'book' => $this->book,
            'chapter' => $this->chapter,
            'page' => $this->page,
        ];
    }


}