<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\Book;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class PageUpdateNotification extends BaseActivityNotification
{
    public function toMail(User $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;
        $book = $this->detail;

        $locale = $notifiable->getLocale();

        return $this->newMailMessage($locale)
            ->subject(trans('notifications.updated_page_subject', ['pageName' => $page->getShortName()], $locale))
            ->line(trans('notifications.updated_page_intro', ['appName' => setting('app-name')], $locale))
            ->line(new ListMessageLine([
                trans('notifications.detail_book_name', [], $locale) => $book->name,
                trans('notifications.detail_page_name', [], $locale) => $page->name,
                trans('notifications.detail_updated_by', [], $locale) => $this->user->name,
            ]))
            ->line(trans('notifications.updated_page_debounce', [], $locale))
            ->action(trans('notifications.action_view_page', [], $locale), $page->getUrl())
            ->line($this->buildReasonFooterLine($locale));
    }
}
