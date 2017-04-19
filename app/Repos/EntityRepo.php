<?php namespace BookStack\Repos;

use BookStack\Book;
use BookStack\Chapter;
use BookStack\Entity;
use BookStack\Exceptions\NotFoundException;
use BookStack\Page;
use BookStack\PageRevision;
use BookStack\Services\AttachmentService;
use BookStack\Services\PermissionService;
use BookStack\Services\ViewService;
use Carbon\Carbon;
use DOMDocument;
use DOMXPath;
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
     * @var PageRevision
     */
    protected $pageRevision;

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
     * @var TagRepo
     */
    protected $tagRepo;

    /**
     * Acceptable operators to be used in a query
     * @var array
     */
    protected $queryOperators = ['<=', '>=', '=', '<', '>', 'like', '!='];

    /**
     * EntityService constructor.
     * @param Book $book
     * @param Chapter $chapter
     * @param Page $page
     * @param PageRevision $pageRevision
     * @param ViewService $viewService
     * @param PermissionService $permissionService
     * @param TagRepo $tagRepo
     */
    public function __construct(
        Book $book, Chapter $chapter, Page $page, PageRevision $pageRevision,
        ViewService $viewService, PermissionService $permissionService, TagRepo $tagRepo
    )
    {
        $this->book = $book;
        $this->chapter = $chapter;
        $this->page = $page;
        $this->pageRevision = $pageRevision;
        $this->entities = [
            'page' => $this->page,
            'chapter' => $this->chapter,
            'book' => $this->book
        ];
        $this->viewService = $viewService;
        $this->permissionService = $permissionService;
        $this->tagRepo = $tagRepo;
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
        return $this->entityQuery($type, $allowDrafts)->find($id);
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
     * Search through page revisions and retrieve the last page in the
     * current book that has a slug equal to the one given.
     * @param string $pageSlug
     * @param string $bookSlug
     * @return null|Page
     */
    public function getPageByOldSlug($pageSlug, $bookSlug)
    {
        $revision = $this->pageRevision->where('slug', '=', $pageSlug)
            ->whereHas('page', function ($query) {
                $this->permissionService->enforceEntityRestrictions('page', $query);
            })
            ->where('type', '=', 'version')
            ->where('book_slug', '=', $bookSlug)
            ->orderBy('created_at', 'desc')
            ->with('page')->first();
        return $revision !== null ? $revision->page : null;
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
     * Get the latest pages added to the system with pagination.
     * @param string $type
     * @param int $count
     * @return mixed
     */
    public function getRecentlyCreatedPaginated($type, $count = 20)
    {
        return $this->entityQuery($type)->orderBy('created_at', 'desc')->paginate($count);
    }

    /**
     * Get the latest pages added to the system with pagination.
     * @param string $type
     * @param int $count
     * @return mixed
     */
    public function getRecentlyUpdatedPaginated($type, $count = 20)
    {
        return $this->entityQuery($type)->orderBy('updated_at', 'desc')->paginate($count);
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

    /**
     * Get all child objects of a book.
     * Returns a sorted collection of Pages and Chapters.
     * Loads the book slug onto child elements to prevent access database access for getting the slug.
     * @param Book $book
     * @param bool $filterDrafts
     * @param bool $renderPages
     * @return mixed
     */
    public function getBookChildren(Book $book, $filterDrafts = false, $renderPages = false)
    {
        $q = $this->permissionService->bookChildrenQuery($book->id, $filterDrafts, $renderPages)->get();
        $entities = [];
        $parents = [];
        $tree = [];

        foreach ($q as $index => $rawEntity) {
            if ($rawEntity->entity_type === 'BookStack\\Page') {
                $entities[$index] = $this->page->newFromBuilder($rawEntity);
                if ($renderPages) {
                    $entities[$index]->html = $rawEntity->description;
                    $entities[$index]->html = $this->renderPage($entities[$index]);
                };
            } else if ($rawEntity->entity_type === 'BookStack\\Chapter') {
                $entities[$index] = $this->chapter->newFromBuilder($rawEntity);
                $key = $entities[$index]->entity_type . ':' . $entities[$index]->id;
                $parents[$key] = $entities[$index];
                $parents[$key]->setAttribute('pages', collect());
            }
            if ($entities[$index]->chapter_id === 0 || $entities[$index]->chapter_id === '0') $tree[] = $entities[$index];
            $entities[$index]->book = $book;
        }

        foreach ($entities as $entity) {
            if ($entity->chapter_id === 0 || $entity->chapter_id === '0') continue;
            $parentKey = 'BookStack\\Chapter:' . $entity->chapter_id;
            $chapter = $parents[$parentKey];
            $chapter->pages->push($entity);
        }

        return collect($tree);
    }

    /**
     * Get the child items for a chapter sorted by priority but
     * with draft items floated to the top.
     * @param Chapter $chapter
     */
    public function getChapterChildren(Chapter $chapter)
    {
        return $this->permissionService->enforceEntityRestrictions('page', $chapter->pages())
            ->orderBy('draft', 'DESC')->orderBy('priority', 'ASC')->get();
    }

    /**
     * Search entities of a type via a given query.
     * @param string $type
     * @param string $term
     * @param array $whereTerms
     * @param int $count
     * @param array $paginationAppends
     * @return mixed
     */
    public function getBySearch($type, $term, $whereTerms = [], $count = 20, $paginationAppends = [])
    {
        $terms = $this->prepareSearchTerms($term);
        $q = $this->permissionService->enforceEntityRestrictions($type, $this->getEntity($type)->fullTextSearchQuery($terms, $whereTerms));
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
     * Get the next sequential priority for a new child element in the given book.
     * @param Book $book
     * @return int
     */
    public function getNewBookPriority(Book $book)
    {
        $lastElem = $this->getBookChildren($book)->pop();
        return $lastElem ? $lastElem->priority + 1 : 0;
    }

    /**
     * Get a new priority for a new page to be added to the given chapter.
     * @param Chapter $chapter
     * @return int
     */
    public function getNewChapterPriority(Chapter $chapter)
    {
        $lastPage = $chapter->pages('DESC')->first();
        return $lastPage !== null ? $lastPage->priority + 1 : 0;
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
     * Create a new entity from request input.
     * Used for books and chapters.
     * @param string $type
     * @param array $input
     * @param bool|Book $book
     * @return Entity
     */
    public function createFromInput($type, $input = [], $book = false)
    {
        $isChapter = strtolower($type) === 'chapter';
        $entity = $this->getEntity($type)->newInstance($input);
        $entity->slug = $this->findSuitableSlug($type, $entity->name, false, $isChapter ? $book->id : false);
        $entity->created_by = user()->id;
        $entity->updated_by = user()->id;
        $isChapter ? $book->chapters()->save($entity) : $entity->save();
        $this->permissionService->buildJointPermissionsForEntity($entity);
        return $entity;
    }

    /**
     * Update entity details from request input.
     * Use for books and chapters
     * @param string $type
     * @param Entity $entityModel
     * @param array $input
     * @return Entity
     */
    public function updateFromInput($type, Entity $entityModel, $input = [])
    {
        if ($entityModel->name !== $input['name']) {
            $entityModel->slug = $this->findSuitableSlug($type, $input['name'], $entityModel->id);
        }
        $entityModel->fill($input);
        $entityModel->updated_by = user()->id;
        $entityModel->save();
        $this->permissionService->buildJointPermissionsForEntity($entityModel);
        return $entityModel;
    }

    /**
     * Change the book that an entity belongs to.
     * @param string $type
     * @param integer $newBookId
     * @param Entity $entity
     * @param bool $rebuildPermissions
     * @return Entity
     */
    public function changeBook($type, $newBookId, Entity $entity, $rebuildPermissions = false)
    {
        $entity->book_id = $newBookId;
        // Update related activity
        foreach ($entity->activity as $activity) {
            $activity->book_id = $newBookId;
            $activity->save();
        }
        $entity->slug = $this->findSuitableSlug($type, $entity->name, $entity->id, $newBookId);
        $entity->save();

        // Update all child pages if a chapter
        if (strtolower($type) === 'chapter') {
            foreach ($entity->pages as $page) {
                $this->changeBook('page', $newBookId, $page, false);
            }
        }

        // Update permissions if applicable
        if ($rebuildPermissions) {
            $entity->load('book');
            $this->permissionService->buildJointPermissionsForEntity($entity->book);
        }

        return $entity;
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
        $name = $this->remove_accents($name);

        $slug = str_replace(' ', '-', strtolower($name));
        $slug = preg_replace('/[\+\/\\\?\@\}\{\.\,\=\[\]\#\&\!\*\'\;\:\$\%]/', '', $slug);
        if ($slug === "") $slug = substr(md5(rand(1, 500)), 0, 5);
        return $slug;
    }

    /**
     * Checks to see if a string is utf8 encoded.
     *
     * NOTE: This function checks for 5-Byte sequences, UTF8
     *       has Bytes Sequences with a maximum length of 4.
     *
     * @author bmorel at ssi dot fr (modified)
     *
     * @param string $str The string to be checked
     * @return bool True if $str fits a UTF-8 model, false otherwise.
     */
    protected function seems_utf8($str)
    {
        $length = strlen($str);
        for ($i = 0; $i < $length; $i++) {
            $c = ord($str[$i]);
            if ($c < 0x80)
                $n = 0; # 0bbbbbbb
            elseif (($c & 0xE0) == 0xC0)
                $n = 1; # 110bbbbb
            elseif (($c & 0xF0) == 0xE0)
                $n = 2; # 1110bbbb
            elseif (($c & 0xF8) == 0xF0)
                $n = 3; # 11110bbb
            elseif (($c & 0xFC) == 0xF8)
                $n = 4; # 111110bb
            elseif (($c & 0xFE) == 0xFC)
                $n = 5; # 1111110b
            else
                return false;
            # Does not match any model
            for ($j = 0; $j < $n; $j++) { # n bytes matching 10bbbbbb follow ?
                if ((++$i == $length) || ((ord($str[$i]) & 0xC0) != 0x80))
                    return false;
            }
        }
        return true;
    }

    /**
     * Converts all accent characters to ASCII characters.
     *
     * If there are no accent characters, then the string given is just returned.
     *
     * @param string $string Text that might have accent characters
     * @return string Filtered string with replaced "nice" characters.
     */
    protected function remove_accents($string)
    {
        if (!preg_match('/[\x80-\xff]/', $string))
            return $string;

        if (self::seems_utf8($string)) {
            $chars = array(
                // Decompositions for Latin-1 Supplement
                chr(195) . chr(128) => 'A', chr(195) . chr(129) => 'A',
                chr(195) . chr(130) => 'A', chr(195) . chr(131) => 'A',
                chr(195) . chr(132) => 'A', chr(195) . chr(133) => 'A',
                chr(195) . chr(135) => 'C', chr(195) . chr(136) => 'E',
                chr(195) . chr(137) => 'E', chr(195) . chr(138) => 'E',
                chr(195) . chr(139) => 'E', chr(195) . chr(140) => 'I',
                chr(195) . chr(141) => 'I', chr(195) . chr(142) => 'I',
                chr(195) . chr(143) => 'I', chr(195) . chr(145) => 'N',
                chr(195) . chr(146) => 'O', chr(195) . chr(147) => 'O',
                chr(195) . chr(148) => 'O', chr(195) . chr(149) => 'O',
                chr(195) . chr(150) => 'O', chr(195) . chr(153) => 'U',
                chr(195) . chr(154) => 'U', chr(195) . chr(155) => 'U',
                chr(195) . chr(156) => 'U', chr(195) . chr(157) => 'Y',
                chr(195) . chr(159) => 's', chr(195) . chr(160) => 'a',
                chr(195) . chr(161) => 'a', chr(195) . chr(162) => 'a',
                chr(195) . chr(163) => 'a', chr(195) . chr(164) => 'a',
                chr(195) . chr(165) => 'a', chr(195) . chr(167) => 'c',
                chr(195) . chr(168) => 'e', chr(195) . chr(169) => 'e',
                chr(195) . chr(170) => 'e', chr(195) . chr(171) => 'e',
                chr(195) . chr(172) => 'i', chr(195) . chr(173) => 'i',
                chr(195) . chr(174) => 'i', chr(195) . chr(175) => 'i',
                chr(195) . chr(177) => 'n', chr(195) . chr(178) => 'o',
                chr(195) . chr(179) => 'o', chr(195) . chr(180) => 'o',
                chr(195) . chr(181) => 'o', chr(195) . chr(182) => 'o',
                chr(195) . chr(182) => 'o', chr(195) . chr(185) => 'u',
                chr(195) . chr(186) => 'u', chr(195) . chr(187) => 'u',
                chr(195) . chr(188) => 'u', chr(195) . chr(189) => 'y',
                chr(195) . chr(191) => 'y',
                // Decompositions for Latin Extended-A
                chr(196) . chr(128) => 'A', chr(196) . chr(129) => 'a',
                chr(196) . chr(130) => 'A', chr(196) . chr(131) => 'a',
                chr(196) . chr(132) => 'A', chr(196) . chr(133) => 'a',
                chr(196) . chr(134) => 'C', chr(196) . chr(135) => 'c',
                chr(196) . chr(136) => 'C', chr(196) . chr(137) => 'c',
                chr(196) . chr(138) => 'C', chr(196) . chr(139) => 'c',
                chr(196) . chr(140) => 'C', chr(196) . chr(141) => 'c',
                chr(196) . chr(142) => 'D', chr(196) . chr(143) => 'd',
                chr(196) . chr(144) => 'D', chr(196) . chr(145) => 'd',
                chr(196) . chr(146) => 'E', chr(196) . chr(147) => 'e',
                chr(196) . chr(148) => 'E', chr(196) . chr(149) => 'e',
                chr(196) . chr(150) => 'E', chr(196) . chr(151) => 'e',
                chr(196) . chr(152) => 'E', chr(196) . chr(153) => 'e',
                chr(196) . chr(154) => 'E', chr(196) . chr(155) => 'e',
                chr(196) . chr(156) => 'G', chr(196) . chr(157) => 'g',
                chr(196) . chr(158) => 'G', chr(196) . chr(159) => 'g',
                chr(196) . chr(160) => 'G', chr(196) . chr(161) => 'g',
                chr(196) . chr(162) => 'G', chr(196) . chr(163) => 'g',
                chr(196) . chr(164) => 'H', chr(196) . chr(165) => 'h',
                chr(196) . chr(166) => 'H', chr(196) . chr(167) => 'h',
                chr(196) . chr(168) => 'I', chr(196) . chr(169) => 'i',
                chr(196) . chr(170) => 'I', chr(196) . chr(171) => 'i',
                chr(196) . chr(172) => 'I', chr(196) . chr(173) => 'i',
                chr(196) . chr(174) => 'I', chr(196) . chr(175) => 'i',
                chr(196) . chr(176) => 'I', chr(196) . chr(177) => 'i',
                chr(196) . chr(178) => 'IJ', chr(196) . chr(179) => 'ij',
                chr(196) . chr(180) => 'J', chr(196) . chr(181) => 'j',
                chr(196) . chr(182) => 'K', chr(196) . chr(183) => 'k',
                chr(196) . chr(184) => 'k', chr(196) . chr(185) => 'L',
                chr(196) . chr(186) => 'l', chr(196) . chr(187) => 'L',
                chr(196) . chr(188) => 'l', chr(196) . chr(189) => 'L',
                chr(196) . chr(190) => 'l', chr(196) . chr(191) => 'L',
                chr(197) . chr(128) => 'l', chr(197) . chr(129) => 'L',
                chr(197) . chr(130) => 'l', chr(197) . chr(131) => 'N',
                chr(197) . chr(132) => 'n', chr(197) . chr(133) => 'N',
                chr(197) . chr(134) => 'n', chr(197) . chr(135) => 'N',
                chr(197) . chr(136) => 'n', chr(197) . chr(137) => 'N',
                chr(197) . chr(138) => 'n', chr(197) . chr(139) => 'N',
                chr(197) . chr(140) => 'O', chr(197) . chr(141) => 'o',
                chr(197) . chr(142) => 'O', chr(197) . chr(143) => 'o',
                chr(197) . chr(144) => 'O', chr(197) . chr(145) => 'o',
                chr(197) . chr(146) => 'OE', chr(197) . chr(147) => 'oe',
                chr(197) . chr(148) => 'R', chr(197) . chr(149) => 'r',
                chr(197) . chr(150) => 'R', chr(197) . chr(151) => 'r',
                chr(197) . chr(152) => 'R', chr(197) . chr(153) => 'r',
                chr(197) . chr(154) => 'S', chr(197) . chr(155) => 's',
                chr(197) . chr(156) => 'S', chr(197) . chr(157) => 's',
                chr(197) . chr(158) => 'S', chr(197) . chr(159) => 's',
                chr(197) . chr(160) => 'S', chr(197) . chr(161) => 's',
                chr(197) . chr(162) => 'T', chr(197) . chr(163) => 't',
                chr(197) . chr(164) => 'T', chr(197) . chr(165) => 't',
                chr(197) . chr(166) => 'T', chr(197) . chr(167) => 't',
                chr(197) . chr(168) => 'U', chr(197) . chr(169) => 'u',
                chr(197) . chr(170) => 'U', chr(197) . chr(171) => 'u',
                chr(197) . chr(172) => 'U', chr(197) . chr(173) => 'u',
                chr(197) . chr(174) => 'U', chr(197) . chr(175) => 'u',
                chr(197) . chr(176) => 'U', chr(197) . chr(177) => 'u',
                chr(197) . chr(178) => 'U', chr(197) . chr(179) => 'u',
                chr(197) . chr(180) => 'W', chr(197) . chr(181) => 'w',
                chr(197) . chr(182) => 'Y', chr(197) . chr(183) => 'y',
                chr(197) . chr(184) => 'Y', chr(197) . chr(185) => 'Z',
                chr(197) . chr(186) => 'z', chr(197) . chr(187) => 'Z',
                chr(197) . chr(188) => 'z', chr(197) . chr(189) => 'Z',
                chr(197) . chr(190) => 'z', chr(197) . chr(191) => 's',
                // Euro Sign
                chr(226) . chr(130) . chr(172) => 'E',
                // GBP (Pound) Sign
                chr(194) . chr(163) => '');

            $string = strtr($string, $chars);
        } else {
            // Assume ISO-8859-1 if not UTF-8
            $chars['in'] = chr(128) . chr(131) . chr(138) . chr(142) . chr(154) . chr(158)
                . chr(159) . chr(162) . chr(165) . chr(181) . chr(192) . chr(193) . chr(194)
                . chr(195) . chr(196) . chr(197) . chr(199) . chr(200) . chr(201) . chr(202)
                . chr(203) . chr(204) . chr(205) . chr(206) . chr(207) . chr(209) . chr(210)
                . chr(211) . chr(212) . chr(213) . chr(214) . chr(216) . chr(217) . chr(218)
                . chr(219) . chr(220) . chr(221) . chr(224) . chr(225) . chr(226) . chr(227)
                . chr(228) . chr(229) . chr(231) . chr(232) . chr(233) . chr(234) . chr(235)
                . chr(236) . chr(237) . chr(238) . chr(239) . chr(241) . chr(242) . chr(243)
                . chr(244) . chr(245) . chr(246) . chr(248) . chr(249) . chr(250) . chr(251)
                . chr(252) . chr(253) . chr(255);

            $chars['out'] = "EfSZszYcYuAAAAAACEEEEIIIINOOOOOOUUUUYaaaaaaceeeeiiiinoooooouuuuyy";

            $string = strtr($string, $chars['in'], $chars['out']);
            $double_chars['in'] = array(chr(140), chr(156), chr(198), chr(208), chr(222), chr(223), chr(230), chr(240), chr(254));
            $double_chars['out'] = array('OE', 'oe', 'AE', 'DH', 'TH', 'ss', 'ae', 'dh', 'th');
            $string = str_replace($double_chars['in'], $double_chars['out'], $string);
        }

        return $string;
    }

    /**
     * Encode the Unicode values to be used in the URI.
     *
     * @param string $utf8_string
     * @param int $length Max length of the string
     * @return string String with Unicode encoded for URI.
     */
    protected function utf8_uri_encode($utf8_string, $length = 0)
    {
        $unicode = '';
        $values = array();
        $num_octets = 1;
        $unicode_length = 0;

        $string_length = strlen($utf8_string);
        for ($i = 0; $i < $string_length; $i++) {

            $value = ord($utf8_string[$i]);

            if ($value < 128) {
                if ($length && ($unicode_length >= $length))
                    break;
                $unicode .= chr($value);
                $unicode_length++;
            } else {
                if (count($values) == 0)
                    $num_octets = ($value < 224) ? 2 : 3;

                $values[] = $value;

                if ($length && ($unicode_length + ($num_octets * 3)) > $length)
                    break;
                if (count($values) == $num_octets) {
                    if ($num_octets == 3) {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                        $unicode_length += 9;
                    } else {
                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                        $unicode_length += 6;
                    }

                    $values = array();
                    $num_octets = 1;
                }
            }
        }

        return $unicode;
    }

    /**
     * Publish a draft page to make it a normal page.
     * Sets the slug and updates the content.
     * @param Page $draftPage
     * @param array $input
     * @return Page
     */
    public function publishPageDraft(Page $draftPage, array $input)
    {
        $draftPage->fill($input);

        // Save page tags if present
        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($draftPage, $input['tags']);
        }

        $draftPage->slug = $this->findSuitableSlug('page', $draftPage->name, false, $draftPage->book->id);
        $draftPage->html = $this->formatHtml($input['html']);
        $draftPage->text = strip_tags($draftPage->html);
        $draftPage->draft = false;

        $draftPage->save();
        $this->savePageRevision($draftPage, trans('entities.pages_initial_revision'));

        return $draftPage;
    }

    /**
     * Saves a page revision into the system.
     * @param Page $page
     * @param null|string $summary
     * @return PageRevision
     */
    public function savePageRevision(Page $page, $summary = null)
    {
        $revision = $this->pageRevision->newInstance($page->toArray());
        if (setting('app-editor') !== 'markdown') $revision->markdown = '';
        $revision->page_id = $page->id;
        $revision->slug = $page->slug;
        $revision->book_slug = $page->book->slug;
        $revision->created_by = user()->id;
        $revision->created_at = $page->updated_at;
        $revision->type = 'version';
        $revision->summary = $summary;
        $revision->save();

        // Clear old revisions
        if ($this->pageRevision->where('page_id', '=', $page->id)->count() > 50) {
            $this->pageRevision->where('page_id', '=', $page->id)
                ->orderBy('created_at', 'desc')->skip(50)->take(5)->delete();
        }

        return $revision;
    }

    /**
     * Formats a page's html to be tagged correctly
     * within the system.
     * @param string $htmlText
     * @return string
     */
    protected function formatHtml($htmlText)
    {
        if ($htmlText == '') return $htmlText;
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($htmlText, 'HTML-ENTITIES', 'UTF-8'));

        $container = $doc->documentElement;
        $body = $container->childNodes->item(0);
        $childNodes = $body->childNodes;

        // Ensure no duplicate ids are used
        $idArray = [];

        foreach ($childNodes as $index => $childNode) {
            /** @var \DOMElement $childNode */
            if (get_class($childNode) !== 'DOMElement') continue;

            // Overwrite id if not a BookStack custom id
            if ($childNode->hasAttribute('id')) {
                $id = $childNode->getAttribute('id');
                if (strpos($id, 'bkmrk') === 0 && array_search($id, $idArray) === false) {
                    $idArray[] = $id;
                    continue;
                };
            }

            // Create an unique id for the element
            // Uses the content as a basis to ensure output is the same every time
            // the same content is passed through.
            $contentId = 'bkmrk-' . substr(strtolower(preg_replace('/\s+/', '-', trim($childNode->nodeValue))), 0, 20);
            $newId = urlencode($contentId);
            $loopIndex = 0;
            while (in_array($newId, $idArray)) {
                $newId = urlencode($contentId . '-' . $loopIndex);
                $loopIndex++;
            }

            $childNode->setAttribute('id', $newId);
            $idArray[] = $newId;
        }

        // Generate inner html as a string
        $html = '';
        foreach ($childNodes as $childNode) {
            $html .= $doc->saveHTML($childNode);
        }

        return $html;
    }


    /**
     * Render the page for viewing, Parsing and performing features such as page transclusion.
     * @param Page $page
     * @return mixed|string
     */
    public function renderPage(Page $page)
    {
        $content = $page->html;
        $matches = [];
        preg_match_all("/{{@\s?([0-9].*?)}}/", $content, $matches);
        if (count($matches[0]) === 0) return $content;

        foreach ($matches[1] as $index => $includeId) {
            $splitInclude = explode('#', $includeId, 2);
            $pageId = intval($splitInclude[0]);
            if (is_nan($pageId)) continue;

            $page = $this->getById('page', $pageId);
            if ($page === null) {
                $content = str_replace($matches[0][$index], '', $content);
                continue;
            }

            if (count($splitInclude) === 1) {
                $content = str_replace($matches[0][$index], $page->html, $content);
                continue;
            }

            $doc = new DOMDocument();
            $doc->loadHTML(mb_convert_encoding('<body>'.$page->html.'</body>', 'HTML-ENTITIES', 'UTF-8'));
            $matchingElem = $doc->getElementById($splitInclude[1]);
            if ($matchingElem === null) {
                $content = str_replace($matches[0][$index], '', $content);
                continue;
            }
            $innerContent = '';
            foreach ($matchingElem->childNodes as $childNode) {
                $innerContent .= $doc->saveHTML($childNode);
            }
            $content = str_replace($matches[0][$index], trim($innerContent), $content);
        }

        return $content;
    }

    /**
     * Get a new draft page instance.
     * @param Book $book
     * @param Chapter|bool $chapter
     * @return Page
     */
    public function getDraftPage(Book $book, $chapter = false)
    {
        $page = $this->page->newInstance();
        $page->name = trans('entities.pages_initial_name');
        $page->created_by = user()->id;
        $page->updated_by = user()->id;
        $page->draft = true;

        if ($chapter) $page->chapter_id = $chapter->id;

        $book->pages()->save($page);
        $this->permissionService->buildJointPermissionsForEntity($page);
        return $page;
    }

    /**
     * Search for image usage within page content.
     * @param $imageString
     * @return mixed
     */
    public function searchForImage($imageString)
    {
        $pages = $this->entityQuery('page')->where('html', 'like', '%' . $imageString . '%')->get();
        foreach ($pages as $page) {
            $page->url = $page->getUrl();
            $page->html = '';
            $page->text = '';
        }
        return count($pages) > 0 ? $pages : false;
    }

    /**
     * Parse the headers on the page to get a navigation menu
     * @param String $pageContent
     * @return array
     */
    public function getPageNav($pageContent)
    {
        if ($pageContent == '') return [];
        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($pageContent, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);
        $headers = $xPath->query("//h1|//h2|//h3|//h4|//h5|//h6");

        if (is_null($headers)) return [];

        $tree = collect([]);
        foreach ($headers as $header) {
            $text = $header->nodeValue;
            $tree->push([
                'nodeName' => strtolower($header->nodeName),
                'level' => intval(str_replace('h', '', $header->nodeName)),
                'link' => '#' . $header->getAttribute('id'),
                'text' => strlen($text) > 30 ? substr($text, 0, 27) . '...' : $text
            ]);
        }

        // Normalise headers if only smaller headers have been used
        if (count($tree) > 0) {
            $minLevel = $tree->pluck('level')->min();
            $tree = $tree->map(function($header) use ($minLevel) {
                $header['level'] -= ($minLevel - 2);
                return $header;
            });
        }
        return $tree->toArray();
    }

    /**
     * Updates a page with any fillable data and saves it into the database.
     * @param Page $page
     * @param int $book_id
     * @param array $input
     * @return Page
     */
    public function updatePage(Page $page, $book_id, $input)
    {
        // Hold the old details to compare later
        $oldHtml = $page->html;
        $oldName = $page->name;

        // Prevent slug being updated if no name change
        if ($page->name !== $input['name']) {
            $page->slug = $this->findSuitableSlug('page', $input['name'], $page->id, $book_id);
        }

        // Save page tags if present
        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($page, $input['tags']);
        }

        // Update with new details
        $userId = user()->id;
        $page->fill($input);
        $page->html = $this->formatHtml($input['html']);
        $page->text = strip_tags($page->html);
        if (setting('app-editor') !== 'markdown') $page->markdown = '';
        $page->updated_by = $userId;
        $page->save();

        // Remove all update drafts for this user & page.
        $this->userUpdatePageDraftsQuery($page, $userId)->delete();

        // Save a revision after updating
        if ($oldHtml !== $input['html'] || $oldName !== $input['name'] || $input['summary'] !== null) {
            $this->savePageRevision($page, $input['summary']);
        }

        return $page;
    }

    /**
     * The base query for getting user update drafts.
     * @param Page $page
     * @param $userId
     * @return mixed
     */
    protected function userUpdatePageDraftsQuery(Page $page, $userId)
    {
        return $this->pageRevision->where('created_by', '=', $userId)
            ->where('type', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->orderBy('created_at', 'desc');
    }

    /**
     * Checks whether a user has a draft version of a particular page or not.
     * @param Page $page
     * @param $userId
     * @return bool
     */
    public function hasUserGotPageDraft(Page $page, $userId)
    {
        return $this->userUpdatePageDraftsQuery($page, $userId)->count() > 0;
    }

    /**
     * Get the latest updated draft revision for a particular page and user.
     * @param Page $page
     * @param $userId
     * @return mixed
     */
    public function getUserPageDraft(Page $page, $userId)
    {
        return $this->userUpdatePageDraftsQuery($page, $userId)->first();
    }

    /**
     * Get the notification message that informs the user that they are editing a draft page.
     * @param PageRevision $draft
     * @return string
     */
    public function getUserPageDraftMessage(PageRevision $draft)
    {
        $message = trans('entities.pages_editing_draft_notification', ['timeDiff' => $draft->updated_at->diffForHumans()]);
        if ($draft->page->updated_at->timestamp <= $draft->updated_at->timestamp) return $message;
        return $message . "\n" . trans('entities.pages_draft_edited_notification');
    }

    /**
     * Check if a page is being actively editing.
     * Checks for edits since last page updated.
     * Passing in a minuted range will check for edits
     * within the last x minutes.
     * @param Page $page
     * @param null $minRange
     * @return bool
     */
    public function isPageEditingActive(Page $page, $minRange = null)
    {
        $draftSearch = $this->activePageEditingQuery($page, $minRange);
        return $draftSearch->count() > 0;
    }

    /**
     * A query to check for active update drafts on a particular page.
     * @param Page $page
     * @param null $minRange
     * @return mixed
     */
    protected function activePageEditingQuery(Page $page, $minRange = null)
    {
        $query = $this->pageRevision->where('type', '=', 'update_draft')
            ->where('page_id', '=', $page->id)
            ->where('updated_at', '>', $page->updated_at)
            ->where('created_by', '!=', user()->id)
            ->with('createdBy');

        if ($minRange !== null) {
            $query = $query->where('updated_at', '>=', Carbon::now()->subMinutes($minRange));
        }

        return $query;
    }

    /**
     * Restores a revision's content back into a page.
     * @param Page $page
     * @param Book $book
     * @param  int $revisionId
     * @return Page
     */
    public function restorePageRevision(Page $page, Book $book, $revisionId)
    {
        $this->savePageRevision($page);
        $revision = $page->revisions()->where('id', '=', $revisionId)->first();
        $page->fill($revision->toArray());
        $page->slug = $this->findSuitableSlug('page', $page->name, $page->id, $book->id);
        $page->text = strip_tags($page->html);
        $page->updated_by = user()->id;
        $page->save();
        return $page;
    }


    /**
     * Save a page update draft.
     * @param Page $page
     * @param array $data
     * @return PageRevision|Page
     */
    public function updatePageDraft(Page $page, $data = [])
    {
        // If the page itself is a draft simply update that
        if ($page->draft) {
            $page->fill($data);
            if (isset($data['html'])) {
                $page->text = strip_tags($data['html']);
            }
            $page->save();
            return $page;
        }

        // Otherwise save the data to a revision
        $userId = user()->id;
        $drafts = $this->userUpdatePageDraftsQuery($page, $userId)->get();

        if ($drafts->count() > 0) {
            $draft = $drafts->first();
        } else {
            $draft = $this->pageRevision->newInstance();
            $draft->page_id = $page->id;
            $draft->slug = $page->slug;
            $draft->book_slug = $page->book->slug;
            $draft->created_by = $userId;
            $draft->type = 'update_draft';
        }

        $draft->fill($data);
        if (setting('app-editor') !== 'markdown') $draft->markdown = '';

        $draft->save();
        return $draft;
    }

    /**
     * Get a notification message concerning the editing activity on a particular page.
     * @param Page $page
     * @param null $minRange
     * @return string
     */
    public function getPageEditingActiveMessage(Page $page, $minRange = null)
    {
        $pageDraftEdits = $this->activePageEditingQuery($page, $minRange)->get();

        $userMessage = $pageDraftEdits->count() > 1 ? trans('entities.pages_draft_edit_active.start_a', ['count' => $pageDraftEdits->count()]): trans('entities.pages_draft_edit_active.start_b', ['userName' => $pageDraftEdits->first()->createdBy->name]);
        $timeMessage = $minRange === null ? trans('entities.pages_draft_edit_active.time_a') : trans('entities.pages_draft_edit_active.time_b', ['minCount'=>$minRange]);
        return trans('entities.pages_draft_edit_active.message', ['start' => $userMessage, 'time' => $timeMessage]);
    }

    /**
     * Change the page's parent to the given entity.
     * @param Page $page
     * @param Entity $parent
     */
    public function changePageParent(Page $page, Entity $parent)
    {
        $book = $parent->isA('book') ? $parent : $parent->book;
        $page->chapter_id = $parent->isA('chapter') ? $parent->id : 0;
        $page->save();
        if ($page->book->id !== $book->id) {
            $page = $this->changeBook('page', $book->id, $page);
        }
        $page->load('book');
        $this->permissionService->buildJointPermissionsForEntity($book);
    }

    /**
     * Destroy the provided book and all its child entities.
     * @param Book $book
     */
    public function destroyBook(Book $book)
    {
        foreach ($book->pages as $page) {
            $this->destroyPage($page);
        }
        foreach ($book->chapters as $chapter) {
            $this->destroyChapter($chapter);
        }
        \Activity::removeEntity($book);
        $book->views()->delete();
        $book->permissions()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($book);
        $book->delete();
    }

    /**
     * Destroy a chapter and its relations.
     * @param Chapter $chapter
     */
    public function destroyChapter(Chapter $chapter)
    {
        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $page->chapter_id = 0;
                $page->save();
            }
        }
        \Activity::removeEntity($chapter);
        $chapter->views()->delete();
        $chapter->permissions()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($chapter);
        $chapter->delete();
    }

    /**
     * Destroy a given page along with its dependencies.
     * @param Page $page
     */
    public function destroyPage(Page $page)
    {
        \Activity::removeEntity($page);
        $page->views()->delete();
        $page->tags()->delete();
        $page->revisions()->delete();
        $page->permissions()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($page);

        // Delete Attached Files
        $attachmentService = app(AttachmentService::class);
        foreach ($page->attachments as $attachment) {
            $attachmentService->deleteFile($attachment);
        }

        $page->delete();
    }

}












