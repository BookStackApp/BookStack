<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;

interface NotificationHandler
{
    /**
     * Run this handler.
     */
    public function handle(string $activityType, string|Loggable $detail): void;
}
