<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Models\Loggable;
use BookStack\Users\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

abstract class BaseActivityNotification extends Notification
{
    use Queueable;

    public function __construct(
        protected Loggable|string $detail,
        protected User $user,
    ) {
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
     */
    abstract public function toMail(mixed $notifiable): MailMessage;

    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            'activity_detail' => $this->detail,
            'activity_creator' => $this->user,
        ];
    }
}
