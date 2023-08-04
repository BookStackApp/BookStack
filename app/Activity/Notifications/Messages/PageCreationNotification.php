<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\LinkedMailMessageLine;
use BookStack\Entities\Models\Page;
use Illuminate\Notifications\Messages\MailMessage;

class PageCreationNotification extends BaseActivityNotification
{
    public function toMail(mixed $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;

        return (new MailMessage())
            ->subject("New Page: " . $page->getShortName())
            ->line("A new page has been created in " . setting('app-name') . ':')
            ->line("Page Name: " . $page->name)
            ->line("Created By: " . $this->user->name)
            ->action('View Page', $page->getUrl())
            ->line(new LinkedMailMessageLine(
                url('/preferences/notifications'),
                'This notification was sent to you because :link cover this type of activity for this item.',
                'your notification preferences',
            ));
    }
}
