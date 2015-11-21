<?php namespace BookStack\Repos;

use BookStack\Activity;
use Illuminate\Support\Str;
use BookStack\Book;
use Views;

class BookRepo
{

    protected $book;
    protected $pageRepo;

    /**
     * BookRepo constructor.
     * @param Book     $book
     * @param PageRepo $pageRepo
     */
    public function __construct(Book $book, PageRepo $pageRepo)
    {
        $this->book = $book;
        $this->pageRepo = $pageRepo;
    }

    /**
     * Get the book that has the given id.
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->book->findOrFail($id);
    }

    /**
     * Get all books, Limited by count.
     * @param int $count
     * @return mixed
     */
    public function getAll($count = 10)
    {
        return $this->book->orderBy('name', 'asc')->take($count)->get();
    }

    /**
     * Get all books paginated.
     * @param int $count
     * @return mixed
     */
    public function getAllPaginated($count = 10)
    {
        return $this->book->orderBy('name', 'asc')->paginate($count);
    }

    public function getRecentlyViewed($count = 10, $page = 0)
    {
        return Views::getUserRecentlyViewed($count, $page, $this->book);
    }

    /**
     * Get a book by slug
     * @param $slug
     * @return mixed
     */
    public function getBySlug($slug)
    {
        return $this->book->where('slug', '=', $slug)->first();
    }

    /**
     * Checks if a book exists.
     * @param $id
     * @return bool
     */
    public function exists($id)
    {
        return $this->book->where('id', '=', $id)->exists();
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

    /**
     * Count the amount of books that have a specific slug.
     * @param $slug
     * @return mixed
     */
    public function countBySlug($slug)
    {
        return $this->book->where('slug', '=', $slug)->count();
    }

    /**
     * Destroy a book identified by the given slug.
     * @param $bookSlug
     */
    public function destroyBySlug($bookSlug)
    {
        $book = $this->getBySlug($bookSlug);
        foreach ($book->pages as $page) {
            \Activity::removeEntity($page);
            $page->delete();
        }
        foreach ($book->chapters as $chapter) {
            \Activity::removeEntity($chapter);
            $chapter->delete();
        }
        $book->delete();
    }

    /**
     * Get the next child element priority.
     * @param Book $book
     * @return int
     */
    public function getNewPriority($book)
    {
        $lastElem = $book->children()->pop();
        return $lastElem ? $lastElem->priority + 1 : 0;
    }

    /**
     * @param string     $slug
     * @param bool|false $currentId
     * @return bool
     */
    public function doesSlugExist($slug, $currentId = false)
    {
        $query = $this->book->where('slug', '=', $slug);
        if ($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    /**
     * Provides a suitable slug for the given book name.
     * Ensures the returned slug is unique in the system.
     * @param string     $name
     * @param bool|false $currentId
     * @return string
     */
    public function findSuitableSlug($name, $currentId = false)
    {
        $originalSlug = Str::slug($name);
        $slug = $originalSlug;
        $count = 2;
        while ($this->doesSlugExist($slug, $currentId)) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }
        return $slug;
    }

    /**
     * Get books by search term.
     * @param $term
     * @return mixed
     */
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