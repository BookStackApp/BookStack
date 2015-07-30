<?php namespace Oxbow\Repos;

use Oxbow\Book;

class BookRepo
{

    protected $book;
    protected $pageRepo;

    /**
     * BookRepo constructor.
     * @param Book $book
     * @param PageRepo $pageRepo
     */
    public function __construct(Book $book, PageRepo $pageRepo)
    {
        $this->book = $book;
        $this->pageRepo = $pageRepo;
    }

    public function getById($id)
    {
        return $this->book->findOrFail($id);
    }

    public function getAll()
    {
        return $this->book->all();
    }

    public function getBySlug($slug)
    {
        return $this->book->where('slug', '=', $slug)->first();
    }

    public function newFromInput($input)
    {
        return $this->book->fill($input);
    }

    public function countBySlug($slug)
    {
        return $this->book->where('slug', '=', $slug)->count();
    }

    public function destroyBySlug($bookSlug)
    {
        $book = $this->getBySlug($bookSlug);
        foreach($book->pages as $page) {
            $page->delete();
        }
        foreach($book->chapters as $chapter) {
            $chapter->delete();
        }
        $book->delete();
    }

    public function getNewPriority($book)
    {
        $lastElem = $book->children()->pop();
        return $lastElem ? $lastElem->priority + 1 : 0;
    }

}