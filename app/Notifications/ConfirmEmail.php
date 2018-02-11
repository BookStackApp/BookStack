<?php

namespace BookStack\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmEmail extends Notification implements ShouldQueue
{

    use Queueable;
    
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
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
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
        return (new MailMessage)
                    ->subject(trans('auth.email_confirm_subject', $appName))
                    ->greeting(trans('auth.email_confirm_greeting', $appName))
                    ->line(trans('auth.email_confirm_text'))
                    ->action(trans('auth.email_confirm_action'), baseUrl('/register/confirm/' . $this->token));
    }
}
