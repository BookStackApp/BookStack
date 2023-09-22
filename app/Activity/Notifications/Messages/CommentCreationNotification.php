<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Models\Comment;
use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use BookStack\Users\Models\User;
use Illuminate\Notifications\Messages\MailMessage;

class CommentCreationNotification extends BaseActivityNotification
{
    public function toMail(User $notifiable): MailMessage
    {
        /** @var Comment $comment */
        $comment = $this->detail;
        /** @var Page $page */
        $page = $comment->entity;

        $locale = $notifiable->getLocale();

        return $this->newMailMessage($locale)
            ->subject($locale->trans('notifications.new_comment_subject', ['pageName' => $page->getShortName()]))
            ->line($locale->trans('notifications.new_comment_intro', ['appName' => setting('app-name')]))
            ->line(new ListMessageLine([
                $locale->trans('notifications.detail_page_name') => $page->name,
                $locale->trans('notifications.detail_commenter') => $this->user->name,
                $locale->trans('notifications.detail_comment') => strip_tags($comment->html),
            ]))
            ->action($locale->trans('notifications.action_view_comment'), $page->getUrl('#comment' . $comment->local_id))
            ->line($this->buildReasonFooterLine($locale));
    }
}
