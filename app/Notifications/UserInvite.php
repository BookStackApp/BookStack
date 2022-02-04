<?php

namespace BookStack\Notifications;

use BookStack\Auth\User;
use Illuminate\Notifications\Messages\MailMessage;

class UserInvite extends MailNotification
{
    public $token;

    /**
     * Create a new notification instance.
     */
    public function __construct(string $token)
    {
        $this->token = $token;
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(User $notifiable): MailMessage
    {
        $appName = ['appName' => setting('app-name')];
        $language = setting()->getUser($notifiable, 'language');

        return $this->newMailMessage()
                ->subject(trans('auth.user_invite_email_subject', $appName, $language))
                ->greeting(trans('auth.user_invite_email_greeting', $appName, $language))
                ->line(trans('auth.user_invite_email_text', [], $language))
                ->action(trans('auth.user_invite_email_action', [], $language), url('/register/invite/' . $this->token));
    }
}
