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

    public function destroyById($id)
    {
        $book = $this->getById($id);
        foreach($book->pages as $page) {
            $this->pageRepo->destroyById($page->id);
        }
        $book->delete();
    }

    public function getTree($book, $currentPageId = false)
    {
        $tree = $book->toArray();
        $tree['pages'] = $this->pageRepo->getTreeByBookId($book->id, $currentPageId);
        $tree['hasChildren'] = count($tree['pages']) > 0;
        return $tree;
    }

}