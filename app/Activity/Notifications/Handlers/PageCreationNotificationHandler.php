<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Messages\PageCreationNotification;
use BookStack\Activity\Tools\EntityWatchers;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;

class PageCreationNotificationHandler extends BaseNotificationHandler
{
    public function handle(string $activityType, Loggable|string $detail, User $user): void
    {
        if (!($detail instanceof Page)) {
            throw new \InvalidArgumentException("Detail for page create notifications must be a page");
        }

        $watchers = new EntityWatchers($detail, WatchLevels::NEW);
        $this->sendNotificationToUserIds(PageCreationNotification::class, $watchers->getWatcherUserIds(), $user, $detail);
    }
}
