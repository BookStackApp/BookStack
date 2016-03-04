<?php namespace BookStack\Repos;

use Activity;
use Illuminate\Support\Str;
use BookStack\Book;
use Views;

class BookRepo
{

    protected $book;
    protected $pageRepo;
    protected $chapterRepo;

    /**
     * BookRepo constructor.
     * @param Book $book
     * @param PageRepo $pageRepo
     * @param ChapterRepo $chapterRepo
     */
    public function __construct(Book $book, PageRepo $pageRepo, ChapterRepo $chapterRepo)
    {
        $this->book = $book;
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
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
        $bookQuery = $this->book->orderBy('name', 'asc');
        if (!$count) return $bookQuery->get();
        return $bookQuery->take($count)->get();
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


    /**
     * Get the latest books.
     * @param int $count
     * @return mixed
     */
    public function getLatest($count = 10)
    {
        return $this->book->orderBy('created_at', 'desc')->take($count)->get();
    }

    /**
     * Gets the most recently viewed for a user.
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyViewed($count = 10, $page = 0)
    {
        return Views::getUserRecentlyViewed($count, $page, $this->book);
    }

    /**
     * Gets the most viewed books.
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getPopular($count = 10, $page = 0)
    {
        return Views::getPopular($count, $page, $this->book);
    }

    /**
     * Get a book by slug
     * @param $slug
     * @return mixed
     */
    public function getBySlug($slug)
    {
        $book = $this->book->where('slug', '=', $slug)->first();
        if ($book === null) abort(404);
        return $book;
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
            $this->pageRepo->destroy($page);
        }
        foreach ($book->chapters as $chapter) {
            $this->chapterRepo->destroy($chapter);
        }
        $book->views()->delete();
        $book->delete();
    }

    /**
     * Get the next child element priority.
     * @param Book $book
     * @return int
     */
    public function getNewPriority($book)
    {
        $lastElem = $this->getChildren($book)->pop();
        return $lastElem ? $lastElem->priority + 1 : 0;
    }

    /**
     * @param string $slug
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
     * @param string $name
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
     * Get all child objects of a book.
     * Returns a sorted collection of Pages and Chapters.
     * Loads the bookslug onto child elements to prevent access database access for getting the slug.
     * @param Book $book
     * @return mixed
     */
    public function getChildren(Book $book)
    {
        $pages = $book->pages()->where('chapter_id', '=', 0)->get();
        $chapters = $book->chapters()->with('pages')->get();
        $children = $pages->merge($chapters);
        $bookSlug = $book->slug;
        $children->each(function ($child) use ($bookSlug) {
            $child->setAttribute('bookSlug', $bookSlug);
            if ($child->isA('chapter')) {
                $child->pages->each(function ($page) use ($bookSlug) {
                    $page->setAttribute('bookSlug', $bookSlug);
                });
            }
        });
        return $children->sortBy('priority');
    }

    /**
     * Get books by search term.
     * @param $term
     * @param int $count
     * @param array $paginationAppends
     * @return mixed
     */
    public function getBySearch($term, $count = 20, $paginationAppends = [])
    {
        preg_match_all('/"(.*?)"/', $term, $matches);
        if (count($matches[1]) > 0) {
            $terms = $matches[1];
            $term = trim(preg_replace('/"(.*?)"/', '', $term));
        } else {
            $terms = [];
        }
        if (!empty($term)) {
            $terms = array_merge($terms, explode(' ', $term));
        }
        $books = $this->book->fullTextSearchQuery(['name', 'description'], $terms)
            ->paginate($count)->appends($paginationAppends);
        $words = join('|', explode(' ', preg_quote(trim($term), '/')));
        foreach ($books as $book) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $book->getExcerpt(100));
            $book->searchSnippet = $result;
        }
        return $books;
    }

}