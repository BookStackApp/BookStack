<?php

namespace Tests\Activity;

use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Comment;
use BookStack\Activity\Notifications\Messages\BaseActivityNotification;
use BookStack\Activity\Notifications\Messages\CommentCreationNotification;
use BookStack\Activity\Notifications\Messages\PageCreationNotification;
use BookStack\Activity\Notifications\Messages\PageUpdateNotification;
use BookStack\Activity\Tools\ActivityLogger;
use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Activity\WatchLevels;
use BookStack\Entities\Models\Entity;
use BookStack\Settings\UserNotificationPreferences;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class WatchTest extends TestCase
{
    public function test_watch_action_exists_on_entity_unless_active()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $entities = [$this->entities->book(), $this->entities->chapter(), $this->entities->page()];
        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementContains('form[action$="/watching/update"] button.icon-list-item', 'Watch');

            $watchOptions = new UserEntityWatchOptions($editor, $entity);
            $watchOptions->updateLevelByValue(WatchLevels::COMMENTS);

            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementNotExists('form[action$="/watching/update"] button.icon-list-item');
        }
    }

    public function test_watch_action_only_shows_with_permission()
    {
        $viewer = $this->users->viewer();
        $this->actingAs($viewer);

        $entities = [$this->entities->book(), $this->entities->chapter(), $this->entities->page()];
        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementNotExists('form[action$="/watching/update"] button.icon-list-item');
        }

        $this->permissions->grantUserRolePermissions($viewer, ['receive-notifications']);

        /** @var Entity $entity */
        foreach ($entities as $entity) {
            $resp = $this->get($entity->getUrl());
            $this->withHtml($resp)->assertElementExists('form[action$="/watching/update"] button.icon-list-item');
        }
    }

    public function test_watch_update()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();

        $this->actingAs($editor)->get($book->getUrl());
        $resp = $this->put('/watching/update', [
            'type' => get_class($book),
            'id' => $book->id,
            'level' => 'comments'
        ]);

        $resp->assertRedirect($book->getUrl());
        $this->assertSessionHas('success');
        $this->assertDatabaseHas('watches', [
            'watchable_id' => $book->id,
            'watchable_type' => $book->getMorphClass(),
            'user_id' => $editor->id,
            'level' => WatchLevels::COMMENTS,
        ]);

        $resp = $this->put('/watching/update', [
            'type' => get_class($book),
            'id' => $book->id,
            'level' => 'default'
        ]);
        $resp->assertRedirect($book->getUrl());
        $this->assertDatabaseMissing('watches', [
            'watchable_id' => $book->id,
            'watchable_type' => $book->getMorphClass(),
            'user_id' => $editor->id,
        ]);
    }

    public function test_watch_update_fails_for_guest()
    {
        $this->setSettings(['app-public' => 'true']);
        $guest = $this->users->guest();
        $this->permissions->grantUserRolePermissions($guest, ['receive-notifications']);
        $book = $this->entities->book();

        $resp = $this->put('/watching/update', [
            'type' => get_class($book),
            'id' => $book->id,
            'level' => 'comments'
        ]);

        $this->assertPermissionError($resp);
        $guest->unsetRelations();
    }

    public function test_watch_detail_display_reflects_state()
    {
        $editor = $this->users->editor();
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $page = $chapter->pages()->first();

        (new UserEntityWatchOptions($editor, $book))->updateLevelByValue(WatchLevels::UPDATES);

        $this->actingAs($editor)->get($book->getUrl())->assertSee('Watching new pages and updates');
        $this->get($chapter->getUrl())->assertSee('Watching via parent book');
        $this->get($page->getUrl())->assertSee('Watching via parent book');

        (new UserEntityWatchOptions($editor, $chapter))->updateLevelByValue(WatchLevels::COMMENTS);
        $this->get($chapter->getUrl())->assertSee('Watching new pages, updates & comments');
        $this->get($page->getUrl())->assertSee('Watching via parent chapter');

        (new UserEntityWatchOptions($editor, $page))->updateLevelByValue(WatchLevels::UPDATES);
        $this->get($page->getUrl())->assertSee('Watching new pages and updates');
    }

    public function test_watch_detail_ignore_indicator_cascades()
    {
        $editor = $this->users->editor();
        $book = $this->entities->bookHasChaptersAndPages();
        (new UserEntityWatchOptions($editor, $book))->updateLevelByValue(WatchLevels::IGNORE);

        $this->actingAs($editor)->get($book->getUrl())->assertSee('Ignoring notifications');
        $this->get($book->chapters()->first()->getUrl())->assertSee('Ignoring via parent book');
        $this->get($book->pages()->first()->getUrl())->assertSee('Ignoring via parent book');
    }

    public function test_watch_option_menu_shows_current_active_state()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();
        $options = new UserEntityWatchOptions($editor, $book);

        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementNotExists('form[action$="/watching/update"] svg[data-icon="check-circle"]');

        $options->updateLevelByValue(WatchLevels::COMMENTS);
        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[value="comments"] svg[data-icon="check-circle"]');

        $options->updateLevelByValue(WatchLevels::IGNORE);
        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[value="ignore"] svg[data-icon="check-circle"]');
    }

    public function test_watch_option_menu_limits_options_for_pages()
    {
        $editor = $this->users->editor();
        $book = $this->entities->bookHasChaptersAndPages();
        (new UserEntityWatchOptions($editor, $book))->updateLevelByValue(WatchLevels::IGNORE);

        $respHtml = $this->withHtml($this->actingAs($editor)->get($book->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[name="level"][value="new"]');

        $respHtml = $this->withHtml($this->get($book->pages()->first()->getUrl()));
        $respHtml->assertElementExists('form[action$="/watching/update"] button[name="level"][value="updates"]');
        $respHtml->assertElementNotExists('form[action$="/watching/update"] button[name="level"][value="new"]');
    }

    public function test_notify_own_page_changes()
    {
        $editor = $this->users->editor();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $prefs = new UserNotificationPreferences($editor);
        $prefs->updateFromSettingsArray(['own-page-changes' => 'true']);

        $notifications = Notification::fake();

        $this->asAdmin();
        $this->entities->updatePage($entities['page'], ['name' => 'My updated page', 'html' => 'Hello']);
        $notifications->assertSentTo($editor, PageUpdateNotification::class);
    }

    public function test_notify_own_page_comments()
    {
        $editor = $this->users->editor();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $prefs = new UserNotificationPreferences($editor);
        $prefs->updateFromSettingsArray(['own-page-comments' => 'true']);

        $notifications = Notification::fake();

        $this->asAdmin()->post("/comment/{$entities['page']->id}", [
            'text' => 'My new comment'
        ]);
        $notifications->assertSentTo($editor, CommentCreationNotification::class);
    }

    public function test_notify_comment_replies()
    {
        $editor = $this->users->editor();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $prefs = new UserNotificationPreferences($editor);
        $prefs->updateFromSettingsArray(['comment-replies' => 'true']);

        $notifications = Notification::fake();

        $this->actingAs($editor)->post("/comment/{$entities['page']->id}", [
            'text' => 'My new comment'
        ]);
        $comment = $entities['page']->comments()->first();

        $this->asAdmin()->post("/comment/{$entities['page']->id}", [
            'text' => 'My new comment response',
            'parent_id' => $comment->id,
        ]);
        $notifications->assertSentTo($editor, CommentCreationNotification::class);
    }

    public function test_notify_watch_parent_book_ignore()
    {
        $editor = $this->users->editor();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $watches = new UserEntityWatchOptions($editor, $entities['book']);
        $prefs = new UserNotificationPreferences($editor);
        $watches->updateLevelByValue(WatchLevels::IGNORE);
        $prefs->updateFromSettingsArray(['own-page-changes' => 'true', 'own-page-comments' => true]);

        $notifications = Notification::fake();

        $this->asAdmin()->post("/comment/{$entities['page']->id}", [
            'text' => 'My new comment response',
        ]);
        $this->entities->updatePage($entities['page'], ['name' => 'My updated page', 'html' => 'Hello']);
        $notifications->assertNothingSent();
    }

    public function test_notify_watch_parent_book_comments()
    {
        $notifications = Notification::fake();
        $editor = $this->users->editor();
        $admin = $this->users->admin();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $watches = new UserEntityWatchOptions($editor, $entities['book']);
        $watches->updateLevelByValue(WatchLevels::COMMENTS);

        // Comment post
        $this->actingAs($admin)->post("/comment/{$entities['page']->id}", [
            'text' => 'My new comment response',
        ]);

        $notifications->assertSentTo($editor, function (CommentCreationNotification $notification) use ($editor, $admin, $entities) {
            $mail = $notification->toMail($editor);
            $mailContent = html_entity_decode(strip_tags($mail->render()));
            return $mail->subject === 'New comment on page: ' . $entities['page']->getShortName()
                && str_contains($mailContent, 'View Comment')
                && str_contains($mailContent, 'Page Name: ' . $entities['page']->name)
                && str_contains($mailContent, 'Commenter: ' . $admin->name)
                && str_contains($mailContent, 'Comment: My new comment response');
        });
    }

    public function test_notify_watch_parent_book_updates()
    {
        $notifications = Notification::fake();
        $editor = $this->users->editor();
        $admin = $this->users->admin();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $watches = new UserEntityWatchOptions($editor, $entities['book']);
        $watches->updateLevelByValue(WatchLevels::UPDATES);

        $this->actingAs($admin);
        $this->entities->updatePage($entities['page'], ['name' => 'Updated page', 'html' => 'new page content']);

        $notifications->assertSentTo($editor, function (PageUpdateNotification $notification) use ($editor, $admin) {
            $mail = $notification->toMail($editor);
            $mailContent = html_entity_decode(strip_tags($mail->render()));
            return $mail->subject === 'Updated page: Updated page'
                && str_contains($mailContent, 'View Page')
                && str_contains($mailContent, 'Page Name: Updated page')
                && str_contains($mailContent, 'Updated By: ' . $admin->name)
                && str_contains($mailContent, 'you won\'t be sent notifications for further edits to this page by the same editor');
        });

        // Test debounce
        $notifications = Notification::fake();
        $this->entities->updatePage($entities['page'], ['name' => 'Updated page', 'html' => 'new page content']);
        $notifications->assertNothingSentTo($editor);
    }

    public function test_notify_watch_parent_book_new()
    {
        $notifications = Notification::fake();
        $editor = $this->users->editor();
        $admin = $this->users->admin();
        $entities = $this->entities->createChainBelongingToUser($editor);
        $watches = new UserEntityWatchOptions($editor, $entities['book']);
        $watches->updateLevelByValue(WatchLevels::NEW);

        $this->actingAs($admin)->get($entities['chapter']->getUrl('/create-page'));
        $page = $entities['chapter']->pages()->where('draft', '=', true)->first();
        $this->post($page->getUrl(), ['name' => 'My new page', 'html' => 'My new page content']);

        $notifications->assertSentTo($editor, function (PageCreationNotification $notification) use ($editor, $admin) {
            $mail = $notification->toMail($editor);
            $mailContent = html_entity_decode(strip_tags($mail->render()));
            return $mail->subject === 'New page: My new page'
                && str_contains($mailContent, 'View Page')
                && str_contains($mailContent, 'Page Name: My new page')
                && str_contains($mailContent, 'Created By: ' . $admin->name);
        });
    }

    public function test_notifications_sent_in_right_language()
    {
        $editor = $this->users->editor();
        $admin = $this->users->admin();
        setting()->putUser($editor, 'language', 'de');
        $entities = $this->entities->createChainBelongingToUser($editor);
        $watches = new UserEntityWatchOptions($editor, $entities['book']);
        $watches->updateLevelByValue(WatchLevels::COMMENTS);

        $activities = [
            ActivityType::PAGE_CREATE => $entities['page'],
            ActivityType::PAGE_UPDATE => $entities['page'],
            ActivityType::COMMENT_CREATE => (new Comment([]))->forceFill(['entity_id' => $entities['page']->id, 'entity_type' => $entities['page']->getMorphClass()]),
        ];

        $notifications = Notification::fake();
        $logger = app()->make(ActivityLogger::class);
        $this->actingAs($admin);

        foreach ($activities as $activityType => $detail) {
            $logger->add($activityType, $detail);
        }

        $sent = $notifications->sentNotifications()[get_class($editor)][$editor->id];
        $this->assertCount(3, $sent);

        foreach ($sent as $notificationInfo) {
            $notification = $notificationInfo[0]['notification'];
            $this->assertInstanceOf(BaseActivityNotification::class, $notification);
            $mail = $notification->toMail($editor);
            $mailContent = html_entity_decode(strip_tags($mail->render()));
            $this->assertStringContainsString('Name der Seite:', $mailContent);
            $this->assertStringContainsString('Diese Benachrichtigung wurde', $mailContent);
            $this->assertStringContainsString('Sollte es beim Anklicken der Schaltfläche', $mailContent);
        }
    }

    public function test_notifications_not_sent_if_lacking_view_permission_for_related_item()
    {
        $notifications = Notification::fake();
        $editor = $this->users->editor();
        $page = $this->entities->page();

        $watches = new UserEntityWatchOptions($editor, $page);
        $watches->updateLevelByValue(WatchLevels::COMMENTS);
        $this->permissions->disableEntityInheritedPermissions($page);

        $this->asAdmin()->post("/comment/{$page->id}", [
            'text' => 'My new comment response',
        ])->assertOk();

        $notifications->assertNothingSentTo($editor);
    }
}
