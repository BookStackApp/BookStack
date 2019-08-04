<?php namespace BookStack\Http\Controllers;

use BookStack\Actions\ViewService;
use BookStack\Entities\EntityContextManager;
use BookStack\Entities\Repos\EntityRepo;
use BookStack\Entities\SearchService;
use BookStack\Exceptions\NotFoundException;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    protected $entityRepo;
    protected $viewService;
    protected $searchService;
    protected $entityContextManager;

    /**
     * SearchController constructor.
     * @param EntityRepo $entityRepo
     * @param ViewService $viewService
     * @param SearchService $searchService
     * @param EntityContextManager $entityContextManager
     */
    public function __construct(
        EntityRepo $entityRepo,
        ViewService $viewService,
        SearchService $searchService,
        EntityContextManager $entityContextManager
    ) {
        $this->entityRepo = $entityRepo;
        $this->viewService = $viewService;
        $this->searchService = $searchService;
        $this->entityContextManager = $entityContextManager;
        parent::__construct();
    }

    /**
     * Searches all entities.
     * @param Request $request
     * @return View
     * @internal param string $searchTerm
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('term');
        $this->setPageTitle(trans('entities.search_for_term', ['term' => $searchTerm]));

        $page = intval($request->get('page', '0')) ?: 1;
        $nextPageLink = url('/search?term=' . urlencode($searchTerm) . '&page=' . ($page+1));

        $results = $this->searchService->searchEntities($searchTerm, 'all', $page, 20);

        return view('search.all', [
            'entities'   => $results['results'],
            'totalResults' => $results['total'],
            'searchTerm' => $searchTerm,
            'hasNextPage' => $results['has_more'],
            'nextPageLink' => $nextPageLink
        ]);
    }


    /**
     * Searches all entities within a book.
     * @param Request $request
     * @param integer $bookId
     * @return View
     * @internal param string $searchTerm
     */
    public function searchBook(Request $request, $bookId)
    {
        $term = $request->get('term', '');
        $results = $this->searchService->searchBook($bookId, $term);
        return view('partials.entity-list', ['entities' => $results]);
    }

    /**
     * Searches all entities within a chapter.
     * @param Request $request
     * @param integer $chapterId
     * @return View
     * @internal param string $searchTerm
     */
    public function searchChapter(Request $request, $chapterId)
    {
        $term = $request->get('term', '');
        $results = $this->searchService->searchChapter($chapterId, $term);
        return view('partials.entity-list', ['entities' => $results]);
    }

    /**
     * Search for a list of entities and return a partial HTML response of matching entities.
     * Returns the most popular entities if no search is provided.
     * @param Request $request
     * @return mixed
     */
    public function searchEntitiesAjax(Request $request)
    {
        $entityTypes = $request->filled('types') ? explode(',', $request->get('types')) : ['page', 'chapter', 'book'];
        $searchTerm =  $request->get('term', false);
        $permission = $request->get('permission', 'view');

        // Search for entities otherwise show most popular
        if ($searchTerm !== false) {
            $searchTerm .= ' {type:'. implode('|', $entityTypes) .'}';
            $entities = $this->searchService->searchEntities($searchTerm, 'all', 1, 20, $permission)['results'];
        } else {
            $entities = $this->viewService->getPopular(20, 0, $entityTypes, $permission);
        }

        return view('search.entity-ajax-list', ['entities' => $entities]);
    }

    /**
     * Search siblings items in the system.
     * @param Request $request
     * @return Factory|View|mixed
     */
    public function searchSiblings(Request $request)
    {
        $type = $request->get('entity_type', null);
        $id = $request->get('entity_id', null);

        $entity = $this->entityRepo->getById($type, $id);
        if (!$entity) {
            return $this->jsonError(trans('errors.entity_not_found'), 404);
        }

        $entities = [];

        // Page in chapter
        if ($entity->isA('page') && $entity->chapter) {
            $entities = $this->entityRepo->getChapterChildren($entity->chapter);
        }

        // Page in book or chapter
        if (($entity->isA('page') && !$entity->chapter) || $entity->isA('chapter')) {
            $entities = $this->entityRepo->getBookDirectChildren($entity->book);
        }

        // Book
        // Gets just the books in a shelf if shelf is in context
        if ($entity->isA('book')) {
            $contextShelf = $this->entityContextManager->getContextualShelfForBook($entity);
            if ($contextShelf) {
                $entities = $this->entityRepo->getBookshelfChildren($contextShelf);
            } else {
                $entities = $this->entityRepo->getAll('book');
            }
        }

        // Shelve
        if ($entity->isA('bookshelf')) {
            $entities = $this->entityRepo->getAll('bookshelf');
        }

        return view('partials.entity-list-basic', ['entities' => $entities, 'style' => 'compact']);
    }
}
