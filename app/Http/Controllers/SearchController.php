<?php namespace BookStack\Http\Controllers;

use BookStack\Actions\ViewService;
use BookStack\Entities\Queries\Popular;
use BookStack\Entities\Tools\SearchRunner;
use BookStack\Entities\Tools\ShelfContext;
use BookStack\Entities\Tools\SearchOptions;
use BookStack\Entities\Tools\SiblingFetcher;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    protected $viewService;
    protected $searchRunner;
    protected $entityContextManager;

    public function __construct(
        ViewService $viewService,
        SearchRunner $searchRunner,
        ShelfContext $entityContextManager
    ) {
        $this->viewService = $viewService;
        $this->searchRunner = $searchRunner;
        $this->entityContextManager = $entityContextManager;
    }

    /**
     * Searches all entities.
     */
    public function search(Request $request)
    {
        $searchOpts = SearchOptions::fromRequest($request);
        $fullSearchString = $searchOpts->toString();
        $this->setPageTitle(trans('entities.search_for_term', ['term' => $fullSearchString]));

        $page = intval($request->get('page', '0')) ?: 1;
        $nextPageLink = url('/search?term=' . urlencode($fullSearchString) . '&page=' . ($page+1));

        $results = $this->searchRunner->searchEntities($searchOpts, 'all', $page, 20);

        return view('search.all', [
            'entities'   => $results['results'],
            'totalResults' => $results['total'],
            'searchTerm' => $fullSearchString,
            'hasNextPage' => $results['has_more'],
            'nextPageLink' => $nextPageLink,
            'options' => $searchOpts,
        ]);
    }

    /**
     * Searches all entities within a book.
     */
    public function searchBook(Request $request, int $bookId)
    {
        $term = $request->get('term', '');
        $results = $this->searchRunner->searchBook($bookId, $term);
        return view('partials.entity-list', ['entities' => $results]);
    }

    /**
     * Searches all entities within a chapter.
     */
    public function searchChapter(Request $request, int $chapterId)
    {
        $term = $request->get('term', '');
        $results = $this->searchRunner->searchChapter($chapterId, $term);
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
            $entities = $this->searchRunner->searchEntities(SearchOptions::fromString($searchTerm), 'all', 1, 20, $permission)['results'];
        } else {
            $entities = (new Popular)->run(20, 0, $entityTypes, $permission);
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

        $entities = (new SiblingFetcher)->fetch($type, $id);
        return view('partials.entity-list-basic', ['entities' => $entities, 'style' => 'compact']);
    }
}
