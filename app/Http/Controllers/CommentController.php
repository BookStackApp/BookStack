<?php namespace BookStack\Http\Controllers;

use BookStack\Repos\CommentRepo;
use BookStack\Repos\EntityRepo;
use BookStack\Comment;
use Illuminate\Http\Request;

// delete  -checkOwnablePermission \
class CommentController extends Controller
{
    protected $entityRepo;

    public function __construct(EntityRepo $entityRepo, CommentRepo $commentRepo, Comment $comment)
    {
        $this->entityRepo = $entityRepo;
        $this->commentRepo = $commentRepo;
        $this->comment = $comment;
        parent::__construct();
    }

    public function save(Request $request, $pageId, $commentId = null)
    {
        $this->validate($request, [
            'text' => 'required|string',
            'html' => 'required|string',
        ]);

        try {
            $page = $this->entityRepo->getById('page', $pageId, true);
        } catch (ModelNotFoundException $e) {
            return response('Not found', 404);
        }

        if($page->draft) {
            // cannot add comments to drafts.
            return response()->json([
                'status' => 'error',
                'message' => trans('errors.cannot_add_comment_to_draft'),
            ], 400);
        }

        $this->checkOwnablePermission('page-view', $page);
        if (empty($commentId)) {
            // create a new comment.
            $this->checkPermission('comment-create-all');
            $comment = $this->commentRepo->create($page, $request->only(['text', 'html', 'parent_id']));
            $respMsg = trans('entities.comment_created');
        } else {
            // update existing comment
            // get comment by ID and check if this user has permission to update.
            $comment = $this->comment->findOrFail($commentId);
            $this->checkOwnablePermission('comment-update', $comment);
            $this->commentRepo->update($comment, $request->all());
            $respMsg = trans('entities.comment_updated');
        }

        $comment = $this->commentRepo->getCommentById($comment->id);

        return response()->json([
            'status'    => 'success',
            'message'   => $respMsg,
            'comment'   => $comment
        ]);

    }

    public function destroy($id) {
        $comment = $this->comment->findOrFail($id);
        $this->checkOwnablePermission('comment-delete', $comment);
        $this->commentRepo->delete($comment);
        $comment = $this->commentRepo->getCommentById($comment->id);

        return response()->json([
            'success' => true,
            'message' => trans('entities.comment_deleted'),
            'comment' => $comment
        ]);
    }


    public function getPageComments($pageId) {
        try {
            $page = $this->entityRepo->getById('page', $pageId, true);
        } catch (ModelNotFoundException $e) {
            return response('Not found', 404);
        }

        if($page->draft) {
            // cannot add comments to drafts.
            return response()->json([
                'status' => 'error',
                'message' => trans('errors.no_comments_for_draft'),
            ], 400);
        }

        $this->checkOwnablePermission('page-view', $page);

        $comments = $this->commentRepo->getPageComments($pageId);
        return response()->json(['success' => true, 'comments'=> $comments['comments'],
            'total' => $comments['total'], 'permissions' => [
                'comment_create' => $this->currentUser->can('comment-create-all'),
                'comment_update_own' => $this->currentUser->can('comment-update-own'),
                'comment_update_all' => $this->currentUser->can('comment-update-all'),
                'comment_delete_all' => $this->currentUser->can('comment-delete-all'),
                'comment_delete_own' => $this->currentUser->can('comment-delete-own'),
            ], 'user_id' => $this->currentUser->id]);
    }
}
