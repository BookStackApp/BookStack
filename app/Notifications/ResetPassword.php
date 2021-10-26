<?php

namespace BookStack\Notifications;

class ResetPassword extends MailNotification
{
    /**
     * The password reset token.
     *
     * @var string
     */
    public $token;

    /**
     * Create a notification instance.
     *
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Build the mail representation of the notification.
     *
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail()
    {
        return $this->newMailMessage()
            ->subject(trans('auth.email_reset_subject', ['appName' => setting('app-name')]))
            ->line(trans('auth.email_reset_text'))
            ->action(trans('auth.reset_password'), url('password/reset/' . $this->token))
            ->line(trans('auth.email_reset_not_requested'));
    }
}
