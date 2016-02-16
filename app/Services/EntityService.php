<?php namespace BookStack\Services;


use BookStack\Book;
use BookStack\Chapter;
use BookStack\Page;

class EntityService
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


}