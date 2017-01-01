<?php namespace BookStack\Repos;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\Exceptions\NotFoundException;
use BookStack\Page;
use BookStack\Services\PermissionService;
use BookStack\Services\ViewService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

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
     * Base entity instances keyed by type
     * @var []Entity
     */
    protected $entities;

    /**
     * @var PermissionService
     */
    protected $permissionService;

    /**
     * @var ViewService
     */
    protected $viewService;

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
        // TODO - Redo this to come via injection
        $this->book = app(Book::class);
        $this->chapter = app(Chapter::class);
        $this->page = app(Page::class);
        $this->entities = [
            'page' => $this->page,
            'chapter' => $this->chapter,
            'book' => $this->book
        ];
        $this->viewService = app(ViewService::class);
        $this->permissionService = app(PermissionService::class);
    }

    /**
     * Get an entity instance via type.
     * @param $type
     * @return Entity
     */
    protected function getEntity($type)
    {
        return $this->entities[strtolower($type)];
    }

    /**
     * Base query for searching entities via permission system
     * @param string $type
     * @param bool $allowDrafts
     * @return \Illuminate\Database\Query\Builder
     */
    protected function entityQuery($type, $allowDrafts = false)
    {
        $q = $this->permissionService->enforceEntityRestrictions($type, $this->getEntity($type), 'view');
        if (strtolower($type) === 'page' && !$allowDrafts) {
            $q = $q->where('draft', '=', false);
        }
        return $q;
    }

    /**
     * Check if an entity with the given id exists.
     * @param $type
     * @param $id
     * @return bool
     */
    public function exists($type, $id)
    {
        return $this->entityQuery($type)->where('id', '=', $id)->exists();
    }

    /**
     * Get an entity by ID
     * @param string $type
     * @param integer $id
     * @param bool $allowDrafts
     * @return Entity
     */
    public function getById($type, $id, $allowDrafts = false)
    {
        return $this->entityQuery($type, $allowDrafts)->findOrFail($id);
    }

    /**
     * Get an entity by its url slug.
     * @param string $type
     * @param string $slug
     * @param string|bool $bookSlug
     * @return Entity
     * @throws NotFoundException
     */
    public function getBySlug($type, $slug, $bookSlug = false)
    {
        $q = $this->entityQuery($type)->where('slug', '=', $slug);
        if (strtolower($type) === 'chapter' || strtolower($type) === 'page') {
            $q = $q->where('book_id', '=', function($query) use ($bookSlug) {
                $query->select('id')
                    ->from($this->book->getTable())
                    ->where('slug', '=', $bookSlug)->limit(1);
            });
        }
        $entity = $q->first();
        if ($entity === null) throw new NotFoundException(trans('errors.' . strtolower($type) . '_not_found'));
        return $entity;
    }

    /**
     * Get all entities of a type limited by count unless count if false.
     * @param string $type
     * @param integer|bool $count
     * @return Collection
     */
    public function getAll($type, $count = 20)
    {
        $q = $this->entityQuery($type)->orderBy('name', 'asc');
        if ($count !== false) $q = $q->take($count);
        return $q->get();
    }

    /**
     * Get all entities in a paginated format
     * @param $type
     * @param int $count
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getAllPaginated($type, $count = 10)
    {
        return $this->entityQuery($type)->orderBy('name', 'asc')->paginate($count);
    }

    /**
     * Get the most recently created entities of the given type.
     * @param string $type
     * @param int $count
     * @param int $page
     * @param bool|callable $additionalQuery
     */
    public function getRecentlyCreated($type, $count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->permissionService->enforceEntityRestrictions($type, $this->getEntity($type))
            ->orderBy('created_at', 'desc');
        if (strtolower($type) === 'page') $query = $query->where('draft', '=', false);
        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }
        return $query->skip($page * $count)->take($count)->get();
    }

    /**
     * Get the most recently updated entities of the given type.
     * @param string $type
     * @param int $count
     * @param int $page
     * @param bool|callable $additionalQuery
     */
    public function getRecentlyUpdated($type, $count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->permissionService->enforceEntityRestrictions($type, $this->getEntity($type))
            ->orderBy('updated_at', 'desc');
        if (strtolower($type) === 'page') $query = $query->where('draft', '=', false);
        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }
        return $query->skip($page * $count)->take($count)->get();
    }

    /**
     * Get the most recently viewed entities.
     * @param string|bool $type
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getRecentlyViewed($type, $count = 10, $page = 0)
    {
        $filter = is_bool($type) ? false : $this->getEntity($type);
        return $this->viewService->getUserRecentlyViewed($count, $page, $filter);
    }

    /**
     * Get the most popular entities base on all views.
     * @param string|bool $type
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getPopular($type, $count = 10, $page = 0)
    {
        $filter = is_bool($type) ? false : $this->getEntity($type);
        return $this->viewService->getPopular($count, $page, $filter);
    }

    /**
     * Get draft pages owned by the current user.
     * @param int $count
     * @param int $page
     */
    public function getUserDraftPages($count = 20, $page = 0)
    {
        return $this->page->where('draft', '=', true)
            ->where('created_by', '=', user()->id)
            ->orderBy('updated_at', 'desc')
            ->skip($count * $page)->take($count)->get();
    }

    public function getBySearch($type, $term, $whereTerms = [], $count = 20, $paginationAppends = [])
    {
        $terms = $this->prepareSearchTerms($term);
        $q = $this->permissionService->enforceChapterRestrictions($this->getEntity($type)->fullTextSearchQuery($terms, $whereTerms));
        $q = $this->addAdvancedSearchQueries($q, $term);
        $entities = $q->paginate($count)->appends($paginationAppends);
        $words = join('|', explode(' ', preg_quote(trim($term), '/')));

        // Highlight page content
        if ($type === 'page') {
            //lookahead/behind assertions ensures cut between words
            $s = '\s\x00-/:-@\[-`{-~'; //character set for start/end of words

            foreach ($entities as $page) {
                preg_match_all('#(?<=[' . $s . ']).{1,30}((' . $words . ').{1,30})+(?=[' . $s . '])#uis', $page->text, $matches, PREG_SET_ORDER);
                //delimiter between occurrences
                $results = [];
                foreach ($matches as $line) {
                    $results[] = htmlspecialchars($line[0], 0, 'UTF-8');
                }
                $matchLimit = 6;
                if (count($results) > $matchLimit) $results = array_slice($results, 0, $matchLimit);
                $result = join('... ', $results);

                //highlight
                $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $result);
                if (strlen($result) < 5) $result = $page->getExcerpt(80);

                $page->searchSnippet = $result;
            }
            return $entities;
        }

        // Highlight chapter/book content
        foreach ($entities as $entity) {
            //highlight
            $result = preg_replace('#' . $words . '#iu', "<span class=\"highlight\">\$0</span>", $entity->getExcerpt(100));
            $entity->searchSnippet = $result;
        }
        return $entities;
    }

    /**
     * Find a suitable slug for an entity.
     * @param string $type
     * @param string $name
     * @param bool|integer $currentId
     * @param bool|integer $bookId Only pass if type is not a book
     * @return string
     */
    public function findSuitableSlug($type, $name, $currentId = false, $bookId = false)
    {
        $slug = $this->nameToSlug($name);
        while ($this->slugExists($type, $slug, $currentId, $bookId)) {
            $slug .= '-' . substr(md5(rand(1, 500)), 0, 3);
        }
        return $slug;
    }

    /**
     * Check if a slug already exists in the database.
     * @param string $type
     * @param string $slug
     * @param bool|integer $currentId
     * @param bool|integer $bookId
     * @return bool
     */
    protected function slugExists($type, $slug, $currentId = false, $bookId = false)
    {
        $query = $this->getEntity($type)->where('slug', '=', $slug);
        if (strtolower($type) === 'page' || strtolower($type) === 'chapter') {
            $query = $query->where('book_id', '=', $bookId);
        }
        if ($currentId) $query = $query->where('id', '!=', $currentId);
        return $query->count() > 0;
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
    public function prepareSearchTerms($termString)
    {
        $termString = $this->cleanSearchTermString($termString);
        preg_match_all('/(".*?")/', $termString, $matches);
        $terms = [];
        if (count($matches[1]) > 0) {
            foreach ($matches[1] as $match) {
                $terms[] = $match;
            }
            $termString = trim(preg_replace('/"(.*?)"/', '', $termString));
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

    /**
     * Alias method to update the book jointPermissions in the PermissionService.
     * @param Collection $collection collection on entities
     */
    public function buildJointPermissions(Collection $collection)
    {
        $this->permissionService->buildJointPermissionsForEntities($collection);
    }

    /**
     * Format a name as a url slug.
     * @param $name
     * @return string
     */
    protected function nameToSlug($name)
    {
        $slug = str_replace(' ', '-', strtolower($name));
        $slug = preg_replace('/[\+\/\\\?\@\}\{\.\,\=\[\]\#\&\!\*\'\;\:\$\%]/', '', $slug);
        if ($slug === "") $slug = substr(md5(rand(1, 500)), 0, 5);
        return $slug;
    }

}












