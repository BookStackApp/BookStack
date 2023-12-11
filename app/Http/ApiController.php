<?php

namespace BookStack\Http;

use BookStack\Api\ListingResponseBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;

abstract class ApiController extends Controller
{
    protected $rules = [];

    /**
     * Provide a paginated listing JSON response in a standard format
     * taking into account any pagination parameters passed by the user.
     */
    protected function apiListingResponse(Builder $query, array $fields, array $modifiers = []): JsonResponse
    {
        $listing = new ListingResponseBuilder($query, request(), $fields);

        foreach ($modifiers as $modifier) {
            $listing->modifyResults($modifier);
        }

        return $listing->toResponse();
    }

    /**
     * Get the validation rules for this controller.
     * Defaults to a $rules property but can be a rules() method.
     */
    public function getValidationRules(): array
    {
        return $this->rules();
    }

    /**
     * Get the validation rules for the actions in this controller.
     * Defaults to a $rules property but can be a rules() method.
     */
    protected function rules(): array
    {
        return $this->rules;
    }
}
