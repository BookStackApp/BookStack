<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;

class CommentCreationNotificationHandler implements NotificationHandler
{
    public function handle(string $activityType, Loggable|string $detail): void
    {
        // TODO
    }
}
