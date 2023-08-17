<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use Illuminate\Notifications\Messages\MailMessage;

class PageUpdateNotification extends BaseActivityNotification
{
    public function toMail(mixed $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;

        return (new MailMessage())
            ->subject(trans('notifications.updated_page_subject', ['pageName' => $page->getShortName()]))
            ->line(trans('notifications.updated_page_intro', ['appName' => setting('app-name')]))
            ->line(new ListMessageLine([
                trans('notifications.detail_page_name') => $page->name,
                trans('notifications.detail_updated_by') => $this->user->name,
            ]))
            ->line(trans('notifications.updated_page_debounce'))
            ->action(trans('notifications.action_view_page'), $page->getUrl())
            ->line($this->buildReasonFooterLine());
    }
}
