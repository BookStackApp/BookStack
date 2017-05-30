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

    public function update($comment, $input) {
        $userId = user()->id;
        $comment->updated_by = $userId;
        $comment->fill($input);
        $comment->save();
        return $comment;
    }

    public function getPageComments($pageId) {
        $comments = $this->comment->getAllPageComments($pageId);
        $index = [];
        $totalComments = count($comments);
        // normalizing the response.
        foreach($comments as &$comment) {
            $comment = $this->normalizeComment($comment);
            $parentId = $comment->parent_id;
            if (empty($parentId)) {
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
            'comments' => $comments,
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