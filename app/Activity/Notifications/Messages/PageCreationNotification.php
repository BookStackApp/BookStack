<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\MessageParts\EntityLinkMessageLine;
use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class PageCreationNotification extends BaseActivityNotification
{
    public function toMail(User $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;

        $locale = $notifiable->getLocale();

        $listLines = array_filter([
            $locale->trans('notifications.detail_page_name') => new EntityLinkMessageLine($page),
            $locale->trans('notifications.detail_page_path') => $this->buildPagePathLine($page, $notifiable),
            $locale->trans('notifications.detail_created_by') => $this->user->name,
        ]);

        return $this->newMailMessage($locale)
            ->subject($locale->trans('notifications.new_page_subject', ['pageName' => $page->getShortName()]))
            ->line($locale->trans('notifications.new_page_intro', ['appName' => setting('app-name')]))
            ->line(new ListMessageLine($listLines))
            ->action($locale->trans('notifications.action_view_page'), $page->getUrl())
            ->line($this->buildReasonFooterLine($locale));
    }
}
