<?php

namespace BookStack\Activity\Notifications\Messages;

use BookStack\Activity\Models\Comment;
use BookStack\Activity\Notifications\LinkedMailMessageLine;
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
            ->subject("New Comment on Page: " . $page->getShortName())
            ->line("A user has commented on a page in " . setting('app-name') . ':')
            ->line("Page Name: " . $page->name)
            ->line("Commenter: " . $this->user->name)
            ->line("Comment: " . strip_tags($comment->html))
            ->action('View Comment', $page->getUrl('#comment' . $comment->local_id))
            ->line(new LinkedMailMessageLine(
                url('/preferences/notifications'),
                'This notification was sent to you because :link cover this type of activity for this item.',
                'your notification preferences',
            ));
    }
}
