<?php namespace BookStack\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class MailNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Get the notification's channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Create a new mail message.
     * @return MailMessage
     */
    protected function newMailMessage()
    {
        return (new MailMessage)->view([
            'html' => 'vendor.notifications.email',
            'text' => 'vendor.notifications.email-plain'
        ]);
    }

}