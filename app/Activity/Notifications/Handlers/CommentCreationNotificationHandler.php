<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Activity;
use BookStack\Activity\Models\Comment;
use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Messages\CommentCreationNotification;
use BookStack\Activity\Tools\EntityWatchers;
use BookStack\Activity\WatchLevels;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Users\Models\User;

class CommentCreationNotificationHandler extends BaseNotificationHandler
{
    public function handle(Activity $activity, Loggable|string $detail, User $user): void
    {
        if (!($detail instanceof Comment)) {
            throw new \InvalidArgumentException("Detail for comment creation notifications must be a comment");
        }

        // Main watchers
        $page = $detail->entity;
        $watchers = new EntityWatchers($page, WatchLevels::COMMENTS);
        $watcherIds = $watchers->getWatcherUserIds();

        // Page owner if user preferences allow
        if (!$watchers->isUserIgnoring($detail->created_by) && $detail->createdBy) {
            $userNotificationPrefs = new UserNotificationPreferences($detail->createdBy);
            if ($userNotificationPrefs->notifyOnOwnPageComments()) {
                $watcherIds[] = $detail->created_by;
            }
        }

        // Parent comment creator if preferences allow
        $parentComment = $detail->parent()->first();
        if ($parentComment && !$watchers->isUserIgnoring($parentComment->created_by) && $parentComment->createdBy) {
            $parentCommenterNotificationsPrefs = new UserNotificationPreferences($parentComment->createdBy);
            if ($parentCommenterNotificationsPrefs->notifyOnCommentReplies()) {
                $watcherIds[] = $parentComment->created_by;
            }
        }

        $this->sendNotificationToUserIds(CommentCreationNotification::class, $watcherIds, $user, $detail, $page);
    }
}
