<?php namespace BookStack\Entities\Repos;

use Activity;
use BookStack\Actions\TagRepo;
use BookStack\Actions\ViewService;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\Book;
use BookStack\Entities\BookChild;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Entity;
use BookStack\Entities\EntityProvider;
use BookStack\Entities\Page;
use BookStack\Entities\SearchService;
use BookStack\Exceptions\NotFoundException;
use BookStack\Exceptions\NotifyException;
use BookStack\Uploads\AttachmentService;
use DOMDocument;
use DOMXPath;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Throwable;

class EntityRepo
{

    /**
     * @var EntityProvider
     */
    protected $entityProvider;

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
     * @var SearchService
     */
    protected $searchService;

    /**
     * EntityRepo constructor.
     * @param EntityProvider $entityProvider
     * @param ViewService $viewService
     * @param PermissionService $permissionService
     * @param TagRepo $tagRepo
     * @param SearchService $searchService
     */
    public function __construct(
        EntityProvider $entityProvider,
        ViewService $viewService,
        PermissionService $permissionService,
        TagRepo $tagRepo,
        SearchService $searchService
    ) {
        $this->entityProvider = $entityProvider;
        $this->viewService = $viewService;
        $this->permissionService = $permissionService;
        $this->tagRepo = $tagRepo;
        $this->searchService = $searchService;
    }

