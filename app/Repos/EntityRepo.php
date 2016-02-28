<?php namespace BookStack\Repos;


use BookStack\Book;
use BookStack\Chapter;
use BookStack\Page;
use BookStack\Services\RestrictionService;

class EntityRepo
{

    public $book;
    public $chapter;
    public $page;
    private $restrictionService;

    /**
     * EntityService constructor.
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param RestrictionService $restrictionService
     */
    public function __construct(Book $book, Chapter $chapter, Page $page, RestrictionService $restrictionService)
    {
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        $this->restrictionService = $restrictionService;
    }

    /**
     * Get the latest books added to the system.
     * @param $count
     * @param $page
     */
    public function getRecentlyCreatedBooks($count = 20, $page = 0)
    {
        return $this->restrictionService->enforceBookRestrictions($this->book)
            ->orderBy('created_at', 'desc')->skip($page*$count)->take($count)->get();
    }

    /**
     * Get the most recently updated books.
     * @param $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyUpdatedBooks($count = 20, $page = 0)
    {
        return $this->restrictionService->enforceBookRestrictions($this->book)
            ->orderBy('updated_at', 'desc')->skip($page*$count)->take($count)->get();
    }

    /**
     * Get the latest pages added to the system.
     * @param $count
     * @param $page
     */
    public function getRecentlyCreatedPages($count = 20, $page = 0)
    {
        return $this->restrictionService->enforcePageRestrictions($this->page)
            ->orderBy('created_at', 'desc')->skip($page*$count)->take($count)->get();
    }

    /**
     * Get the most recently updated pages.
     * @param $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyUpdatedPages($count = 20, $page = 0)
    {
        return $this->restrictionService->enforcePageRestrictions($this->page)
            ->orderBy('updated_at', 'desc')->skip($page*$count)->take($count)->get();
    }


}