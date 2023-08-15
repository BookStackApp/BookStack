<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Notifications\MessageParts\LinkedMailMessageLine;
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

    /**
     * Build the common reason footer line used in mail messages.
     */
    protected function buildReasonFooterLine(): LinkedMailMessageLine
    {
        return new LinkedMailMessageLine(
            url('/preferences/notifications'),
            trans('notifications.footer_reason'),
            trans('notifications.footer_reason_link'),
        );
    }
}
