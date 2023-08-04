<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Models\Watch;
use BookStack\Activity\Tools\EntityWatchers;
use BookStack\Activity\WatchLevels;
use BookStack\Users\Models\User;

class PageCreationNotificationHandler implements NotificationHandler
{
    public function handle(string $activityType, Loggable|string $detail, User $user): void
    {
        // TODO

        // No user-level preferences to care about here.
        // Possible Scenarios:
        // ✅ User watching parent chapter
        // ✅ User watching parent book
        // ❌ User ignoring parent book
        // ❌ User ignoring parent chapter
        // ❌ User watching parent book, ignoring chapter
        // ✅ User watching parent book, watching chapter
        // ❌ User ignoring parent book, ignoring chapter
        // ✅ User ignoring parent book, watching chapter

        // Get all relevant watchers
        $watchers = new EntityWatchers($detail, WatchLevels::NEW);

        // TODO - need to check entity visibility and receive-notifications permissions.
        //   Maybe abstract this to a generic late-stage filter?
    }
}
