<?php namespace BookStack\Repos;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\Page;
use BookStack\Services\RestrictionService;

class EntityRepo
{

    /**
     * @var Book $book
     */
    public $book;

    /**
     * @var Chapter
     */
    public $chapter;

    /**
     * @var Page
     */
    public $page;

    /**
     * @var RestrictionService
     */
    protected $restrictionService;

    /**
     * EntityService constructor.
     */
    public function __construct()
    {
        $this->book = app(Book::class);
        $this->chapter = app(Chapter::class);
        $this->page = app(Page::class);
        $this->restrictionService = app(RestrictionService::class);
    }

    /**
     * Get the latest books added to the system.
     * @param int $count
     * @param int $page
     * @param bool $additionalQuery
     * @return
     */
    public function getRecentlyCreatedBooks($count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->restrictionService->enforceBookRestrictions($this->book)
            ->orderBy('created_at', 'desc');
        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }
        return $query->skip($page * $count)->take($count)->get();
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
            ->orderBy('updated_at', 'desc')->skip($page * $count)->take($count)->get();
    }

    /**
     * Get the latest pages added to the system.
     * @param int $count
     * @param int $page
     * @param bool $additionalQuery
     * @return
     */
    public function getRecentlyCreatedPages($count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->restrictionService->enforcePageRestrictions($this->page)
            ->orderBy('created_at', 'desc');
        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }
        return $query->skip($page * $count)->take($count)->get();
    }

    /**
     * Get the latest chapters added to the system.
     * @param int $count
     * @param int $page
     * @param bool $additionalQuery
     * @return
     */
    public function getRecentlyCreatedChapters($count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->restrictionService->enforceChapterRestrictions($this->chapter)
            ->orderBy('created_at', 'desc');
        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }
        return $query->skip($page * $count)->take($count)->get();
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
            ->orderBy('updated_at', 'desc')->skip($page * $count)->take($count)->get();
    }

    /**
     * Updates entity restrictions from a request
     * @param $request
     * @param Entity $entity
     */
    public function updateRestrictionsFromRequest($request, Entity $entity)
    {
        $entity->restricted = $request->has('restricted') && $request->get('restricted') === 'true';
        $entity->restrictions()->delete();
        if ($request->has('restrictions')) {
            foreach ($request->get('restrictions') as $roleId => $restrictions) {
                foreach ($restrictions as $action => $value) {
                    $entity->restrictions()->create([
                        'role_id' => $roleId,
                        'action'  => strtolower($action)
                    ]);
                }
            }
        }
        $entity->save();
    }

    /**
     * Prepare a string of search terms by turning
     * it into an array of terms.
     * Keeps quoted terms together.
     * @param $termString
     * @return array
     */
    protected function prepareSearchTerms($termString)
    {
        preg_match_all('/"(.*?)"/', $termString, $matches);
        if (count($matches[1]) > 0) {
            $terms = $matches[1];
            $termString = trim(preg_replace('/"(.*?)"/', '', $termString));
        } else {
            $terms = [];
        }
        if (!empty($termString)) $terms = array_merge($terms, explode(' ', $termString));
        return $terms;
    }


}