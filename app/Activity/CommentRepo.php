<?php

namespace BookStack\Activity;

use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Entity;
use BookStack\Exceptions\NotifyException;
use BookStack\Exceptions\PrettyException;
use BookStack\Facades\Activity as ActivityService;
use BookStack\Util\HtmlDescriptionFilter;

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
    public function create(Entity $entity, string $html, ?int $parentId, string $contentRef): Comment
    {
        $userId = user()->id;
        $comment = new Comment();

        $comment->html = HtmlDescriptionFilter::filterFromString($html);
        $comment->created_by = $userId;
        $comment->updated_by = $userId;
        $comment->local_id = $this->getNextLocalId($entity);
        $comment->parent_id = $parentId;
        $comment->content_ref = preg_match('/^bkmrk-(.*?):\d+:(\d*-\d*)?$/', $contentRef) === 1 ? $contentRef : '';

        $entity->comments()->save($comment);
        ActivityService::add(ActivityType::COMMENT_CREATE, $comment);
        ActivityService::add(ActivityType::COMMENTED_ON, $entity);

        return $comment;
    }

    /**
     * Update an existing comment.
     */
    public function update(Comment $comment, string $html): Comment
    {
        $comment->updated_by = user()->id;
        $comment->html = HtmlDescriptionFilter::filterFromString($html);
        $comment->save();

        ActivityService::add(ActivityType::COMMENT_UPDATE, $comment);

        return $comment;
    }


    /**
     * Archive an existing comment.
     */
    public function archive(Comment $comment): Comment
    {
        if ($comment->parent_id) {
            throw new NotifyException('Only top-level comments can be archived.', '/', 400);
        }

        $comment->archived = true;
        $comment->save();

        ActivityService::add(ActivityType::COMMENT_UPDATE, $comment);

        return $comment;
    }

    /**
     * Un-archive an existing comment.
     */
    public function unarchive(Comment $comment): Comment
    {
        if ($comment->parent_id) {
            throw new NotifyException('Only top-level comments can be un-archived.', '/', 400);
        }

        $comment->archived = false;
        $comment->save();

        ActivityService::add(ActivityType::COMMENT_UPDATE, $comment);

        return $comment;
    }

    /**
     * Delete a comment from the system.
     */
    public function delete(Comment $comment): void
    {
        $comment->delete();

        ActivityService::add(ActivityType::COMMENT_DELETE, $comment);
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
