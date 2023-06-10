<?php

namespace BookStack\Search;

use BookStack\Entities\Queries\Popular;
use BookStack\Entities\Tools\SiblingFetcher;
use BookStack\Http\Controller;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function __construct(
        protected SearchRunner $searchRunner
    ) {
    }

    /**
     * Searches all entities.
     */
    public function search(Request $request, SearchResultsFormatter $formatter)
    {
        $searchOpts = SearchOptions::fromRequest($request);
        $fullSearchString = $searchOpts->toString();
        $this->setPageTitle(trans('entities.search_for_term', ['term' => $fullSearchString]));

        $page = intval($request->get('page', '0')) ?: 1;
        $nextPageLink = url('/search?term=' . urlencode($fullSearchString) . '&page=' . ($page + 1));

        $results = $this->searchRunner->searchEntities($searchOpts, 'all', $page, 20);
        $formatter->format($results['results']->all(), $searchOpts);

        return view('search.all', [
            'entities'     => $results['results'],
            'totalResults' => $results['total'],
            'searchTerm'   => $fullSearchString,
            'hasNextPage'  => $results['has_more'],
            'nextPageLink' => $nextPageLink,
            'options'      => $searchOpts,
        ]);
    }

    /**
     * Searches all entities within a book.
     */
    public function searchBook(Request $request, int $bookId)
    {
        $term = $request->get('term', '');
        $results = $this->searchRunner->searchBook($bookId, $term);

        return view('entities.list', ['entities' => $results]);
    }

    /**
     * Searches all entities within a chapter.
     */
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
    public function searchForSelector(Request $request)
    {
        $entityTypes = $request->filled('types') ? explode(',', $request->get('types')) : ['page', 'chapter', 'book'];
        $searchTerm = $request->get('term', false);
        $permission = $request->get('permission', 'view');

        // Search for entities otherwise show most popular
        if ($searchTerm !== false) {
            $searchTerm .= ' {type:' . implode('|', $entityTypes) . '}';
            $entities = $this->searchRunner->searchEntities(SearchOptions::fromString($searchTerm), 'all', 1, 20)['results'];
        } else {
            $entities = (new Popular())->run(20, 0, $entityTypes);
        }

        return view('search.parts.entity-selector-list', ['entities' => $entities, 'permission' => $permission]);
    }

    /**
     * Search for a list of entities and return a partial HTML response of matching entities
     * to be used as a result preview suggestion list for global system searches.
     */
    public function searchSuggestions(Request $request)
    {
        $searchTerm = $request->get('term', '');
        $entities = $this->searchRunner->searchEntities(SearchOptions::fromString($searchTerm), 'all', 1, 5)['results'];

        foreach ($entities as $entity) {
            $entity->setAttribute('preview_content', '');
        }

        return view('search.parts.entity-suggestion-list', [
            'entities' => $entities->slice(0, 5)
        ]);
    }

    /**
     * Search siblings items in the system.
     */
    public function searchSiblings(Request $request)
    {
        $type = $request->get('entity_type', null);
        $id = $request->get('entity_id', null);

        $entities = (new SiblingFetcher())->fetch($type, $id);

        return view('entities.list-basic', ['entities' => $entities, 'style' => 'compact']);
    }
}
