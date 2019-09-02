<?php namespace BookStack\Notifications;

class ConfirmEmail extends MailNotification
{
    public $token;

    /**
     * Create a new notification instance.
     * @param string $token
     */
    public function __construct($token)
    {
        $this->token = $token;
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
        $appName = ['appName' => setting('app-name')];
        return $this->newMailMessage()
                ->subject(trans('auth.email_confirm_subject', $appName))
                ->greeting(trans('auth.email_confirm_greeting', $appName))
                ->line(trans('auth.email_confirm_text'))
                ->action(trans('auth.email_confirm_action'), url('/register/confirm/' . $this->token));
    }
}
