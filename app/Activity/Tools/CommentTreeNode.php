<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Comment;

class CommentTreeNode
{
    public Comment $comment;
    public int $depth;

    /**
     * @var CommentTreeNode[]
     */
    public array $children;

    public function __construct(Comment $comment, int $depth, array $children)
    {
        $this->comment = $comment;
        $this->depth = $depth;
        $this->children = $children;
    }
}
