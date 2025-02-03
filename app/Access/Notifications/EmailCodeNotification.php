<?php

namespace BookStack\Access\Notifications;

use BookStack\App\MailNotification;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;
use BookStack\Activity\Notifications\MessageParts\ListMessageLine;

class EmailCodeNotification extends MailNotification
{
    public function __construct(
        public string $validcode
    ) {
    }

    public function toMail(User $notifiable): MailMessage
    {
      $locale = $notifiable->getLocale();

        $listLines = array_filter([
            $locale->trans('notifications.detail_created_by') => $this->validcode,
        ]);

        return $this->newMailMessage()
            ->subject(trans('auth.email_reset_subject', ['appName' => setting('app-name')]))
            ->line(trans('auth.email_reset_text'))
            ->line(new ListMessageLine($listLines))
            ->action(trans('auth.login'), url('login'))
            ->line(trans('auth.email_reset_not_requested'));
    }
}
