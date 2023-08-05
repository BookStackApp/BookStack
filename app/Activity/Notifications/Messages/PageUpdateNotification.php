<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\LinkedMailMessageLine;
use BookStack\Entities\Models\Page;
use Illuminate\Notifications\Messages\MailMessage;

class PageUpdateNotification extends BaseActivityNotification
{
    public function toMail(mixed $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;

        return (new MailMessage())
            ->subject("Updated Page: " . $page->getShortName())
            ->line("A page has been updated in " . setting('app-name') . ':')
            ->line("Page Name: " . $page->name)
            ->line("Updated By: " . $this->user->name)
            ->line("To prevent a mass of notifications, for a while you won't be sent notifications for further edits to this page by the same editor.")
            ->action('View Page', $page->getUrl())
            ->line(new LinkedMailMessageLine(
                url('/preferences/notifications'),
                'This notification was sent to you because :link cover this type of activity for this item.',
                'your notification preferences',
            ));
    }
}
