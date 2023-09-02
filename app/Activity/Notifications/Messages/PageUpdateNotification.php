<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class PageUpdateNotification extends BaseActivityNotification
{
    public function toMail(User $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;

        $language = $notifiable->getLanguage();

        return $this->newMailMessage($language)
            ->subject(trans('notifications.updated_page_subject', ['pageName' => $page->getShortName()], $language))
            ->line(trans('notifications.updated_page_intro', ['appName' => setting('app-name')], $language))
            ->line(new ListMessageLine([
                trans('notifications.detail_page_name', [], $language) => $page->name,
                trans('notifications.detail_updated_by', [], $language) => $this->user->name,
            ]))
            ->line(trans('notifications.updated_page_debounce', [], $language))
            ->action(trans('notifications.action_view_page', [], $language), $page->getUrl())
            ->line($this->buildReasonFooterLine($language));
    }
}
