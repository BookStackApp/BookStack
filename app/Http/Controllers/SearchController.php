<?php namespace BookStack\Http\Controllers;

use BookStack\Actions\ViewService;
use BookStack\Entities\Book;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Entity;
use BookStack\Entities\Managers\EntityContext;
use BookStack\Entities\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $viewService;
    protected $searchService;
    protected $entityContextManager;

    /**
     * SearchController constructor.
     */
    public function __construct(
        ViewService $viewService,
        SearchService $searchService,
        EntityContext $entityContextManager
    ) {
        $this->viewService = $viewService;
        $this->searchService = $searchService;
        $this->entityContextManager = $entityContextManager;
        parent::__construct();
    }

    /**
     * Searches all entities.
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
     */
    public function searchBook(Request $request, int $bookId)
    {
        $term = $request->get('term', '');
        $results = $this->searchService->searchBook($bookId, $term);
        return view('partials.entity-list', ['entities' => $results]);
    }

    /**
     * Searches all entities within a chapter.
     */
    public function searchChapter(Request $request, int $chapterId)
    {
        $term = $request->get('term', '');
        $results = $this->searchService->searchChapter($chapterId, $term);
        return view('partials.entity-list', ['entities' => $results]);
    }

    /**
     * Search for a list of entities and return a partial HTML response of matching entities.
     * Returns the most popular entities if no search is provided.
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
     */
    public function searchSiblings(Request $request)
    {
        $type = $request->get('entity_type', null);
        $id = $request->get('entity_id', null);

        $entity = Entity::getEntityInstance($type)->newQuery()->visible()->find($id);
        if (!$entity) {
            return $this->jsonError(trans('errors.entity_not_found'), 404);
        }

        $entities = [];

        // Page in chapter
        if ($entity->isA('page') && $entity->chapter) {
            $entities = $entity->chapter->getVisiblePages();
        }

        // Page in book or chapter
        if (($entity->isA('page') && !$entity->chapter) || $entity->isA('chapter')) {
            $entities = $entity->book->getDirectChildren();
        }

        // Book
        // Gets just the books in a shelf if shelf is in context
        if ($entity->isA('book')) {
            $contextShelf = $this->entityContextManager->getContextualShelfForBook($entity);
            if ($contextShelf) {
                $entities = $contextShelf->visibleBooks()->get();
            } else {
                $entities = Book::visible()->get();
            }
        }

        // Shelve
        if ($entity->isA('bookshelf')) {
            $entities = Bookshelf::visible()->get();
        }

        return view('partials.entity-list-basic', ['entities' => $entities, 'style' => 'compact']);
    }
}
