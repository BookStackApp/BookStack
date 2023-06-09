<?php

namespace BookStack\Activity;

use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Entity;
use BookStack\Facades\Activity as ActivityService;
use League\CommonMark\CommonMarkConverter;

class CommentRepo
{
    /**
     * Get a comment by ID.
     */
    public function getById(int $id): Comment
    {
        return Comment::query()->findOrFail($id);
    }

    /**
     * Create a new comment on an entity.
     */
    public function create(Entity $entity, string $text, ?int $parent_id): Comment
    {
        $userId = user()->id;
        $comment = new Comment();

        $comment->text = $text;
        $comment->html = $this->commentToHtml($text);
        $comment->created_by = $userId;
        $comment->updated_by = $userId;
        $comment->local_id = $this->getNextLocalId($entity);
        $comment->parent_id = $parent_id;

        $entity->comments()->save($comment);
        ActivityService::add(ActivityType::COMMENTED_ON, $entity);

        return $comment;
    }

    /**
     * Update an existing comment.
     */
    public function update(Comment $comment, string $text): Comment
    {
        $comment->updated_by = user()->id;
        $comment->text = $text;
        $comment->html = $this->commentToHtml($text);
        $comment->save();

        return $comment;
    }

    /**
     * Delete a comment from the system.
     */
    public function delete(Comment $comment): void
    {
        $comment->delete();
    }

    /**
     * Convert the given comment Markdown to HTML.
     */
    public function commentToHtml(string $commentText): string
    {
        $converter = new CommonMarkConverter([
            'html_input'         => 'strip',
            'max_nesting_level'  => 10,
            'allow_unsafe_links' => false,
        ]);

        return $converter->convert($commentText);
    }

    /**
     * Get the next local ID relative to the linked entity.
     */
    protected function getNextLocalId(Entity $entity): int
    {
        $currentMaxId = $entity->comments()->max('local_id');

        return $currentMaxId + 1;
    }
}
