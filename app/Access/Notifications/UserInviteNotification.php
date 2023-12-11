<?php

namespace BookStack\Access\Notifications;

use BookStack\App\MailNotification;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class UserInviteNotification extends MailNotification
{
    public function __construct(
        public string $token
    ) {
    }

    public function toMail(User $notifiable): MailMessage
    {
        $appName = ['appName' => setting('app-name')];
        $locale = $notifiable->getLocale();

        return $this->newMailMessage($locale)
                ->subject($locale->trans('auth.user_invite_email_subject', $appName))
                ->greeting($locale->trans('auth.user_invite_email_greeting', $appName))
                ->line($locale->trans('auth.user_invite_email_text'))
                ->action($locale->trans('auth.user_invite_email_action'), url('/register/invite/' . $this->token));
    }
}
