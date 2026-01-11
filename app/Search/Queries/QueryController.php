<?php

namespace BookStack\Search\Queries;

use BookStack\Http\Controller;
use BookStack\Search\SearchOptions;
use BookStack\Search\SearchRunner;
use Illuminate\Http\Request;

class QueryController extends Controller
{
    public function __construct(
        protected SearchRunner $searchRunner,
    ) {
        // TODO - Check via testing
        $this->middleware(function ($request, $next) {
            if (!LlmQueryServiceProvider::isEnabled()) {
                $this->showPermissionError('/');
            }
            return $next($request);
        });
    }

    /**
     * Show the view to start a vector/LLM-based query search.
     */
    public function show(Request $request)
    {
        $query = $request->get('ask', '');

        // TODO - Set page title

        return view('search.query', [
            'query' => $query,
        ]);
    }

    /**
     * Perform an LLM-based query search.
     */
    public function run(Request $request, LlmQueryRunner $llmRunner)
    {
        // TODO - Rate limiting
        $query = $request->get('query', '');

        return response()->eventStream(function () use ($query, $llmRunner) {

            $searchTerms = $llmRunner->queryToSearchTerms($query);
            $searchOptions = SearchOptions::fromTermArray($searchTerms);
            $searchResults = $this->searchRunner->searchEntities($searchOptions, count: 10)['results'];

            $entities = [];
            foreach ($searchResults as $entity) {
                $entityKey = $entity->getMorphClass() . ':' . $entity->id;
                if (!isset($entities[$entityKey])) {
                    $entities[$entityKey] = $entity;
                }
            }

            yield ['view' => view('entities.list', ['entities' => $entities])->render()];

            yield ['result' => $llmRunner->run($query, array_values($entities))];
        });
    }
}
