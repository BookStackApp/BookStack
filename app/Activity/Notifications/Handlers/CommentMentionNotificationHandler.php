<?php

namespace BookStack\Activity\Notifications\Handlers;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Activity;
use BookStack\Activity\Models\Comment;
use BookStack\Activity\Models\Loggable;
use BookStack\Activity\Models\MentionHistory;
use BookStack\Activity\Notifications\Messages\CommentMentionNotification;
use BookStack\Activity\Tools\MentionParser;
use BookStack\Entities\Models\Page;
use BookStack\Settings\UserNotificationPreferences;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class CommentMentionNotificationHandler extends BaseNotificationHandler
{
    public function handle(Activity $activity, Loggable|string $detail, User $user): void
    {
        if (!($detail instanceof Comment) || !($detail->entity instanceof Page)) {
            throw new \InvalidArgumentException("Detail for comment mention notifications must be a comment on a page");
        }

        /** @var Page $page */
        $page = $detail->entity;

        $parser = new MentionParser();
        $mentionedUserIds = $parser->parseUserIdsFromHtml($detail->html);
        $realMentionedUsers = User::whereIn('id', $mentionedUserIds)->get();

        $receivingNotifications = $realMentionedUsers->filter(function (User $user) {
            $prefs = new UserNotificationPreferences($user);
            return $prefs->notifyOnCommentMentions();
        });
        $receivingNotificationsUserIds = $receivingNotifications->pluck('id')->toArray();

        $userMentionsToLog = $realMentionedUsers;

        // When an edit, we check our history to see if we've already notified the user about this comment before
        // so that we can filter them out to avoid double notifications.
        if ($activity->type === ActivityType::COMMENT_UPDATE) {
            $previouslyNotifiedUserIds = $this->getPreviouslyNotifiedUserIds($detail);
            $receivingNotificationsUserIds = array_values(array_diff($receivingNotificationsUserIds, $previouslyNotifiedUserIds));
            $userMentionsToLog = $userMentionsToLog->filter(function (User $user) use ($previouslyNotifiedUserIds) {
                return !in_array($user->id, $previouslyNotifiedUserIds);
            });
        }

        $this->logMentions($userMentionsToLog, $detail, $user);
        $this->sendNotificationToUserIds(CommentMentionNotification::class, $receivingNotificationsUserIds, $user, $detail, $page);
    }

    /**
     * @param Collection<User> $mentionedUsers
     */
    protected function logMentions(Collection $mentionedUsers, Comment $comment, User $fromUser): void
    {
        $mentions = [];
        $now = Carbon::now();

        foreach ($mentionedUsers as $mentionedUser) {
            $mentions[] = [
                'mentionable_type' => $comment->getMorphClass(),
                'mentionable_id' => $comment->id,
                'from_user_id' => $fromUser->id,
                'to_user_id' => $mentionedUser->id,
                'created_at' => $now,
                'updated_at' => $now,
            ];
        }

        MentionHistory::query()->insert($mentions);
    }

    protected function getPreviouslyNotifiedUserIds(Comment $comment): array
    {
        return MentionHistory::query()
            ->where('mentionable_id', $comment->id)
            ->where('mentionable_type', $comment->getMorphClass())
            ->pluck('to_user_id')
            ->toArray();
    }
}
