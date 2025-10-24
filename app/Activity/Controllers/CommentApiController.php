<?php

declare(strict_types=1);

namespace BookStack\Activity\Controllers;

use BookStack\Activity\CommentRepo;
use BookStack\Activity\Models\Comment;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Http\ApiController;
use BookStack\Permissions\Permission;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

/**
 * The comment data model has a 'local_id' property, which is a unique integer ID
 * scoped to the page which the comment is on. The 'parent_id' is used for replies
 * and refers to the 'local_id' of the parent comment on the same page, not the main
 * globally unique 'id'.
 *
 * If you want to get all comments for a page in a tree-like structure, as reflected in
 * the UI, then that is provided on pages-read API responses.
 */
class CommentApiController extends ApiController
{
    protected array $rules = [
        'create' => [
            'page_id' => ['required', 'integer'],
            'reply_to' => ['nullable', 'integer'],
            'html' => ['required', 'string'],
            'content_ref' => ['string'],
        ],
        'update' => [
            'html' => ['string'],
            'archived' => ['boolean'],
        ]
    ];

    public function __construct(
        protected CommentRepo $commentRepo,
        protected PageQueries $pageQueries,
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
     * Create a new comment on a page.
     * If commenting as a reply to an existing comment, the 'reply_to' parameter
     * should be provided, set to the 'local_id' of the comment being replied to.
     */
    public function create(Request $request): JsonResponse
    {
        $this->checkPermission(Permission::CommentCreateAll);

        $input = $this->validate($request, $this->rules()['create']);
        $page = $this->pageQueries->findVisibleByIdOrFail($input['page_id']);

        $comment = $this->commentRepo->create(
            $page,
            $input['html'],
            $input['reply_to'] ?? null,
            $input['content_ref'] ?? '',
        );

        return response()->json($comment);
    }

    /**
     * Read the details of a single comment, along with its direct replies.
     */
    public function read(string $id): JsonResponse
    {
        $comment = $this->commentRepo->getVisibleById(intval($id));
        $comment->load('createdBy', 'updatedBy');

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


    /**
     * Update the content or archived status of an existing comment.
     *
     * Only provide a new archived status if needing to actively change the archive state.
     * Only top-level comments (non-replies) can be archived or unarchived.
     */
    public function update(Request $request, string $id): JsonResponse
    {
        $comment = $this->commentRepo->getVisibleById(intval($id));
        $this->checkOwnablePermission(Permission::CommentUpdate, $comment);

        $input = $this->validate($request, $this->rules()['update']);
        $hasHtml = isset($input['html']);

        if (isset($input['archived'])) {
            if ($input['archived']) {
                $this->commentRepo->archive($comment, !$hasHtml);
            } else {
                $this->commentRepo->unarchive($comment, !$hasHtml);
            }
        }

        if ($hasHtml) {
            $comment = $this->commentRepo->update($comment, $input['html']);
        }

        return response()->json($comment);
    }

    /**
     * Delete a single comment from the system.
     */
    public function delete(string $id): Response
    {
        $comment = $this->commentRepo->getVisibleById(intval($id));
        $this->checkOwnablePermission(Permission::CommentDelete, $comment);

        $this->commentRepo->delete($comment);

        return response('', 204);
    }
}
