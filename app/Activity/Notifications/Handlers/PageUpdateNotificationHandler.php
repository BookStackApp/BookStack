<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Activity;
use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Messages\PageUpdateNotification;
use BookStack\Activity\Tools\EntityWatchers;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Page;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Users\Models\User;

class PageUpdateNotificationHandler extends BaseNotificationHandler
{
    public function handle(Activity $activity, Loggable|string $detail, User $user): void
    {
        if (!($detail instanceof Page)) {
            throw new \InvalidArgumentException("Detail for page update notifications must be a page");
        }

        // Get last update from activity
        $lastUpdate = $detail->activity()
            ->where('type', '=', ActivityType::PAGE_UPDATE)
            ->where('id', '!=', $activity->id)
            ->latest('created_at')
            ->first();

        // Return if the same user has already updated the page in the last 15 mins
        if ($lastUpdate && $lastUpdate->user_id === $user->id) {
            if ($lastUpdate->created_at->gt(now()->subMinutes(15))) {
                return;
            }
        }

        // Get active watchers
        $watchers = new EntityWatchers($detail, WatchLevels::UPDATES);
        $watcherIds = $watchers->getWatcherUserIds();

        // Add page owner if preferences allow
        if (!$watchers->isUserIgnoring($detail->owned_by) && $detail->ownedBy) {
            $userNotificationPrefs = new UserNotificationPreferences($detail->ownedBy);
            if ($userNotificationPrefs->notifyOnOwnPageChanges()) {
                $watcherIds[] = $detail->owned_by;
            }
        }

        $this->sendNotificationToUserIds(PageUpdateNotification::class, $watcherIds, $user, $detail, $detail);
    }
}
