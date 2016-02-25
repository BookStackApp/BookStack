<?php namespace BookStack\Repos;


use BookStack\Book;
use BookStack\Chapter;
use BookStack\Page;

class EntityRepo
{

    public $book;
    public $chapter;
    public $page;

    /**
     * EntityService constructor.
     * @param $book
     * @param $chapter
     * @param $page
     */
    public function __construct(Book $book, Chapter $chapter, Page $page)
    {
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
    }

    /**
     * Get the latest books added to the system.
     * @param $count
     * @param $page
     */
    public function getRecentlyCreatedBooks($count = 20, $page = 0)
    {
        return $this->book->orderBy('created_at', 'desc')->skip($page*$count)->take($count)->get();
    }

    /**
     * Get the most recently updated books.
     * @param $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyUpdatedBooks($count = 20, $page = 0)
    {
        return $this->book->orderBy('updated_at', 'desc')->skip($page*$count)->take($count)->get();
    }

    /**
     * Get the latest pages added to the system.
     * @param $count
     * @param $page
     */
    public function getRecentlyCreatedPages($count = 20, $page = 0)
    {
        return $this->page->orderBy('created_at', 'desc')->skip($page*$count)->take($count)->get();
    }

    /**
     * Get the most recently updated pages.
     * @param $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyUpdatedPages($count = 20, $page = 0)
    {
        return $this->page->orderBy('updated_at', 'desc')->skip($page*$count)->take($count)->get();
    }


}