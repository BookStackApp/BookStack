<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Messages\PageUpdateNotification;
use BookStack\Activity\Tools\EntityWatchers;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Page;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Users\Models\User;

class PageUpdateNotificationHandler extends BaseNotificationHandler
{
    public function handle(string $activityType, Loggable|string $detail, User $user): void
    {
        if (!($detail instanceof Page)) {
            throw new \InvalidArgumentException("Detail for page update notifications must be a page");
        }

        $watchers = new EntityWatchers($detail, WatchLevels::UPDATES);
        $watcherIds = $watchers->getWatcherUserIds();

        if (!$watchers->isUserIgnoring($detail->owned_by) && $detail->ownedBy) {
            $userNotificationPrefs = new UserNotificationPreferences($detail->ownedBy);
            if ($userNotificationPrefs->notifyOnOwnPageChanges()) {
                $watcherIds[] = $detail->owned_by;
            }
        }

        $this->sendNotificationToUserIds(PageUpdateNotification::class, $watcherIds, $user, $detail);
    }
}
