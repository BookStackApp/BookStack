<?php

declare(strict_types=1);

namespace BookStack\Activity\Controllers;

use BookStack\Activity\CommentRepo;
use BookStack\Activity\Models\Comment;
use BookStack\Http\ApiController;
use Illuminate\Http\JsonResponse;

/**
 * The comment data model has a 'local_id' property, which is a unique integer ID
 * scoped to the page which the comment is on. The 'parent_id' is used for replies
 * and refers to the 'local_id' of the parent comment on the same page, not the main
 * globally unique 'id'.
 */
class CommentApiController extends ApiController
{
    // TODO - Add tree-style comment listing to page-show responses.
    // TODO - create
    // TODO - update
    // TODO - delete

    // TODO - Test visibility controls
    // TODO - Test permissions of each action

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

    /**
     * Read the details of a single comment, along with its direct replies.
     */
    public function read(string $id): JsonResponse
    {
        $comment = $this->commentRepo->getQueryForVisible()
            ->where('id', '=', $id)->firstOrFail();

        $replies = $this->commentRepo->getQueryForVisible()
            ->where('parent_id', '=', $comment->local_id)
            ->where('commentable_id', '=', $comment->commentable_id)
            ->where('commentable_type', '=', $comment->commentable_type)
            ->get();

        /** @var Comment[] $toProcess */
        $toProcess = [$comment, ...$replies];
        foreach ($toProcess as $commentToProcess) {
            $commentToProcess->setAttribute('html', $commentToProcess->safeHtml());
            $commentToProcess->makeVisible('html');
        }

        $comment->setRelation('replies', $replies);

        return response()->json($comment);
    }
}
