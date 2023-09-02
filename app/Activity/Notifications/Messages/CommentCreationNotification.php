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

        $language = $notifiable->getLanguage();

        return $this->newMailMessage($language)
            ->subject(trans('notifications.new_comment_subject', ['pageName' => $page->getShortName()], $language))
            ->line(trans('notifications.new_comment_intro', ['appName' => setting('app-name')], $language))
            ->line(new ListMessageLine([
                trans('notifications.detail_page_name', [], $language) => $page->name,
                trans('notifications.detail_commenter', [], $language) => $this->user->name,
                trans('notifications.detail_comment', [], $language) => strip_tags($comment->html),
            ]))
            ->action(trans('notifications.action_view_comment', [], $language), $page->getUrl('#comment' . $comment->local_id))
            ->line($this->buildReasonFooterLine($language));
    }
}
