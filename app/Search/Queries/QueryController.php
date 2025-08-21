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
    }

    /**
     * Show the view to start a vector/LLM-based query search.
     */
    public function show(Request $request)
    {
        // TODO - Validate if query system is active
        $query = $request->get('ask', '');

        // TODO - Placeholder
        $entities = $this->searchRunner->searchEntities(SearchOptions::fromString("cat"), 'all', 1, 20)['results'];

        // TODO - Set page title

        return view('search.query', [
            'query' => $query,
            'entities' => $entities,
        ]);
    }

    /**
     * Perform a vector/LLM-based query search.
     */
    public function run(Request $request, VectorSearchRunner $searchRunner, LlmQueryRunner $llmRunner)
    {
        // TODO - Validate if query system is active
        $query = $request->get('query', '');

        return response()->eventStream(function () use ($query, $searchRunner, $llmRunner) {
            $results = $query ? $searchRunner->run($query) : [];

            $count = count($results);
            yield "Found {$count} results for query: {$query}!";
            $llmResult = $llmRunner->run($query, $results);
            yield "LLM result: {$llmResult}";
        });
    }
}
