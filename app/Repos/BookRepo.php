<?php namespace BookStack\Repos;

use Alpha\B;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Support\Str;
use BookStack\Book;
use Views;

class BookRepo extends EntityRepo
{
    protected $pageRepo;
    protected $chapterRepo;

    /**
     * BookRepo constructor.
     * @param PageRepo $pageRepo
     * @param ChapterRepo $chapterRepo
     */
    public function __construct(PageRepo $pageRepo, ChapterRepo $chapterRepo)
    {
        $this->pageRepo = $pageRepo;
        $this->chapterRepo = $chapterRepo;
        parent::__construct();
    }

    /**
     * Base query for getting books.
     * Takes into account any restrictions.
     * @return mixed
     */
    private function bookQuery()
    {
        return $this->permissionService->enforceBookRestrictions($this->book, 'view');
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
     * @throws NotFoundException
     */
    public function getBySlug($slug)
    {
        $book = $this->bookQuery()->where('slug', '=', $slug)->first();
        if ($book === null) throw new NotFoundException('Book not found');
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
     * @param array $input
     * @return Book
     */
    public function createFromInput($input)
    {
        $book = $this->book->newInstance($input);
        $book->slug = $this->findSuitableSlug($book->name);
        $book->created_by = auth()->user()->id;
        $book->updated_by = auth()->user()->id;
        $book->save();
        $this->permissionService->buildJointPermissionsForEntity($book);
        return $book;
    }

    /**
     * Update the given book from user input.
     * @param Book $book
     * @param $input
     * @return Book
     */
    public function updateFromInput(Book $book, $input)
    {
        $book->fill($input);
        $book->slug = $this->findSuitableSlug($book->name, $book->id);
        $book->updated_by = auth()->user()->id;
        $book->save();
        $this->permissionService->buildJointPermissionsForEntity($book);
        return $book;
    }

    /**
     * Destroy the given book.
     * @param Book $book
     * @throws \Exception
     */
    public function destroy(Book $book)
    {
        foreach ($book->pages as $page) {
            $this->pageRepo->destroy($page);
        }
        foreach ($book->chapters as $chapter) {
            $this->chapterRepo->destroy($chapter);
        }
        $book->views()->delete();
        $book->permissions()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($book);
        $book->delete();
    }

    /**
     * Alias method to update the book jointPermissions in the PermissionService.
     * @param Book $book
     */
    public function updateBookPermissions(Book $book)
    {
        $this->permissionService->buildJointPermissionsForEntity($book);
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
     * @param bool $filterDrafts
     * @return mixed
     */
    public function getChildren(Book $book, $filterDrafts = false)
    {
        $pageQuery = $book->pages()->where('chapter_id', '=', 0);
        $pageQuery = $this->permissionService->enforcePageRestrictions($pageQuery, 'view');

        if ($filterDrafts) {
            $pageQuery = $pageQuery->where('draft', '=', false);
        }

        $pages = $pageQuery->get();

        $chapterQuery = $book->chapters()->with(['pages' => function($query) use ($filterDrafts) {
            $this->permissionService->enforcePageRestrictions($query, 'view');
            if ($filterDrafts) $query->where('draft', '=', false);
        }]);
        $chapterQuery = $this->permissionService->enforceChapterRestrictions($chapterQuery, 'view');
        $chapters = $chapterQuery->get();
        $children = $pages->merge($chapters);
        $bookSlug = $book->slug;

        $children->each(function ($child) use ($bookSlug) {
            $child->setAttribute('bookSlug', $bookSlug);
            if ($child->isA('chapter')) {
                $child->pages->each(function ($page) use ($bookSlug) {
                    $page->setAttribute('bookSlug', $bookSlug);
                });
                $child->pages = $child->pages->sortBy(function($child, $key) {
                    $score = $child->priority;
                    if ($child->draft) $score -= 100;
                    return $score;
                });
            }
        });

        // Sort items with drafts first then by priority.
        return $children->sortBy(function($child, $key) {
            $score = $child->priority;
            if ($child->isA('page') && $child->draft) $score -= 100;
            return $score;
        });
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
        $terms = $this->prepareSearchTerms($term);
        $bookQuery = $this->permissionService->enforceBookRestrictions($this->book->fullTextSearchQuery(['name', 'description'], $terms));
        $bookQuery = $this->addAdvancedSearchQueries($bookQuery, $term);
        $books = $bookQuery->paginate($count)->appends($paginationAppends);
        $words = join('|', explode(' ', preg_quote(trim($term), '/')));
        foreach ($books as $book) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $book->getExcerpt(100));
            $book->searchSnippet = $result;
        }
        return $books;
    }

}