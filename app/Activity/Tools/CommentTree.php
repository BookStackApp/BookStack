<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Page;

class CommentTree
{
    /**
     * The built nested tree structure array.
     * @var CommentTreeNode[]
     */
    protected array $tree;
    protected array $comments;

    public function __construct(
        protected Page $page
    ) {
        $this->comments = $this->loadComments();
        $this->tree = $this->createTree($this->comments);
    }

    public function enabled(): bool
    {
        return !setting('app-disable-comments');
    }

    public function empty(): bool
    {
        return count($this->getActive()) === 0;
    }

    public function count(): int
    {
        return count($this->comments);
    }

    public function getActive(): array
    {
        return array_filter($this->tree, fn (CommentTreeNode $node) => !$node->comment->archived);
    }

    public function activeThreadCount(): int
    {
        return count($this->getActive());
    }

    public function getArchived(): array
    {
        return array_filter($this->tree, fn (CommentTreeNode $node) => $node->comment->archived);
    }

    public function archivedThreadCount(): int
    {
        return count($this->getArchived());
    }

    public function getCommentNodeForId(int $commentId): ?CommentTreeNode
    {
        foreach ($this->tree as $node) {
            if ($node->comment->id === $commentId) {
                return $node;
            }
        }

        return null;
    }

    public function canUpdateAny(): bool
    {
        foreach ($this->comments as $comment) {
            if (userCan('comment-update', $comment)) {
                return true;
            }
        }

        return false;
    }

    /**
     * @param Comment[] $comments
     * @return CommentTreeNode[]
     */
    protected function createTree(array $comments): array
    {
        $byId = [];
        foreach ($comments as $comment) {
            $byId[$comment->local_id] = $comment;
        }

        $childMap = [];
        foreach ($comments as $comment) {
            $parent = $comment->parent_id;
            if (is_null($parent) || !isset($byId[$parent])) {
                $parent = 0;
            }

            if (!isset($childMap[$parent])) {
                $childMap[$parent] = [];
            }
            $childMap[$parent][] = $comment->local_id;
        }

        $tree = [];
        foreach ($childMap[0] ?? [] as $childId) {
            $tree[] = $this->createTreeNodeForId($childId, 0, $byId, $childMap);
        }

        return $tree;
    }

    protected function createTreeNodeForId(int $id, int $depth, array &$byId, array &$childMap): CommentTreeNode
    {
        $childIds = $childMap[$id] ?? [];
        $children = [];

        foreach ($childIds as $childId) {
            $children[] = $this->createTreeNodeForId($childId, $depth + 1, $byId, $childMap);
        }

        return new CommentTreeNode($byId[$id], $depth, $children);
    }

    protected function loadComments(): array
    {
        if (!$this->enabled()) {
            return [];
        }

        return $this->page->comments()
            ->with('createdBy')
            ->get()
            ->all();
    }
}
