<?php namespace BookStack\Http\Controllers;

use Activity;
use BookStack\Actions\CommentRepo;
use BookStack\Entities\Page;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class CommentController extends Controller
{
    protected $commentRepo;

    /**
     * CommentController constructor.
     */
    public function __construct(CommentRepo $commentRepo)
    {
        $this->commentRepo = $commentRepo;
        parent::__construct();
    }

    /**
     * Save a new comment for a Page
     * @throws ValidationException
     */
    public function savePageComment(Request $request, int $pageId, int $commentId = null)
    {
        $this->validate($request, [
            'text' => 'required|string',
            'html' => 'required|string',
        ]);

        $page = Page::visible()->find($pageId);
        if ($page === null) {
            return response('Not found', 404);
        }

        $this->checkOwnablePermission('page-view', $page);

        // Prevent adding comments to draft pages
        if ($page->draft) {
            return $this->jsonError(trans('errors.cannot_add_comment_to_draft'), 400);
        }

        // Create a new comment.
        $this->checkPermission('comment-create-all');
        $comment = $this->commentRepo->create($page, $request->only(['html', 'text', 'parent_id']));
        Activity::add($page, 'commented_on', $page->book->id);
        return view('comments.comment', ['comment' => $comment]);
    }

    /**
     * Update an existing comment.
     * @throws ValidationException
     */
    public function update(Request $request, int $commentId)
    {
        $this->validate($request, [
            'text' => 'required|string',
            'html' => 'required|string',
        ]);

        $comment = $this->commentRepo->getById($commentId);
        $this->checkOwnablePermission('page-view', $comment->entity);
        $this->checkOwnablePermission('comment-update', $comment);

        $comment = $this->commentRepo->update($comment, $request->only(['html', 'text']));
        return view('comments.comment', ['comment' => $comment]);
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
