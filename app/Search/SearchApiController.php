<?php

declare(strict_types=1);

namespace BookStack\Search;

use BookStack\Api\ApiEntityListFormatter;
use BookStack\Entities\Models\Entity;
use BookStack\Http\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class SearchApiController extends ApiController
{
    protected array $rules = [
        'all' => [
            'query' => ['required'],
            'page'  => ['integer', 'min:1'],
            'count' => ['integer', 'min:1', 'max:100'],
        ],
        'book' => [
            'query' => ['required'],
            'page'  => ['integer', 'min:1'],
            'count' => ['integer', 'min:1', 'max:100'],
        ],
        'chapter' => [
            'query' => ['required'],
            'page'  => ['integer', 'min:1'],
            'count' => ['integer', 'min:1', 'max:100'],
        ],
    ];

    public function __construct(
        protected SearchRunner $searchRunner,
        protected SearchResultsFormatter $resultsFormatter
    ) {
    }

    /**
     * Run a search query against all main content types (shelves, books, chapters & pages)
     * in the system. Takes the same input as the main search bar within the BookStack
     * interface as a 'query' parameter. See https://www.bookstackapp.com/docs/user/searching/
     * for a full list of search term options. Results contain a 'type' property to distinguish
     * between: bookshelf, book, chapter & page.
     *
     * The paging parameters and response format emulates a standard listing endpoint
     * but standard sorting and filtering cannot be done on this endpoint.
     */
    public function all(Request $request): JsonResponse
    {
        $this->validate($request, $this->rules['all']);

        [$options, $page, $count] = $this->getRequestData($request);
        $results = $this->searchRunner->searchEntities($options, 'all', $page, $count);

        return $this->resultsResponse($results, $options);
    }

    /**
     * Run a search query against the contents of a single book, searching its pages and chapters.
     * Takes the same input as the 'all' endpoint, and the same input as the search box
     * shown within a book in the BookStack interface.
     *
     * Only pages and chapters are searched, since those are what a book contains. A
     * {type:...} term in the query can narrow that further but cannot widen it.
     */
    public function book(Request $request, string $id): JsonResponse
    {
        $this->validate($request, $this->rules['book']);

        [$options, $page, $count] = $this->getRequestData($request);
        $results = $this->searchRunner->searchBook(intval($id), $options, $page, $count);

        return $this->resultsResponse($results, $options);
    }

    /**
     * Run a search query against the contents of a single chapter.
     * Takes the same input as the 'all' endpoint, and the same input as the search box
     * shown within a chapter in the BookStack interface.
     *
     * Only pages are searched, since those are what a chapter contains.
     */
    public function chapter(Request $request, string $id): JsonResponse
    {
        $this->validate($request, $this->rules['chapter']);

        [$options, $page, $count] = $this->getRequestData($request);
        $results = $this->searchRunner->searchChapter(intval($id), $options, $page, $count);

        return $this->resultsResponse($results, $options);
    }

    /**
     * Get common search request data.
     * @return array{0: SearchOptions, 1: int, 2: int}
     */
    protected function getRequestData(Request $request): array
    {
        $options = SearchOptions::fromString($request->input('query') ?? '');
        $page = intval($request->input('page', '0')) ?: 1;
        $count = min(intval($request->input('count', '0')) ?: 20, 100);
        return [$options, $page, $count];
    }

    /**
     * Format a set of search results into the standard API response shape.
     *
     * @param array{total: int, results: Collection} $results
     */
    protected function resultsResponse(array $results, SearchOptions $options): JsonResponse
    {
        $this->resultsFormatter->format($results['results']->all(), $options);

        $data = (new ApiEntityListFormatter($results['results']->all()))
            ->withType()->withTags()->withParents()
            ->withField('preview_html', function (Entity $entity) {
                return [
                    'name' => (string) $entity->getAttribute('preview_name'),
                    'content' => (string) $entity->getAttribute('preview_content'),
                ];
            })->format();

        return response()->json([
            'data' => $data,
            'total' => $results['total'],
        ]);
    }
}
