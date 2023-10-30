<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Models\Chapter;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class PageCreationNotification extends BaseActivityNotification
{
    public function toMail(User $notifiable): MailMessage
    {
        /** @var Page $page */
        $page = $this->detail;
        $book = $page->book;
        $chapterId = $page->chapter_id;
        $chapter = $chapterId ? Chapter::find($chapterId) : null;

        $locale = $notifiable->getLocale();

        $listMessageData = [
            $locale->trans('notifications.detail_page_name') => $page->name,
            '' => '',
        ];

        if ($chapter) {
            $listMessageData[$locale->trans('notifications.detail_chapter_name') => $chapter->name;
        }
    
        $listMessageData += [
            $locale->trans('notifications.detail_book_name') => $book->name,
            $locale->trans('notifications.detail_created_by') => $this->user->name,
        ];

        return $this->newMailMessage($locale)
            ->subject($locale->trans('notifications.new_page_subject', ['pageName' => $page->getShortName()]))
            ->line($locale->trans('notifications.new_page_intro', ['appName' => setting('app-name')], $locale))
            ->line(new ListMessageLine($listMessageData))
            ->action($locale->trans('notifications.action_view_page'), $page->getUrl())
            ->line($this->buildReasonFooterLine($locale));
    }
}
