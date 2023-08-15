<?php

namespace BookStack\Activity\Notifications;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Activity;
use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Handlers\CommentCreationNotificationHandler;
use BookStack\Activity\Notifications\Handlers\NotificationHandler;
use BookStack\Activity\Notifications\Handlers\PageCreationNotificationHandler;
use BookStack\Activity\Notifications\Handlers\PageUpdateNotificationHandler;
use BookStack\Users\Models\User;

class NotificationManager
{
    /**
     * @var class-string<NotificationHandler>[]
     */
    protected array $handlers = [];

    public function handle(Activity $activity, string|Loggable $detail, User $user): void
    {
        $activityType = $activity->type;
        $handlersToRun = $this->handlers[$activityType] ?? [];
        foreach ($handlersToRun as $handlerClass) {
            /** @var NotificationHandler $handler */
            $handler = app()->make($handlerClass);
            $handler->handle($activity, $detail, $user);
        }
    }

    /**
     * @param class-string<NotificationHandler> $handlerClass
     */
    public function registerHandler(string $activityType, string $handlerClass): void
    {
        if (!isset($this->handlers[$activityType])) {
            $this->handlers[$activityType] = [];
        }

        if (!in_array($handlerClass, $this->handlers[$activityType])) {
            $this->handlers[$activityType][] = $handlerClass;
        }
    }

    public function loadDefaultHandlers(): void
    {
        $this->registerHandler(ActivityType::PAGE_CREATE, PageCreationNotificationHandler::class);
        $this->registerHandler(ActivityType::PAGE_UPDATE, PageUpdateNotificationHandler::class);
        $this->registerHandler(ActivityType::COMMENT_CREATE, CommentCreationNotificationHandler::class);
    }
}
