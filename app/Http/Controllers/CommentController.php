<?php namespace BookStack\Http\Controllers;

use BookStack\Repos\CommentRepo;
use BookStack\Repos\EntityRepo;
use Illuminate\Http\Request;
use Views;

// delete  -checkOwnablePermission \
class CommentController extends Controller
{
    protected $entityRepo;

    public function __construct(EntityRepo $entityRepo, CommentRepo $commentRepo)
    {
        $this->entityRepo = $entityRepo;
        $this->commentRepo = $commentRepo;
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

        return response()->json([
            'status'    => 'success',
            'message'   => $respMsg
        ]);

    }
    
    public function destroy($id) {
        $comment = $this->comment->findOrFail($id);
        $this->checkOwnablePermission('comment-delete', $comment);

        //
    }

    public function getComments($pageId, $commentId = null) {        
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
        
        $comments = $this->commentRepo->getCommentsForPage($pageId, $commentId);
        if (empty($commentId)) {
            // requesting for parent level comments, send the total count as well.
            $totalComments = $this->commentRepo->getCommentCount($pageId);
            return response()->json(array('success' => true, 'comments'=> $comments, 'total' => $totalComments));
        }
        return response()->json(array('success' => true, 'comments'=> $comments));
    }
}
