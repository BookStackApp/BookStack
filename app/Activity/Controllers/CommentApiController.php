<?php

declare(strict_types=1);

namespace BookStack\Activity\Controllers;

use BookStack\Activity\CommentRepo;
use BookStack\Http\ApiController;
use Illuminate\Http\JsonResponse;

class CommentApiController extends ApiController
{
    // TODO - Add tree-style comment listing to page-show responses.
    // TODO - list
    // TODO - create
    // TODO - read
    // TODO - update
    // TODO - delete

    // TODO - Test visibility controls
    // TODO - Test permissions of each action

    // TODO - Support intro block for API docs so we can explain the
    //   properties for comments in a shared kind of way?

    public function __construct(
        protected CommentRepo $commentRepo,
    ) {
    }


    /**
     * Get a listing of comments visible to the user.
     */
    public function list(): JsonResponse
    {
        $query = $this->commentRepo->getQueryForVisible();

        return $this->apiListingResponse($query, [
            'id', 'commentable_id', 'commentable_type', 'parent_id', 'local_id', 'content_ref', 'created_by', 'updated_by', 'created_at', 'updated_at'
        ]);
    }
}
