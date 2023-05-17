<?php

namespace BookStack\Search;

use BookStack\Api\ApiEntityListFormatter;
use BookStack\Entities\Models\Entity;
use BookStack\Http\Controllers\ApiController;
use Illuminate\Http\Request;

class SearchApiController extends ApiController
{
    protected SearchRunner $searchRunner;
    protected SearchResultsFormatter $resultsFormatter;

    protected $rules = [
        'all' => [
            'query'  => ['required'],
            'page'   => ['integer', 'min:1'],
            'count'  => ['integer', 'min:1', 'max:100'],
        ],
    ];

    public function __construct(SearchRunner $searchRunner, SearchResultsFormatter $resultsFormatter)
    {
        $this->searchRunner = $searchRunner;
        $this->resultsFormatter = $resultsFormatter;
    }

    /**
     * Run a search query against all main content types (shelves, books, chapters & pages)
     * in the system. Takes the same input as the main search bar within the BookStack
     * interface as a 'query' parameter. See https://www.bookstackapp.com/docs/user/searching/
     * for a full list of search term options. Results contain a 'type' property to distinguish
     * between: bookshelf, book, chapter & page.
     *
     * The paging parameters and response format emulates a standard listing endpoint
     * but standard sorting and filtering cannot be done on this endpoint. If a count value
     * is provided this will only be taken as a suggestion. The results in the response
     * may currently be up to 4x this value.
     */
    public function all(Request $request)
    {
        $this->validate($request, $this->rules['all']);

        $options = SearchOptions::fromString($request->get('query') ?? '');
        $page = intval($request->get('page', '0')) ?: 1;
        $count = min(intval($request->get('count', '0')) ?: 20, 100);

        $results = $this->searchRunner->searchEntities($options, 'all', $page, $count);
        $this->resultsFormatter->format($results['results']->all(), $options);

        $data = (new ApiEntityListFormatter($results['results']->all()))
            ->withType()->withTags()
            ->withField('preview_html', function (Entity $entity) {
                return [
                    'name'    => (string) $entity->getAttribute('preview_name'),
                    'content' => (string) $entity->getAttribute('preview_content'),
                ];
            })->format();

        return response()->json([
            'data'  => $data,
            'total' => $results['total'],
        ]);
    }
}