    /**
     * Base query for searching entities via permission system
     * @param string $type
     * @param bool $allowDrafts
     * @param string $permission
     * @return QueryBuilder
     */
    protected function entityQuery($type, $allowDrafts = false, $permission = 'view')
    {
        $q = $this->permissionService->enforceEntityRestrictions($type, $this->entityProvider->get($type), $permission);
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
     * @param bool $ignorePermissions
     * @return Entity
     */
    public function getById($type, $id, $allowDrafts = false, $ignorePermissions = false)
    {
        $query = $this->entityQuery($type, $allowDrafts);

        if ($ignorePermissions) {
            $query = $this->entityProvider->get($type)->newQuery();
        }

        return $query->find($id);
    }

    /**
     * @param string $type
     * @param []int $ids
     * @param bool $allowDrafts
     * @param bool $ignorePermissions
     * @return Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
     */
    public function getManyById($type, $ids, $allowDrafts = false, $ignorePermissions = false)
    {
        $query = $this->entityQuery($type, $allowDrafts);

        if ($ignorePermissions) {
            $query = $this->entityProvider->get($type)->newQuery();
        }

        return $query->whereIn('id', $ids)->get();
    }

    /**
     * Get an entity by its url slug.
     * @param string $type
     * @param string $slug
     * @param string|null $bookSlug
     * @return Entity
     * @throws NotFoundException
     */
    public function getEntityBySlug(string $type, string $slug, string $bookSlug = null): Entity
    {
        $type = strtolower($type);
        $query = $this->entityQuery($type)->where('slug', '=', $slug);

        if ($type === 'chapter' || $type === 'page') {
            $query = $query->where('book_id', '=', function (QueryBuilder $query) use ($bookSlug) {
                $query->select('id')
                    ->from($this->entityProvider->book->getTable())
                    ->where('slug', '=', $bookSlug)->limit(1);
            });
        }

        $entity = $query->first();

        if ($entity === null) {
            throw new NotFoundException(trans('errors.' . $type . '_not_found'));
        }

        return $entity;
    }


    /**
     * Get all entities of a type with the given permission, limited by count unless count is false.
     * @param string $type
     * @param integer|bool $count
     * @param string $permission
     * @return Collection
     */
    public function getAll($type, $count = 20, $permission = 'view')
    {
        $q = $this->entityQuery($type, false, $permission)->orderBy('name', 'asc');
        if ($count !== false) {
            $q = $q->take($count);
        }
        return $q->get();
    }

    /**
     * Get all entities in a paginated format
     * @param $type
     * @param int $count
     * @param string $sort
     * @param string $order
     * @param null|callable $queryAddition
     * @return LengthAwarePaginator
     */
    public function getAllPaginated($type, int $count = 10, string $sort = 'name', string $order = 'asc', $queryAddition = null)
    {
        $query = $this->entityQuery($type);
        $query = $this->addSortToQuery($query, $sort, $order);
        if ($queryAddition) {
            $queryAddition($query);
        }
        return $query->paginate($count);
    }

    /**
     * Add sorting operations to an entity query.
     * @param Builder $query
     * @param string $sort
     * @param string $order
     * @return Builder
     */
    protected function addSortToQuery(Builder $query, string $sort = 'name', string $order = 'asc')
    {
        $order = ($order === 'asc') ? 'asc' : 'desc';
        $propertySorts = ['name', 'created_at', 'updated_at'];

        if (in_array($sort, $propertySorts)) {
            return $query->orderBy($sort, $order);
        }

        return $query;
    }

    /**
     * Get the most recently created entities of the given type.
     * @param string $type
     * @param int $count
     * @param int $page
     * @param bool|callable $additionalQuery
     * @return Collection
     */
    public function getRecentlyCreated($type, $count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->permissionService->enforceEntityRestrictions($type, $this->entityProvider->get($type))
            ->orderBy('created_at', 'desc');
        if (strtolower($type) === 'page') {
            $query = $query->where('draft', '=', false);
        }
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
     * @return Collection
     */
    public function getRecentlyUpdated($type, $count = 20, $page = 0, $additionalQuery = false)
    {
        $query = $this->permissionService->enforceEntityRestrictions($type, $this->entityProvider->get($type))
            ->orderBy('updated_at', 'desc');
        if (strtolower($type) === 'page') {
            $query = $query->where('draft', '=', false);
        }
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
        $filter = is_bool($type) ? false : $this->entityProvider->get($type);
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
     * @param string $type
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getPopular(string $type, int $count = 10, int $page = 0)
    {
        return $this->viewService->getPopular($count, $page, $type);
    }

    /**
     * Get draft pages owned by the current user.
     * @param int $count
     * @param int $page
     * @return Collection
     */
    public function getUserDraftPages($count = 20, $page = 0)
    {
        return $this->entityProvider->page->where('draft', '=', true)
            ->where('created_by', '=', user()->id)
            ->orderBy('updated_at', 'desc')
            ->skip($count * $page)->take($count)->get();
    }

    /**
     * Get the number of entities the given user has created.
     * @param string $type
     * @param User $user
     * @return int
     */
    public function getUserTotalCreated(string $type, User $user)
    {
        return $this->entityProvider->get($type)
            ->where('created_by', '=', $user->id)->count();
    }

    /**
     * Get the child items for a chapter sorted by priority but
     * with draft items floated to the top.
     * @param Bookshelf $bookshelf
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getBookshelfChildren(Bookshelf $bookshelf)
    {
        return $this->permissionService->enforceEntityRestrictions('book', $bookshelf->books())->get();
    }

    /**
     * Get the direct children of a book.
     * @param Book $book
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBookDirectChildren(Book $book)
    {
        $pages = $this->permissionService->enforceEntityRestrictions('page', $book->directPages())->get();
        $chapters = $this->permissionService->enforceEntityRestrictions('chapters', $book->chapters())->get();
        return collect()->concat($pages)->concat($chapters)->sortBy('priority')->sortByDesc('draft');
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
            if ($rawEntity->entity_type ===  $this->entityProvider->page->getMorphClass()) {
                $entities[$index] = $this->entityProvider->page->newFromBuilder($rawEntity);
                if ($renderPages) {
                    $entities[$index]->html = $rawEntity->html;
                    $entities[$index]->html = $this->renderPage($entities[$index]);
                };
            } else if ($rawEntity->entity_type === $this->entityProvider->chapter->getMorphClass()) {
                $entities[$index] = $this->entityProvider->chapter->newFromBuilder($rawEntity);
                $key = $entities[$index]->entity_type . ':' . $entities[$index]->id;
                $parents[$key] = $entities[$index];
                $parents[$key]->setAttribute('pages', collect());
            }
            if ($entities[$index]->chapter_id === 0 || $entities[$index]->chapter_id === '0') {
                $tree[] = $entities[$index];
            }
            $entities[$index]->book = $book;
        }

        foreach ($entities as $entity) {
            if ($entity->chapter_id === 0 || $entity->chapter_id === '0') {
                continue;
            }
            $parentKey = $this->entityProvider->chapter->getMorphClass() . ':' . $entity->chapter_id;
            if (!isset($parents[$parentKey])) {
                $tree[] = $entity;
                continue;
            }
            $chapter = $parents[$parentKey];
            $chapter->pages->push($entity);
        }

        return collect($tree);
    }


    /**
     * Get the bookshelves that a book is contained in.
     * @param Book $book
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getBookParentShelves(Book $book)
    {
        return $this->permissionService->enforceEntityRestrictions('shelf', $book->shelves())->get();
    }

    /**
     * Get the child items for a chapter sorted by priority but
     * with draft items floated to the top.
     * @param Chapter $chapter
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getChapterChildren(Chapter $chapter)
    {
        return $this->permissionService->enforceEntityRestrictions('page', $chapter->pages())
            ->orderBy('draft', 'DESC')->orderBy('priority', 'ASC')->get();
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
     * Updates entity restrictions from a request
     * @param Request $request
     * @param Entity $entity
     * @throws Throwable
     */
    public function updateEntityPermissionsFromRequest(Request $request, Entity $entity)
    {
        $entity->restricted = $request->get('restricted', '') === 'true';
        $entity->permissions()->delete();

        if ($request->filled('restrictions')) {
            $entityPermissionData = collect($request->get('restrictions'))->flatMap(function($restrictions, $roleId) {
                return collect($restrictions)->keys()->map(function($action) use ($roleId) {
                    return [
                        'role_id' => $roleId,
                        'action' => strtolower($action),
                    ] ;
                });
            });

            $entity->permissions()->createMany($entityPermissionData);
        }

        $entity->save();
        $entity->rebuildPermissions();
    }


    /**
     * Create a new entity from request input.
     * Used for books and chapters.
     * @param string $type
     * @param array $input
     * @param Book|null $book
     * @return Entity
     */
    public function createFromInput(string $type, array $input = [], Book $book = null)
    {
        $entityModel = $this->entityProvider->get($type)->newInstance($input);
        $entityModel->created_by = user()->id;
        $entityModel->updated_by = user()->id;

        if ($book) {
            $entityModel->book_id = $book->id;
        }

        $entityModel->refreshSlug();
        $entityModel->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entityModel, $input['tags']);
        }

        $entityModel->rebuildPermissions();
        $this->searchService->indexEntity($entityModel);
        return $entityModel;
    }

    /**
     * Update entity details from request input.
     * Used for shelves, books and chapters.
     */
    public function updateFromInput(Entity $entityModel, array $input): Entity
    {
        $entityModel->fill($input);
        $entityModel->updated_by = user()->id;

        if ($entityModel->isDirty('name')) {
            $entityModel->refreshSlug();
        }

        $entityModel->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entityModel, $input['tags']);
        }

