<?php namespace BookStack\Http\Controllers;

use BookStack\Actions\ViewService;
use BookStack\Entities\EntityRepo;
use BookStack\Entities\SearchService;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $entityRepo;
    protected $viewService;
    protected $searchService;

    /**
     * SearchController constructor.
     * @param EntityRepo $entityRepo
     * @param ViewService $viewService
     * @param SearchService $searchService
     */
    public function __construct(EntityRepo $entityRepo, ViewService $viewService, SearchService $searchService)
    {
        $this->entityRepo = $entityRepo;
        $this->viewService = $viewService;
        $this->searchService = $searchService;
        parent::__construct();
    }

    /**
     * Searches all entities.
     * @param Request $request
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function search(Request $request)
    {
        $searchTerm = $request->get('term');
        $this->setPageTitle(trans('entities.search_for_term', ['term' => $searchTerm]));

        $page = intval($request->get('page', '0')) ?: 1;
        $nextPageLink = baseUrl('/search?term=' . urlencode($searchTerm) . '&page=' . ($page+1));

        $results = $this->searchService->searchEntities($searchTerm, 'all', $page, 20);

        return view('search/all', [
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
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function searchBook(Request $request, $bookId)
    {
        $term = $request->get('term', '');
        $results = $this->searchService->searchBook($bookId, $term);
        return view('partials/entity-list', ['entities' => $results]);
    }

    /**
     * Searches all entities within a chapter.
     * @param Request $request
     * @param integer $chapterId
     * @return \Illuminate\View\View
     * @internal param string $searchTerm
     */
    public function searchChapter(Request $request, $chapterId)
    {
        $term = $request->get('term', '');
        $results = $this->searchService->searchChapter($chapterId, $term);
        return view('partials/entity-list', ['entities' => $results]);
    }

    /**
     * Search for a list of entities and return a partial HTML response of matching entities.
     * Returns the most popular entities if no search is provided.
     * @param Request $request
     * @return mixed
     */
    public function searchEntitiesAjax(Request $request)
    {
        $entityTypes = $request->filled('types') ? collect(explode(',', $request->get('types'))) : collect(['page', 'chapter', 'book']);
        $searchTerm =  $request->get('term', false);
        $permission = $request->get('permission', 'view');

        // Search for entities otherwise show most popular
        if ($searchTerm !== false) {
            $searchTerm .= ' {type:'. implode('|', $entityTypes->toArray()) .'}';
            $entities = $this->searchService->searchEntities($searchTerm, 'all', 1, 20, $permission)['results'];
        } else {
            $entityNames = $entityTypes->map(function ($type) {
                return 'BookStack\\' . ucfirst($type); // TODO - Extract this elsewhere, too specific and stringy
            })->toArray();
            $entities = $this->viewService->getPopular(20, 0, $entityNames, $permission);
        }

        return view('search/entity-ajax-list', ['entities' => $entities]);
    }
}
