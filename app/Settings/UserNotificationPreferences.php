<?php

namespace BookStack\Settings;

use BookStack\Users\Models\User;

class UserNotificationPreferences
{
    public function __construct(
        protected User $user
    ) {
    }

    public function notifyOnOwnPageChanges(): bool
    {
        return $this->getNotificationSetting('own-page-changes');
    }

    public function notifyOnOwnPageComments(): bool
    {
        return $this->getNotificationSetting('own-page-comments');
    }

    public function notifyOnCommentReplies(): bool
    {
        return $this->getNotificationSetting('comment-replies');
    }

    public function updateFromSettingsArray(array $settings)
    {
        $allowList = ['own-page-changes', 'own-page-comments', 'comment-replies'];
        foreach ($settings as $setting => $status) {
            if (!in_array($setting, $allowList)) {
                continue;
            }

            $value = $status === 'true' ? 'true' : 'false';
            setting()->putUser($this->user, 'notifications#' . $setting, $value);
        }
    }

    protected function getNotificationSetting(string $key): bool
    {
        return setting()->getUser($this->user, 'notifications#' . $key);
    }
}
