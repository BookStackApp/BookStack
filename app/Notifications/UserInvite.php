<?php

namespace BookStack\Notifications;

use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class UserInvite extends MailNotification
{
    public function __construct(
        public string $token
    ) {
    }

    public function toMail(User $notifiable): MailMessage
    {
        $appName = ['appName' => setting('app-name')];
        $language = $notifiable->getLanguage();

        return $this->newMailMessage($language)
                ->subject(trans('auth.user_invite_email_subject', $appName, $language))
                ->greeting(trans('auth.user_invite_email_greeting', $appName, $language))
                ->line(trans('auth.user_invite_email_text', [], $language))
                ->action(trans('auth.user_invite_email_action', [], $language), url('/register/invite/' . $this->token));
    }
}
