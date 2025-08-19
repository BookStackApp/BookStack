<?php

namespace BookStack\Search;

use BookStack\Http\Controller;
use BookStack\Search\Vectors\VectorSearchRunner;
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
    public function run(Request $request, VectorSearchRunner $runner)
    {
        // TODO - Validate if query system is active
        $query = $request->get('query', '');

        if ($query) {
            $results = $runner->run($query);
        } else {
            $results = null;
        }

        return view('search.query', [
            'results' => $results,
        ]);
    }
}
