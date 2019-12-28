<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Api\ListingResponseBuilder;
use BookStack\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

class ApiController extends Controller
{

    /**
     * Provide a paginated listing JSON response in a standard format
     * taking into account any pagination parameters passed by the user.
     */
    protected function apiListingResponse(Builder $query, array $fields): JsonResponse
    {
        $listing = new ListingResponseBuilder($query, $fields);
        return $listing->toResponse();
    }
}