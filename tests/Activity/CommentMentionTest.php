<?php

namespace Tests\Activity;

use BookStack\Activity\Notifications\Messages\CommentMentionNotification;
use BookStack\Permissions\Permission;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class CommentMentionTest extends TestCase
{
    public function test_mentions_are_notified()
    {
        $userToMention = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($userToMention, [Permission::ReceiveNotifications]);
        $editor = $this->users->editor();
        $page = $this->entities->pageWithinChapter();
        $notifications = Notification::fake();

        $this->actingAs($editor)->post("/comment/{$page->id}", [
            'html' => '<p>Hello <a data-mention-user-id="' . $userToMention->id . '">@user</a></p>'
        ])->assertOk();

        $notifications->assertSentTo($userToMention, function (CommentMentionNotification $notification) use ($userToMention, $editor, $page) {
            $mail = $notification->toMail($userToMention);
            $mailContent = html_entity_decode(strip_tags($mail->render()), ENT_QUOTES);
            $subjectPrefix = 'You have been mentioned in a comment on page: ' . mb_substr($page->name, 0, 20);
            return str_starts_with($mail->subject, $subjectPrefix)
                && str_contains($mailContent, 'View Comment')
                && str_contains($mailContent, 'Page Name: ' . $page->name)
                && str_contains($mailContent, 'Page Path: ' . $page->book->getShortName(24) . ' > ' . $page->chapter->getShortName(24))
                && str_contains($mailContent, 'Commenter: ' . $editor->name)
                && str_contains($mailContent, 'Comment: Hello @user');
        });
    }

    public function test_mentions_are_not_notified_if_mentioned_by_same_user()
    {
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($editor, [Permission::ReceiveNotifications]);
        $page = $this->entities->page();
        $notifications = Notification::fake();

        $this->actingAs($editor)->post("/comment/{$page->id}", [
            'html' => '<p>Hello <a data-mention-user-id="' . $editor->id . '"></a></p>'
        ])->assertOk();

        $notifications->assertNothingSent();
    }

    public function test_mentions_are_logged_to_the_database_even_if_not_notified()
    {
        $editor = $this->users->editor();
        $otherUser = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($editor, [Permission::ReceiveNotifications]);
        $page = $this->entities->page();
        $notifications = Notification::fake();

        $this->actingAs($editor)->post("/comment/{$page->id}", [
            'html' => '<p>Hello <a data-mention-user-id="' . $editor->id . '"></a> and <a data-mention-user-id="' . $otherUser->id . '"></a></p>'
        ])->assertOk();

        $notifications->assertNothingSent();

        $comment = $page->comments()->latest()->first();
        $this->assertDatabaseHas('mention_history', [
            'mentionable_id' => $comment->id,
            'mentionable_type' => 'comment',
            'from_user_id' => $editor->id,
            'to_user_id' => $otherUser->id,
        ]);
        $this->assertDatabaseHas('mention_history', [
            'mentionable_id' => $comment->id,
            'mentionable_type' => 'comment',
            'from_user_id' => $editor->id,
            'to_user_id' => $editor->id,
        ]);
    }

    public function test_comment_updates_will_send_notifications_only_if_mention_is_new()
    {
        $userToMention = $this->users->viewer();
        $this->permissions->grantUserRolePermissions($userToMention, [Permission::ReceiveNotifications]);
        $editor = $this->users->editor();
        $this->permissions->grantUserRolePermissions($editor, [Permission::CommentUpdateOwn]);
        $page = $this->entities->page();
        $notifications = Notification::fake();

        $this->actingAs($editor)->post("/comment/{$page->id}", [
            'html' => '<p>Hello there</p>'
        ])->assertOk();
        $comment = $page->comments()->latest()->first();

        $notifications->assertNothingSent();

        $this->put("/comment/{$comment->id}", [
            'html' => '<p>Hello <a data-mention-user-id="' . $userToMention->id . '"></a></p>'
        ])->assertOk();

        $notifications->assertSentTo($userToMention, CommentMentionNotification::class);
        $notifications->assertCount(1);

        $this->put("/comment/{$comment->id}", [
            'html' => '<p>Hello again<a data-mention-user-id="' . $userToMention->id . '"></a></p>'
        ])->assertOk();

        $notifications->assertCount(1);
    }

    public function test_notification_limited_to_those_with_view_permissions()
    {
        $userA = $this->users->newUser();
        $userB = $this->users->newUser();
        $this->permissions->grantUserRolePermissions($userA, [Permission::ReceiveNotifications]);
        $this->permissions->grantUserRolePermissions($userB, [Permission::ReceiveNotifications]);
        $notifications = Notification::fake();
        $page = $this->entities->page();

        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, ['view'], $userA->roles()->first());

        $this->asAdmin()->post("/comment/{$page->id}", [
            'html' => '<p>Hello <a data-mention-user-id="' . $userA->id . '"></a> and <a data-mention-user-id="' . $userB->id . '"></a></p>'
        ])->assertOk();

        $notifications->assertCount(1);
        $notifications->assertSentTo($userA, CommentMentionNotification::class);
    }
}
