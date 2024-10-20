<?php

namespace BookStack\Search;

use BookStack\Api\ApiEntityListFormatter;
use BookStack\Entities\Models\Entity;
use BookStack\Http\ApiController;
use Illuminate\Http\Request;

class SearchApiController extends ApiController
{
    protected SearchRunner $searchRunner;
    protected SearchResultsFormatter $resultsFormatter;

    protected $rules = [
        'all' => [
            'query' => ['required'],
            'page' => ['integer', 'min:1'],
            'count' => ['integer', 'min:1', 'max:100'],
            'include' => ['string', 'regex:/^[a-zA-Z,]*$/'],
        ],
    ];

    /**
     * Valid include parameters and their corresponding formatter methods.
     * These parameters allow for additional related data, like titles or tags,
     * to be included in the search results when requested via the API.
     */
    protected const VALID_INCLUDES = [
        'titles' => 'withRelatedTitles',
        'tags' => 'withTags',
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
     * This method now supports the 'include' parameter, which allows API clients to specify related
     * fields (such as titles or tags) that should be included in the search results.
     *
     * The 'include' parameter is a comma-separated string. For example, adding `include=titles,tags`
     * will include both titles and tags in the API response. If the parameter is not provided, only
     * basic entity data will be returned.
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
        $includes = $this->parseIncludes($request->get('include', ''));

        $results = $this->searchRunner->searchEntities($options, 'all', $page, $count);
        $this->resultsFormatter->format($results['results']->all(), $options);

        $formatter = new ApiEntityListFormatter($results['results']->all());
        $formatter->withType(); // Always include type as it's essential for search results

        foreach ($includes as $include) {
            if (isset(self::VALID_INCLUDES[$include])) {
                $method = self::VALID_INCLUDES[$include];
                $formatter->$method();
            }
        }

        $formatter->withField('preview_html', function (Entity $entity) {
            return [
                'name' => (string) $entity->getAttribute('preview_name'),
                'content' => (string) $entity->getAttribute('preview_content'),
            ];
        });

        return response()->json([
            'data' => $formatter->format(),
            'total' => $results['total'],
        ]);
    }

    /**
     * Parse and validate the include parameter.
     *
     * @param string $includeString Comma-separated list of includes
     * @return array<string>
     */
    protected function parseIncludes(string $includeString): array
    {
        if (empty($includeString)) {
            return [];
        }

        return array_filter(
            explode(',', strtolower($includeString)),
            fn($include) => isset (self::VALID_INCLUDES[$include])
        );
    }
}
