<?php namespace BookStack\Repos;

use Activity;
use BookStack\Services\RestrictionService;
use Illuminate\Support\Str;
use BookStack\Book;
use Views;

class BookRepo
{

    protected $book;
    protected $pageRepo;
    protected $chapterRepo;
    protected $restrictionService;

    /**
     * BookRepo constructor.
     * @param Book $book
     * @param PageRepo $pageRepo
     * @param ChapterRepo $chapterRepo
     * @param RestrictionService $restrictionService
     */
    public function __construct(Book $book, PageRepo $pageRepo, ChapterRepo $chapterRepo, RestrictionService $restrictionService)
    {
        $this->book = $book;
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
        $this->restrictionService = $restrictionService;
    }

    /**
     * Base query for getting books.
     * Takes into account any restrictions.
     * @return mixed
     */
    private function bookQuery()
    {
        return $this->restrictionService->enforceBookRestrictions($this->book, 'view');
    }

    /**
     * Get the book that has the given id.
     * @param $id
     * @return mixed
     */
    public function getById($id)
    {
        return $this->bookQuery()->findOrFail($id);
    }

    /**
     * Get all books, Limited by count.
     * @param int $count
     * @return mixed
     */
    public function getAll($count = 10)
    {
        $bookQuery = $this->bookQuery()->orderBy('name', 'asc');
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
        return $this->bookQuery()
            ->orderBy('name', 'asc')->paginate($count);
    }


    /**
     * Get the latest books.
     * @param int $count
     * @return mixed
     */
    public function getLatest($count = 10)
    {
        return $this->bookQuery()->orderBy('created_at', 'desc')->take($count)->get();
    }

    /**
     * Gets the most recently viewed for a user.
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyViewed($count = 10, $page = 0)
    {
        // TODO restrict
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
        // TODO - Restrict
        return Views::getPopular($count, $page, $this->book);
    }

    /**
     * Get a book by slug
     * @param $slug
     * @return mixed
     */
    public function getBySlug($slug)
    {
        $book = $this->bookQuery()->where('slug', '=', $slug)->first();
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
        return $this->bookQuery()->where('id', '=', $id)->exists();
    }

    /**
     * Get a new book instance from request input.
     * @param $input
     * @return Book
     */
    public function newFromInput($input)
    {
        return $this->book->newInstance($input);
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
        $pageQuery = $book->pages()->where('chapter_id', '=', 0);
        $this->restrictionService->enforcePageRestrictions($pageQuery, 'view');
        $pages = $pageQuery->get();

        $chapterQuery = $book->chapters()->with('pages');
        $this->restrictionService->enforceChapterRestrictions($chapterQuery, 'view');
        $chapters = $chapterQuery->get();
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
        $terms = explode(' ', $term);
        $books = $this->restrictionService->enforceBookRestrictions($this->book->fullTextSearchQuery(['name', 'description'], $terms))
            ->paginate($count)->appends($paginationAppends);
        $words = join('|', explode(' ', preg_quote(trim($term), '/')));
        foreach ($books as $book) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $book->getExcerpt(100));
            $book->searchSnippet = $result;
        }
        return $books;
    }

    /**
     * Updates books restrictions from a request
     * @param $request
     * @param $book
     */
    public function updateRestrictionsFromRequest($request, $book)
    {
        // TODO - extract into shared repo
        $book->restricted = $request->has('restricted') && $request->get('restricted') === 'true';
        $book->restrictions()->delete();
        if ($request->has('restrictions')) {
            foreach ($request->get('restrictions') as $roleId => $restrictions) {
                foreach ($restrictions as $action => $value) {
                    $book->restrictions()->create([
                        'role_id' => $roleId,
                        'action' => strtolower($action)
                    ]);
                }
            }
        }
        $book->save();
    }

}