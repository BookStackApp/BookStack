<?php namespace BookStack\Entities\Repos;

use BookStack\Actions\TagRepo;
use BookStack\Actions\ViewService;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\User;
use BookStack\Entities\Book;
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
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

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
     * @return \Illuminate\Database\Query\Builder
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
     * @return \BookStack\Entities\Entity
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
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|Collection
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
     * @param string|bool $bookSlug
     * @return \BookStack\Entities\Entity
     * @throws NotFoundException
     */
    public function getBySlug($type, $slug, $bookSlug = false)
    {
        $q = $this->entityQuery($type)->where('slug', '=', $slug);

        if (strtolower($type) === 'chapter' || strtolower($type) === 'page') {
            $q = $q->where('book_id', '=', function ($query) use ($bookSlug) {
                $query->select('id')
                    ->from($this->entityProvider->book->getTable())
                    ->where('slug', '=', $bookSlug)->limit(1);
            });
        }
        $entity = $q->first();
        if ($entity === null) {
            throw new NotFoundException(trans('errors.' . strtolower($type) . '_not_found'));
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
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
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
     * @param string|bool $type
     * @param int $count
     * @param int $page
     * @return mixed
     */
    public function getPopular($type, $count = 10, $page = 0)
    {
        $filter = is_bool($type) ? false : $this->entityProvider->get($type);
        return $this->viewService->getPopular($count, $page, $filter);
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
     * @param \BookStack\Entities\Bookshelf $bookshelf
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
     * @param \BookStack\Entities\Book $book
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
     * Get the child items for a chapter sorted by priority but
     * with draft items floated to the top.
     * @param \BookStack\Entities\Chapter $chapter
     * @return \Illuminate\Database\Eloquent\Collection|static[]
     */
    public function getChapterChildren(Chapter $chapter)
    {
        return $this->permissionService->enforceEntityRestrictions('page', $chapter->pages())
            ->orderBy('draft', 'DESC')->orderBy('priority', 'ASC')->get();
    }


    /**
     * Get the next sequential priority for a new child element in the given book.
     * @param \BookStack\Entities\Book $book
     * @return int
     */
    public function getNewBookPriority(Book $book)
    {
        $lastElem = $this->getBookChildren($book)->pop();
        return $lastElem ? $lastElem->priority + 1 : 0;
    }

    /**
     * Get a new priority for a new page to be added to the given chapter.
     * @param \BookStack\Entities\Chapter $chapter
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
        $query = $this->entityProvider->get($type)->where('slug', '=', $slug);
        if (strtolower($type) === 'page' || strtolower($type) === 'chapter') {
            $query = $query->where('book_id', '=', $bookId);
        }
        if ($currentId) {
            $query = $query->where('id', '!=', $currentId);
        }
        return $query->count() > 0;
    }

    /**
     * Updates entity restrictions from a request
     * @param Request $request
     * @param \BookStack\Entities\Entity $entity
     * @throws \Throwable
     */
    public function updateEntityPermissionsFromRequest(Request $request, Entity $entity)
    {
        $entity->restricted = $request->get('restricted', '') === 'true';
        $entity->permissions()->delete();

        if ($request->filled('restrictions')) {
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
     * Create a new entity from request input.
     * Used for books and chapters.
     * @param string $type
     * @param array $input
     * @param bool|Book $book
     * @return \BookStack\Entities\Entity
     */
    public function createFromInput($type, $input = [], $book = false)
    {
        $isChapter = strtolower($type) === 'chapter';
        $entityModel = $this->entityProvider->get($type)->newInstance($input);
        $entityModel->slug = $this->findSuitableSlug($type, $entityModel->name, false, $isChapter ? $book->id : false);
        $entityModel->created_by = user()->id;
        $entityModel->updated_by = user()->id;
        $isChapter ? $book->chapters()->save($entityModel) : $entityModel->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entityModel, $input['tags']);
        }

        $this->permissionService->buildJointPermissionsForEntity($entityModel);
        $this->searchService->indexEntity($entityModel);
        return $entityModel;
    }

    /**
     * Update entity details from request input.
     * Used for books and chapters
     * @param string $type
     * @param \BookStack\Entities\Entity $entityModel
     * @param array $input
     * @return \BookStack\Entities\Entity
     */
    public function updateFromInput($type, Entity $entityModel, $input = [])
    {
        if ($entityModel->name !== $input['name']) {
            $entityModel->slug = $this->findSuitableSlug($type, $input['name'], $entityModel->id);
        }
        $entityModel->fill($input);
        $entityModel->updated_by = user()->id;
        $entityModel->save();

        if (isset($input['tags'])) {
            $this->tagRepo->saveTagsToEntity($entityModel, $input['tags']);
        }

        $this->permissionService->buildJointPermissionsForEntity($entityModel);
        $this->searchService->indexEntity($entityModel);
        return $entityModel;
    }

    /**
     * Sync the books assigned to a shelf from a comma-separated list
     * of book IDs.
     * @param \BookStack\Entities\Bookshelf $shelf
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
     * @param string $type
     * @param integer $newBookId
     * @param Entity $entity
     * @param bool $rebuildPermissions
     * @return \BookStack\Entities\Entity
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
     * @param Book $book
     */
    public function buildJointPermissionsForBook(Book $book)
    {
        $this->permissionService->buildJointPermissionsForEntity($book);
    }

    /**
     * Format a name as a url slug.
     * @param $name
     * @return string
     */
    protected function nameToSlug($name)
    {
        $slug = preg_replace('/[\+\/\\\?\@\}\{\.\,\=\[\]\#\&\!\*\'\;\:\$\%]/', '', mb_strtolower($name));
        $slug = preg_replace('/\s{2,}/', ' ', $slug);
        $slug = str_replace(' ', '-', $slug);
        if ($slug === "") {
            $slug = substr(md5(rand(1, 500)), 0, 5);
        }
        return $slug;
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
        $scriptSearchRegex = '/<script.*?>.*?<\/script>/ms';
        $matches = [];
        preg_match_all($scriptSearchRegex, $html, $matches);

        foreach ($matches[0] as $match) {
            $html = str_replace($match, htmlentities($match), $html);
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
        $pages = $this->entityQuery('page')->where('html', 'like', '%' . $imageString . '%')->get();
        foreach ($pages as $page) {
            $page->url = $page->getUrl();
            $page->html = '';
            $page->text = '';
        }
        return count($pages) > 0 ? $pages : false;
    }

    /**
     * Destroy a bookshelf instance
     * @param \BookStack\Entities\Bookshelf $shelf
     * @throws \Throwable
     */
    public function destroyBookshelf(Bookshelf $shelf)
    {
        $this->destroyEntityCommonRelations($shelf);
        $shelf->delete();
    }

    /**
     * Destroy the provided book and all its child entities.
     * @param \BookStack\Entities\Book $book
     * @throws NotifyException
     * @throws \Throwable
     */
    public function destroyBook(Book $book)
    {
        foreach ($book->pages as $page) {
            $this->destroyPage($page);
        }
        foreach ($book->chapters as $chapter) {
            $this->destroyChapter($chapter);
        }
        $this->destroyEntityCommonRelations($book);
        $book->delete();
    }

    /**
     * Destroy a chapter and its relations.
     * @param \BookStack\Entities\Chapter $chapter
     * @throws \Throwable
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
     * @throws \Throwable
     */
    public function destroyPage(Page $page)
    {
        // Check if set as custom homepage
        $customHome = setting('app-homepage', '0:');
        if (intval($page->id) === intval(explode(':', $customHome)[0])) {
            throw new NotifyException(trans('errors.page_custom_home_deletion'), $page->getUrl());
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
     * @param \BookStack\Entities\Entity $entity
     * @throws \Throwable
     */
    protected function destroyEntityCommonRelations(Entity $entity)
    {
        \Activity::removeEntity($entity);
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
     * @param \BookStack\Entities\Bookshelf $bookshelf
     * @return int
     * @throws \Throwable
     */
    public function copyBookshelfPermissions(Bookshelf $bookshelf)
    {
        $shelfPermissions = $bookshelf->permissions()->get(['role_id', 'action'])->toArray();
        $shelfBooks = $bookshelf->books()->get();
        $updatedBookCount = 0;

        foreach ($shelfBooks as $book) {
            if (!userCan('restrictions-manage', $book)) {
                continue;
            }
            $book->permissions()->delete();
            $book->restricted = $bookshelf->restricted;
            $book->permissions()->createMany($shelfPermissions);
            $book->save();
            $this->permissionService->buildJointPermissionsForEntity($book);
            $updatedBookCount++;
        }

        return $updatedBookCount;
    }
}
