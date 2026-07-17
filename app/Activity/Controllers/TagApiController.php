<?php

declare(strict_types=1);

namespace BookStack\Activity\Controllers;

use BookStack\Activity\TagRepo;
use BookStack\Http\ApiController;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Endpoints to query data about tags in the system.
 * You'll only see results based on tags applied to content you have access to.
 * There are no general create/update/delete endpoints here since tags do not exist
 * by themselves, they are managed via the items they are assigned to.
 */
class TagApiController extends ApiController
{
    public function __construct(
        protected TagRepo $tagRepo,
    ) {
    }

    protected function rules(): array
    {
        return [
            'listValues' => [
                'name' => ['required', 'string'],
            ],
        ];
    }

    /**
     * Get a list of tag names used in the system.
     * Only the name field can be used in filters.
     */
    public function listNames(): JsonResponse
    {
        $tagQuery = $this->tagRepo
            ->queryWithTotalsForApi('');

        return $this->apiListingResponse($tagQuery, [
            'name', 'values', 'usages', 'page_count', 'chapter_count', 'book_count', 'shelf_count',
        ], [], [
            'name'
        ]);
    }

    /**
     * Get a list of tag values, which have been set for the given tag name,
     * which must be provided as a query parameter on the request.
     * Only the value field can be used in filters.
     */
    public function listValues(Request $request): JsonResponse
    {
        $data = $this->validate($request, $this->rules()['listValues']);
        $name = $data['name'];

        $tagQuery = $this->tagRepo->queryWithTotalsForApi($name);

        return $this->apiListingResponse($tagQuery, [
            'name', 'value', 'usages', 'page_count', 'chapter_count', 'book_count', 'shelf_count',
        ], [], [
            'value',
        ]);
    }
}
