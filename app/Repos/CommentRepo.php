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
        if (empty($commentId)) {
            // requesting parent comments
            $query = $this->comment->getParentCommentsByPage($pageId);
            return $query->paginate($count);
        } else {
            // requesting the child comments.
            return Comment::whereRaw("page_id = $pageId AND parent_id = $commentId")->get();
        }        
    }
}