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
use DOMDocument;
use DOMXPath;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\Builder as QueryBuilder;
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
        $entityModel = $this->entityProvider->get($type);
        $query = $entityModel::visible()->orderBy('created_at', 'desc');

        if ($entityModel->isA('page')) {
            $query->where('draft', '=', false);
        }

        if ($additionalQuery !== false && is_callable($additionalQuery)) {
            $additionalQuery($query);
        }

        return $query->skip($page * $count)->take($count)->get();
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

}
