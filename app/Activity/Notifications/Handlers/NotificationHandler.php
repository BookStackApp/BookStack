<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;
use BookStack\Users\Models\User;

interface NotificationHandler
{
    /**
     * Run this handler.
     * Provides the activity type, related activity detail/model
     * along with the user that triggered the activity.
     */
    public function handle(string $activityType, string|Loggable $detail, User $user): void;
}
