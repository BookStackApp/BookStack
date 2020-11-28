<?php namespace BookStack\Http\Controllers\Api;

use BookStack\Api\ListingResponseBuilder;
use BookStack\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{

    protected $rules = [];

    /**
     * Provide a paginated listing JSON response in a standard format
     * taking into account any pagination parameters passed by the user.
     */
    protected function apiListingResponse(Builder $query, array $fields): JsonResponse
    {
        $listing = new ListingResponseBuilder($query, request(), $fields);
        return $listing->toResponse();
    }

    /**
     * Get the validation rules for this controller.
     */
    public function getValdationRules(): array
    {
        return $this->rules;
    }
}