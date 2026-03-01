<?php

namespace BookStack\Search;

use BookStack\Entities\Queries\PageQueries;
use BookStack\Entities\Queries\QueryPopular;
use BookStack\Entities\Tools\SiblingFetcher;
use BookStack\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;
use BookStack\Users\Models\User;

class SearchController extends Controller
{
    public function __construct(
        protected SearchRunner $searchRunner,
        protected PageQueries $pageQueries,
    ) {
    }

    public function search(Request $request, SearchResultsFormatter $formatter)
    {
        $searchOpts = SearchOptions::fromRequest($request);
        $fullSearchString = $searchOpts->toString();
        $page = intval($request->get('page', '0')) ?: 1;
        $count = setting()->getInteger('lists-page-count-search', 18, 1, 1000);

        $users = [];
        if (!empty($fullSearchString)) {
            $users = User::where('name', 'like', '%' . $fullSearchString . '%')
                ->take(5)
                ->get();
        }

        $results = $this->searchRunner->searchEntities($searchOpts, 'all', $page, $count);
        $formatter->format($results['results']->all(), $searchOpts);
        $paginator = new LengthAwarePaginator($results['results'], $results['total'], $count, $page);
        $paginator->setPath(url('/search'));
        $paginator->appends($request->except('page'));

        $this->setPageTitle(trans('entities.search_for_term', ['term' => $fullSearchString]));

        return view('search.all', [
            'entities'     => $results['results'],
            'totalResults' => $results['total'],
            'paginator'    => $paginator,
            'searchTerm'   => $fullSearchString,
            'options'      => $searchOpts,
            'users'        => $users,
        ]);
    }
    public function searchBook(Request $request, int $bookId)
    {
        $term = $request->get('term', '');      
        $results = $this->searchRunner->searchBook($bookId, $term);

        return view('entities.list', ['entities' => $results]);
    }
    public function searchChapter(Request $request, int $chapterId)
    {
        $term = $request->get('term', '');
        $results = $this->searchRunner->searchChapter($chapterId, $term);

        return view('entities.list', ['entities' => $results]);
    }

    /**
     * Search for a list of entities and return a partial HTML response of matching entities.
     * Returns the most popular entities if no search is provided.
     */
    public function searchForSelector(Request $request, QueryPopular $queryPopular)
    {
        $entityTypes = $request->filled('types') ? explode(',', $request->get('types')) : ['page', 'chapter', 'book'];
        $searchTerm = $request->get('term', false);
        $permission = $request->get('permission', 'view');

        if ($searchTerm !== false) {
            $options = SearchOptions::fromString($searchTerm);
            $options->setFilter('type', implode('|', $entityTypes));
            $entities = $this->searchRunner->searchEntities($options, 'all', 1, 20)['results'];
        } else {
            $entities = $queryPopular->run(20, 0, $entityTypes);
        }

        return view('search.parts.entity-selector-list', ['entities' => $entities, 'permission' => $permission]);
    }
    public function templatesForSelector(Request $request)
    {
        $searchTerm = $request->get('term', false);

        if ($searchTerm !== false) {
            $searchOptions = SearchOptions::fromString($searchTerm);
            $searchOptions->setFilter('is_template');
            $entities = $this->searchRunner->searchEntities($searchOptions, 'page', 1, 20)['results'];
        } else {
            $entities = $this->pageQueries->visibleTemplates()
                ->where('draft', '=', false)
                ->orderBy('updated_at', 'desc')
                ->take(20)
                ->get();
        }

        return view('search.parts.entity-selector-list', [
            'entities' => $entities,
            'permission' => 'view'
        ]);
    }
    public function searchSuggestions(Request $request)
    {
        $searchTerm = $request->get('term', '');
        
        $users = [];
        if (!empty($searchTerm)) {
            $users = User::where('name', 'like', '%' . $searchTerm . '%')
                ->take(3)
                ->get();
        }

        $entities = $this->searchRunner->searchEntities(SearchOptions::fromString($searchTerm), 'all', 1, 5)['results'];

        foreach ($entities as $entity) {
            $entity->setAttribute('preview_content', '');
        }

        return view('search.parts.entity-suggestion-list', [
            'entities' => $entities->slice(0, 5),
            'users'    => $users,
        ]);
    }
    public function searchSiblings(Request $request, SiblingFetcher $siblingFetcher)
    {
        $type = $request->get('entity_type', null);
        $id = $request->get('entity_id', null);

        $entities = $siblingFetcher->fetch($type, $id);

        return view('entities.list-basic', ['entities' => $entities, 'style' => 'compact']);
    }
}