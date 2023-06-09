<?php

namespace BookStack\Activity\Tools;

use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Page;

class CommentTree
{
    /**
     * The built nested tree structure array.
     * @var array{comment: Comment, depth: int, children: array}[]
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
        return count($this->tree) === 0;
    }

    public function count(): int
    {
        return count($this->comments);
    }

    public function get(): array
    {
        return $this->tree;
    }

    /**
     * @param Comment[] $comments
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
        foreach ($childMap[0] as $childId) {
            $tree[] = $this->createTreeForId($childId, 0, $byId, $childMap);
        }

        return $tree;
    }

    protected function createTreeForId(int $id, int $depth, array &$byId, array &$childMap): array
    {
        $childIds = $childMap[$id] ?? [];
        $children = [];

        foreach ($childIds as $childId) {
            $children[] = $this->createTreeForId($childId, $depth + 1, $byId, $childMap);
        }

        return [
            'comment' => $byId[$id],
            'depth' => $depth,
            'children' => $children,
        ];
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
