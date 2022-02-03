<?php

namespace BookStack\Http\Controllers\Api;

use BookStack\Api\ListingResponseBuilder;
use BookStack\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected $rules = [];
    protected $printHidden = [];

    /**
     * Provide a paginated listing JSON response in a standard format
     * taking into account any pagination parameters passed by the user.
     */
    protected function apiListingResponse(Builder $query, array $fields, array $protectedFieldsToPrint = []): JsonResponse
    {
        $listing = new ListingResponseBuilder($query, request(), $fields, $protectedFieldsToPrint);

        return $listing->toResponse();
    }

    /**
     * Get the validation rules for this controller.
     * Defaults to a $rules property but can be a rules() method.
     */
    public function getValdationRules(): array
    {
        if (method_exists($this, 'rules')) {
            return $this->rules();
        }

        return $this->rules;
    }
}
