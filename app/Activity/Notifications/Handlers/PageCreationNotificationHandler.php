<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Messages\PageCreationNotification;
use BookStack\Activity\Tools\EntityWatchers;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Users\Models\User;

class PageCreationNotificationHandler implements NotificationHandler
{
    public function handle(string $activityType, Loggable|string $detail, User $user): void
    {
        if (!($detail instanceof Page)) {
            throw new \InvalidArgumentException("Detail for page create notifications must be a page");
        }
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
        $users = User::query()->whereIn('id', $watchers->getWatcherUserIds())->get();

        // TODO - Clean this up, likely abstract to base class
        // TODO - Prevent sending to current user
        $permissions = app()->make(PermissionApplicator::class);
        foreach ($users as $user) {
            if ($user->can('receive-notifications') && $permissions->checkOwnableUserAccess($detail, 'view')) {
                $user->notify(new PageCreationNotification($detail, $user));
            }
        }
    }
}
