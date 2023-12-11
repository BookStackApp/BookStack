<?php

namespace BookStack\Access\Notifications;

use BookStack\App\MailNotification;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPasswordNotification extends MailNotification
{
    public function __construct(
        public string $token
    ) {
    }

    public function toMail(User $notifiable): MailMessage
    {
        return $this->newMailMessage()
            ->subject(trans('auth.email_reset_subject', ['appName' => setting('app-name')]))
            ->line(trans('auth.email_reset_text'))
            ->action(trans('auth.reset_password'), url('password/reset/' . $this->token))
            ->line(trans('auth.email_reset_not_requested'));
    }
}
