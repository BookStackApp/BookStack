<?php

namespace BookStack\Activity;

use BookStack\Activity\Models\Comment;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use BookStack\Exceptions\NotifyException;
use BookStack\Facades\Activity as ActivityService;
use BookStack\Util\HtmlDescriptionFilter;
use Illuminate\Database\Eloquent\Builder;

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
     * Get a comment by ID, ensuring it is visible to the user based upon access to the page
     * which the comment is attached to.
     */
    public function getVisibleById(int $id): Comment
    {
        return $this->getQueryForVisible()->findOrFail($id);
    }

    /**
     * Start a query for comments visible to the user.
     * @return Builder<Comment>
     */
    public function getQueryForVisible(): Builder
    {
        return Comment::query()->scopes('visible');
    }

    /**
     * Create a new comment on an entity.
     */
    public function create(Entity $entity, string $html, ?int $parentId, string $contentRef): Comment
    {
        // Prevent comments being added to draft pages
        if ($entity instanceof Page && $entity->draft) {
            throw new \Exception(trans('errors.cannot_add_comment_to_draft'));
        }

        // Validate parent ID
        if ($parentId !== null) {
            $parentCommentExists = Comment::query()
                ->where('commentable_id', '=', $entity->id)
                ->where('commentable_type', '=', $entity->getMorphClass())
                ->where('local_id', '=', $parentId)
                ->exists();
            if (!$parentCommentExists) {
                $parentId = null;
            }
        }

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

        $comment->refresh()->unsetRelations();
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
    public function archive(Comment $comment, bool $log = true): Comment
    {
        if ($comment->parent_id) {
            throw new NotifyException('Only top-level comments can be archived.', '/', 400);
        }

        $comment->archived = true;
        $comment->save();

        if ($log) {
            ActivityService::add(ActivityType::COMMENT_UPDATE, $comment);
        }

        return $comment;
    }

    /**
     * Un-archive an existing comment.
     */
    public function unarchive(Comment $comment, bool $log = true): Comment
    {
        if ($comment->parent_id) {
            throw new NotifyException('Only top-level comments can be un-archived.', '/', 400);
        }

        $comment->archived = false;
        $comment->save();

        if ($log) {
            ActivityService::add(ActivityType::COMMENT_UPDATE, $comment);
        }

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
