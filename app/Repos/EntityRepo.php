<?php namespace BookStack\Repos;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\Page;
use BookStack\Services\PermissionService;
use BookStack\User;
use Illuminate\Support\Facades\Log;

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
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * Acceptable operators to be used in a query
     * @var array
     */
    protected $queryOperators = ['<=', '>=', '=', '<', '>', 'like', '!='];

    /**
     * EntityService constructor.
     */
    public function __construct()
    {
        $this->book = app(Book::class);
        $this->chapter = app(Chapter::class);
        $this->page = app(Page::class);
        $this->permissionService = app(PermissionService::class);
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
        $query = $this->permissionService->enforceBookRestrictions($this->book)
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
        return $this->permissionService->enforceBookRestrictions($this->book)
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
        $query = $this->permissionService->enforcePageRestrictions($this->page)
            ->orderBy('created_at', 'desc')->where('draft', '=', false);
        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }
        return $query->with('book')->skip($page * $count)->take($count)->get();
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
        $query = $this->permissionService->enforceChapterRestrictions($this->chapter)
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
        return $this->permissionService->enforcePageRestrictions($this->page)
            ->where('draft', '=', false)
            ->orderBy('updated_at', 'desc')->with('book')->skip($page * $count)->take($count)->get();
    }

    /**
     * Get draft pages owned by the current user.
     * @param int $count
     * @param int $page
     */
    public function getUserDraftPages($count = 20, $page = 0)
    {
        $user = auth()->user();
        return $this->page->where('draft', '=', true)
            ->where('created_by', '=', $user->id)
            ->orderBy('updated_at', 'desc')
            ->skip($count * $page)->take($count)->get();
    }

    /**
     * Updates entity restrictions from a request
     * @param $request
     * @param Entity $entity
     */
    public function updateEntityPermissionsFromRequest($request, Entity $entity)
    {
        $entity->restricted = $request->has('restricted') && $request->get('restricted') === 'true';
        $entity->permissions()->delete();
        if ($request->has('restrictions')) {
            foreach ($request->get('restrictions') as $roleId => $restrictions) {
                foreach ($restrictions as $action => $value) {
                    $entity->permissions()->create([
                        'role_id' => $roleId,
                        'action'  => strtolower($action)
                    ]);
                }
            }
        }
        $entity->save();
        $this->permissionService->buildJointPermissionsForEntity($entity);
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
        $termString = $this->cleanSearchTermString($termString);
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

    /**
     * Removes any special search notation that should not
     * be used in a full-text search.
     * @param $termString
     * @return mixed
     */
    protected function cleanSearchTermString($termString)
    {
        // Strip tag searches
        $termString = preg_replace('/\[.*?\]/', '', $termString);
        // Reduced multiple spacing into single spacing
        $termString = preg_replace("/\s{2,}/", " ", $termString);
        return $termString;
    }

    /**
     * Get the available query operators as a regex escaped list.
     * @return mixed
     */
    protected function getRegexEscapedOperators()
    {
        $escapedOperators = [];
        foreach ($this->queryOperators as $operator) {
            $escapedOperators[] = preg_quote($operator);
        }
        return join('|', $escapedOperators);
    }

    /**
     * Parses advanced search notations and adds them to the db query.
     * @param $query
     * @param $termString
     * @return mixed
     */
    protected function addAdvancedSearchQueries($query, $termString)
    {
        $escapedOperators = $this->getRegexEscapedOperators();
        // Look for tag searches
        preg_match_all("/\[(.*?)((${escapedOperators})(.*?))?\]/", $termString, $tags);
        if (count($tags[0]) > 0) {
            $this->applyTagSearches($query, $tags);
        }

        return $query;
    }

    /**
     * Apply extracted tag search terms onto a entity query.
     * @param $query
     * @param $tags
     * @return mixed
     */
    protected function applyTagSearches($query, $tags) {
        $query->where(function($query) use ($tags) {
            foreach ($tags[1] as $index => $tagName) {
                $query->whereHas('tags', function($query) use ($tags, $index, $tagName) {
                    $tagOperator = $tags[3][$index];
                    $tagValue = $tags[4][$index];
                    if (!empty($tagOperator) && !empty($tagValue) && in_array($tagOperator, $this->queryOperators)) {
                        if (is_numeric($tagValue) && $tagOperator !== 'like') {
                            // We have to do a raw sql query for this since otherwise PDO will quote the value and MySQL will
                            // search the value as a string which prevents being able to do number-based operations
                            // on the tag values. We ensure it has a numeric value and then cast it just to be sure.
                            $tagValue = (float) trim($query->getConnection()->getPdo()->quote($tagValue), "'");
                            $query->where('name', '=', $tagName)->whereRaw("value ${tagOperator} ${tagValue}");
                        } else {
                            $query->where('name', '=', $tagName)->where('value', $tagOperator, $tagValue);
                        }
                    } else {
                        $query->where('name', '=', $tagName);
                    }
                });
            }
        });
        return $query;
    }

}












