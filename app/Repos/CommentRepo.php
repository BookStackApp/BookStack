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

    public function getCommentsForPage($pageId, $commentId, $count = 20) {
        // requesting parent comments
        $query = $this->comment->getCommentsByPage($pageId, $commentId);
        return $query->paginate($count);
    }

    public function getCommentCount($pageId) {
        return $this->comment->where('page_id', '=', $pageId)->count();
    }
}