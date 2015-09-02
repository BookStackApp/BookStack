<?php namespace Oxbow\Repos;

use Illuminate\Support\Str;
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

    /**
     * Get a new book instance from request input.
     * @param $input
     * @return Book
     */
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

    public function doesSlugExist($slug, $currentId = false)
    {
        $query = $this->book->where('slug', '=', $slug);
        if($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    public function findSuitableSlug($name, $currentId = false)
    {
        $slug = Str::slug($name);
        while($this->doesSlugExist($slug, $currentId)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

    public function getBySearch($term)
    {
        $terms = explode(' ', preg_quote(trim($term)));
        $books = $this->book->fullTextSearch(['name', 'description'], $terms);
        $words = join('|', $terms);
        foreach ($books as $book) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $book->getExcerpt(100));
            $book->searchSnippet = $result;
        }
        return $books;
    }

}