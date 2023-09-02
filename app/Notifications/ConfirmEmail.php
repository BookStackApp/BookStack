<?php

namespace BookStack\Notifications;

use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmEmail extends MailNotification
{
    public function __construct(
        public string $token
    ) {
    }

    public function toMail(User $notifiable): MailMessage
    {
        $appName = ['appName' => setting('app-name')];

        return $this->newMailMessage()
                ->subject(trans('auth.email_confirm_subject', $appName))
                ->greeting(trans('auth.email_confirm_greeting', $appName))
                ->line(trans('auth.email_confirm_text'))
                ->action(trans('auth.email_confirm_action'), url('/register/confirm/' . $this->token));
    }
}
