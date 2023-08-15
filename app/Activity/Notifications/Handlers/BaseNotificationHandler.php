<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\Messages\BaseActivityNotification;
use BookStack\Entities\Models\Entity;
use BookStack\Permissions\PermissionApplicator;
use BookStack\Users\Models\User;

abstract class BaseNotificationHandler implements NotificationHandler
{
    public function __construct(
        protected PermissionApplicator $permissionApplicator
    ) {
    }

    /**
     * @param class-string<BaseActivityNotification> $notification
     * @param int[] $userIds
     */
    protected function sendNotificationToUserIds(string $notification, array $userIds, User $initiator, string|Loggable $detail, Entity $relatedModel): void
    {
        $users = User::query()->whereIn('id', array_unique($userIds))->get();

        foreach ($users as $user) {
            // Prevent sending to the user that initiated the activity
            if ($user->id === $initiator->id) {
                continue;
            }

            // Prevent sending of the user does not have notification permissions
            if (!$user->can('receive-notifications')) {
                continue;
            }

            // Prevent sending if the user does not have access to the related content
            if (!$this->permissionApplicator->checkOwnableUserAccess($relatedModel, 'view')) {
                continue;
            }

            // Send the notification
            $user->notify(new $notification($detail, $initiator));
        }
    }
}