        $entityModel->rebuildPermissions();
        $this->searchService->indexEntity($entityModel);
        return $entityModel;
    }

    /**
     * Sync the books assigned to a shelf from a comma-separated list
     * of book IDs.
     * @param Bookshelf $shelf
     * @param string $books
     */
    public function updateShelfBooks(Bookshelf $shelf, string $books)
    {
        $ids = explode(',', $books);

        // Check books exist and match ordering
        $bookIds = $this->entityQuery('book')->whereIn('id', $ids)->get(['id'])->pluck('id');
        $syncData = [];
        foreach ($ids as $index => $id) {
            if ($bookIds->contains($id)) {
                $syncData[$id] = ['order' => $index];
            }
        }

        $shelf->books()->sync($syncData);
    }

    /**
     * Change the book that an entity belongs to.
     */
    public function changeBook(BookChild $bookChild, int $newBookId): Entity
    {
        $bookChild->book_id = $newBookId;
        $bookChild->refreshSlug();
        $bookChild->save();

        // Update related activity
        $bookChild->activity()->update(['book_id' => $newBookId]);

        // Update all child pages if a chapter
        if ($bookChild->isA('chapter')) {
            foreach ($bookChild->pages as $page) {
                $this->changeBook($page, $newBookId);
            }
        }

        return $bookChild;
    }

    /**
     * Render the page for viewing
     * @param Page $page
     * @param bool $blankIncludes
     * @return string
     */
    public function renderPage(Page $page, bool $blankIncludes = false) : string
    {
        $content = $page->html;

        if (!config('app.allow_content_scripts')) {
            $content = $this->escapeScripts($content);
        }

        if ($blankIncludes) {
            $content = $this->blankPageIncludes($content);
        } else {
            $content = $this->parsePageIncludes($content);
        }

        return $content;
    }

    /**
     * Remove any page include tags within the given HTML.
     * @param string $html
     * @return string
     */
    protected function blankPageIncludes(string $html) : string
    {
        return preg_replace("/{{@\s?([0-9].*?)}}/", '', $html);
    }

    /**
     * Parse any include tags "{{@<page_id>#section}}" to be part of the page.
     * @param string $html
     * @return mixed|string
     */
    protected function parsePageIncludes(string $html) : string
    {
        $matches = [];
        preg_match_all("/{{@\s?([0-9].*?)}}/", $html, $matches);

        $topLevelTags = ['table', 'ul', 'ol'];
        foreach ($matches[1] as $index => $includeId) {
            $splitInclude = explode('#', $includeId, 2);
            $pageId = intval($splitInclude[0]);
            if (is_nan($pageId)) {
                continue;
            }

            $matchedPage = $this->getById('page', $pageId);
            if ($matchedPage === null) {
                $html = str_replace($matches[0][$index], '', $html);
                continue;
            }

            if (count($splitInclude) === 1) {
                $html = str_replace($matches[0][$index], $matchedPage->html, $html);
                continue;
            }

            $doc = new DOMDocument();
            libxml_use_internal_errors(true);
            $doc->loadHTML(mb_convert_encoding('<body>'.$matchedPage->html.'</body>', 'HTML-ENTITIES', 'UTF-8'));
            $matchingElem = $doc->getElementById($splitInclude[1]);
            if ($matchingElem === null) {
                $html = str_replace($matches[0][$index], '', $html);
                continue;
            }
            $innerContent = '';
            $isTopLevel = in_array(strtolower($matchingElem->nodeName), $topLevelTags);
            if ($isTopLevel) {
                $innerContent .= $doc->saveHTML($matchingElem);
            } else {
                foreach ($matchingElem->childNodes as $childNode) {
                    $innerContent .= $doc->saveHTML($childNode);
                }
            }
            libxml_clear_errors();
            $html = str_replace($matches[0][$index], trim($innerContent), $html);
        }

        return $html;
    }

    /**
     * Escape script tags within HTML content.
     * @param string $html
     * @return string
     */
    protected function escapeScripts(string $html) : string
    {
        if ($html == '') {
            return $html;
        }

        libxml_use_internal_errors(true);
        $doc = new DOMDocument();
        $doc->loadHTML(mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8'));
        $xPath = new DOMXPath($doc);

        // Remove standard script tags
        $scriptElems = $xPath->query('//script');
        foreach ($scriptElems as $scriptElem) {
            $scriptElem->parentNode->removeChild($scriptElem);
        }

        // Remove data or JavaScript iFrames
        $badIframes = $xPath->query('//*[contains(@src, \'data:\')] | //*[contains(@src, \'javascript:\')] | //*[@srcdoc]');
        foreach ($badIframes as $badIframe) {
            $badIframe->parentNode->removeChild($badIframe);
        }

        // Remove 'on*' attributes
        $onAttributes = $xPath->query('//@*[starts-with(name(), \'on\')]');
        foreach ($onAttributes as $attr) {
            /** @var \DOMAttr $attr*/
            $attrName = $attr->nodeName;
            $attr->parentNode->removeAttribute($attrName);
        }

        $html = '';
        $topElems = $doc->documentElement->childNodes->item(0)->childNodes;
        foreach ($topElems as $child) {
            $html .= $doc->saveHTML($child);
        }

        return $html;
    }

    /**
     * Search for image usage within page content.
     * @param $imageString
     * @return mixed
     */
    public function searchForImage($imageString)
    {
        $pages = $this->entityQuery('page')->where('html', 'like', '%' . $imageString . '%')->get(['id', 'name', 'slug', 'book_id']);
        foreach ($pages as $page) {
            $page->url = $page->getUrl();
            $page->html = '';
            $page->text = '';
        }
        return count($pages) > 0 ? $pages : false;
    }

    /**
     * Destroy a bookshelf instance
     * @param Bookshelf $shelf
     * @throws Throwable
     */
    public function destroyBookshelf(Bookshelf $shelf)
    {
        $this->destroyEntityCommonRelations($shelf);
        $shelf->delete();
    }

    /**
     * Destroy a chapter and its relations.
     * @param Chapter $chapter
     * @throws Throwable
     */
    public function destroyChapter(Chapter $chapter)
    {
        if (count($chapter->pages) > 0) {
            foreach ($chapter->pages as $page) {
                $page->chapter_id = 0;
                $page->save();
            }
        }
        $this->destroyEntityCommonRelations($chapter);
        $chapter->delete();
    }

    /**
     * Destroy a given page along with its dependencies.
     * @param Page $page
     * @throws NotifyException
     * @throws Throwable
     */
    public function destroyPage(Page $page)
    {
        // Check if set as custom homepage & remove setting if not used or throw error if active
        $customHome = setting('app-homepage', '0:');
        if (intval($page->id) === intval(explode(':', $customHome)[0])) {
            if (setting('app-homepage-type') === 'page') {
                throw new NotifyException(trans('errors.page_custom_home_deletion'), $page->getUrl());
            }
            setting()->remove('app-homepage');
        }

        $this->destroyEntityCommonRelations($page);

        // Delete Attached Files
        $attachmentService = app(AttachmentService::class);
        foreach ($page->attachments as $attachment) {
            $attachmentService->deleteFile($attachment);
        }

        $page->delete();
    }

    /**
     * Destroy or handle the common relations connected to an entity.
     * @param Entity $entity
     * @throws Throwable
     */
    protected function destroyEntityCommonRelations(Entity $entity)
    {
        Activity::removeEntity($entity);
        $entity->views()->delete();
        $entity->permissions()->delete();
        $entity->tags()->delete();
        $entity->comments()->delete();
        $this->permissionService->deleteJointPermissionsForEntity($entity);
        $this->searchService->deleteEntityTerms($entity);
    }

    /**
     * Copy the permissions of a bookshelf to all child books.
     * Returns the number of books that had permissions updated.
     * @param Bookshelf $bookshelf
     * @return int
     * @throws Throwable
     */
    public function copyBookshelfPermissions(Bookshelf $bookshelf)
    {
        $shelfPermissions = $bookshelf->permissions()->get(['role_id', 'action'])->toArray();
        $shelfBooks = $bookshelf->books()->get();
        $updatedBookCount = 0;

        /** @var Book $book */
        foreach ($shelfBooks as $book) {
            if (!userCan('restrictions-manage', $book)) {
                continue;
            }
            $book->permissions()->delete();
            $book->restricted = $bookshelf->restricted;
            $book->permissions()->createMany($shelfPermissions);
            $book->save();
            $book->rebuildPermissions();
            $updatedBookCount++;
        }

        return $updatedBookCount;
    }
}
