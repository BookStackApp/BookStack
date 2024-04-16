<?php

namespace BookStack\Activity\Controllers;

use BookStack\Activity\CommentRepo;
use BookStack\Entities\Queries\PageQueries;
use BookStack\Http\Controller;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    public function __construct(
        protected CommentRepo $commentRepo,
        protected PageQueries $pageQueries,
    ) {
    }

    /**
     * Save a new comment for a Page.
     *
     * @throws ValidationException
     */
    public function savePageComment(Request $request, int $pageId)
    {
        $input = $this->validate($request, [
            'html'      => ['required', 'string'],
            'parent_id' => ['nullable', 'integer'],
        ]);

        $page = $this->pageQueries->findVisibleById($pageId);
        if ($page === null) {
            return response('Not found', 404);
        }

        // Prevent adding comments to draft pages
        if ($page->draft) {
            return $this->jsonError(trans('errors.cannot_add_comment_to_draft'), 400);
        }

        // Create a new comment.
        $this->checkPermission('comment-create-all');
        $comment = $this->commentRepo->create($page, $input['html'], $input['parent_id'] ?? null);

        return view('comments.comment-branch', [
            'readOnly' => false,
            'branch' => [
                'comment' => $comment,
                'children' => [],
            ]
        ]);
    }

    /**
     * Update an existing comment.
     *
     * @throws ValidationException
     */
    public function update(Request $request, int $commentId)
    {
        $input = $this->validate($request, [
            'html' => ['required', 'string'],
        ]);

        $comment = $this->commentRepo->getById($commentId);
        $this->checkOwnablePermission('page-view', $comment->entity);
        $this->checkOwnablePermission('comment-update', $comment);

        $comment = $this->commentRepo->update($comment, $input['html']);

        return view('comments.comment', [
            'comment' => $comment,
            'readOnly' => false,
        ]);
    }

    /**
     * Delete a comment from the system.
     */
    public function destroy(int $id)
    {
        $comment = $this->commentRepo->getById($id);
        $this->checkOwnablePermission('comment-delete', $comment);

        $this->commentRepo->delete($comment);

        return response()->json(['message' => trans('entities.comment_deleted')]);
    }
}
