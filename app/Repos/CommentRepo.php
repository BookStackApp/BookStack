<?php namespace BookStack\Repos;

use BookStack\Comment;
use BookStack\Page;

/**
 * Class TagRepo
 * @package BookStack\Repos
 */
class CommentRepo {
    /**
     *
     * @var Comment $comment
     */
    protected $comment;

    public function __construct(Comment $comment)
    {
        $this->comment = $comment;
    }

    public function create (Page $page, $data = []) {
        $userId = user()->id;
        $comment = $this->comment->newInstance();
        $comment->fill($data);
        // new comment
        $comment->page_id = $page->id;
        $comment->created_by = $userId;
        $comment->updated_at = null;
        $comment->save();
        return $comment;
    }

    public function update($comment, $input, $activeOnly = true) {
        $userId = user()->id;
        $comment->updated_by = $userId;
        $comment->fill($input);

        // only update active comments by default.
        $whereClause = ['active' => 1];
        if (!$activeOnly) {
            $whereClause = [];
        }
        $comment->update($whereClause);
        return $comment;
    }

    public function delete($comment) {
        $comment->text = trans('errors.cannot_add_comment_to_draft');
        $comment->html = trans('errors.cannot_add_comment_to_draft');
        $comment->active = false;
        $userId = user()->id;
        $comment->updated_by = $userId;
        $comment->save();
        return $comment;
    }

    public function getPageComments($pageId) {
        $comments = $this->comment->getAllPageComments($pageId);
        $index = [];
        $totalComments = count($comments);
        $finalCommentList = [];

        // normalizing the response.
        for ($i = 0; $i < count($comments); ++$i) {
            $comment = $this->normalizeComment($comments[$i]);
            $parentId = $comment->parent_id;
            if (empty($parentId)) {
                $finalCommentList[] = $comment;
                $index[$comment->id] = $comment;
                continue;
            }

            if (empty($index[$parentId])) {
                // weird condition should not happen.
                continue;
            }
            if (empty($index[$parentId]->sub_comments)) {
                $index[$parentId]->sub_comments = [];
            }
            array_push($index[$parentId]->sub_comments, $comment);
            $index[$comment->id] = $comment;
        }
        return [
            'comments' => $finalCommentList,
            'total' => $totalComments
        ];
    }

    public function getCommentById($commentId) {
        return $this->normalizeComment($this->comment->getCommentById($commentId));
    }

    private function normalizeComment($comment) {
        if (empty($comment)) {
            return;
        }
        $comment->createdBy->avatar_url = $comment->createdBy->getAvatar(50);
        $comment->createdBy->profile_url = $comment->createdBy->getProfileUrl();
        if (!empty($comment->updatedBy)) {
            $comment->updatedBy->profile_url = $comment->updatedBy->getProfileUrl();
        }
        return $comment;
    }
}