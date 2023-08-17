<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Models\Comment;
use BookStack\Activity\Notifications\MessageParts\ListMessageLine;
use BookStack\Entities\Models\Page;
use Illuminate\Notifications\Messages\MailMessage;

class CommentCreationNotification extends BaseActivityNotification
{
    public function toMail(mixed $notifiable): MailMessage
    {
        /** @var Comment $comment */
        $comment = $this->detail;
        /** @var Page $page */
        $page = $comment->entity;

        return (new MailMessage())
            ->subject(trans('notifications.new_comment_subject', ['pageName' => $page->getShortName()]))
            ->line(trans('notifications.new_comment_intro', ['appName' => setting('app-name')]))
            ->line(new ListMessageLine([
                trans('notifications.detail_page_name') => $page->name,
                trans('notifications.detail_commenter') => $this->user->name,
                trans('notifications.detail_comment') => strip_tags($comment->html),
            ]))
            ->action(trans('notifications.action_view_comment'), $page->getUrl('#comment' . $comment->local_id))
            ->line($this->buildReasonFooterLine());
    }
}
