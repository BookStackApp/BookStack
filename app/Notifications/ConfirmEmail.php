<?php

namespace BookStack\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ConfirmEmail extends Notification
{

    /**
     * Create a new notification instance.
     *
     */
    public function __construct()
    {
        //
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
        return (new MailMessage)
                    ->subject('Confirm your email on ' . session('app-name'))
                    ->greeting('Thanks for joining ' . setting('app-name') . '!')
                    ->line('Please confirm your email address by clicking the button below:')
                    ->action('Confirm Email', baseUrl('/register/confirm/' . $notifiable->token));
    }

}
