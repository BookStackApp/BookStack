<?php

namespace Tests\User;

use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Activity\WatchLevels;
use Tests\TestCase;

class UserMyAccountTest extends TestCase
{
    public function test_index_view()
    {
        $resp = $this->asEditor()->get('/my-account');
        $resp->assertRedirect('/my-account/profile');
    }

    public function test_views_not_accessible_to_guest_user()
    {
        $categories = ['profile', 'auth', 'shortcuts', 'notifications', ''];
        $this->setSettings(['app-public' => 'true']);

        $this->permissions->grantUserRolePermissions($this->users->guest(), ['receive-notifications']);

        foreach ($categories as $category) {
            $resp = $this->get('/my-account/' . $category);
            $resp->assertRedirect('/');
        }
    }
    public function test_interface_shortcuts_updating()
    {
        $this->asEditor();

        // View preferences with defaults
        $resp = $this->get('/my-account/shortcuts');
        $resp->assertSee('UI Shortcut Preferences');

        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('enabled', 'false');
        $html->assertFieldHasValue('shortcut[home_view]', '1');

        // Update preferences
        $resp = $this->put('/my-account/shortcuts', [
            'enabled' => 'true',
            'shortcut' => ['home_view' => 'Ctrl + 1'],
        ]);

        $resp->assertRedirect('/my-account/shortcuts');
        $resp->assertSessionHas('success', 'Shortcut preferences have been updated!');

        // View updates to preferences page
        $resp = $this->get('/my-account/shortcuts');
        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('enabled', 'true');
        $html->assertFieldHasValue('shortcut[home_view]', 'Ctrl + 1');
    }

    public function test_body_has_shortcuts_component_when_active()
    {
        $editor = $this->users->editor();
        $this->actingAs($editor);

        $this->withHtml($this->get('/'))->assertElementNotExists('body[component="shortcuts"]');

        setting()->putUser($editor, 'ui-shortcuts-enabled', 'true');
        $this->withHtml($this->get('/'))->assertElementExists('body[component="shortcuts"]');
    }

    public function test_notification_routes_requires_notification_permission()
    {
        $viewer = $this->users->viewer();
        $resp = $this->actingAs($viewer)->get('/my-account/notifications');
        $this->assertPermissionError($resp);

        $resp = $this->actingAs($viewer)->get('/my-account/profile');
        $resp->assertDontSeeText('Notification Preferences');

        $resp = $this->put('/my-account/notifications');
        $this->assertPermissionError($resp);

        $this->permissions->grantUserRolePermissions($viewer, ['receive-notifications']);
        $resp = $this->get('/my-account/notifications');
        $resp->assertOk();
        $resp->assertSee('Notification Preferences');
    }

    public function test_notification_preferences_updating()
    {
        $editor = $this->users->editor();

        // View preferences with defaults
        $resp = $this->actingAs($editor)->get('/my-account/notifications');
        $resp->assertSee('Notification Preferences');

        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('preferences[comment-replies]', 'false');

        // Update preferences
        $resp = $this->put('/my-account/notifications', [
            'preferences' => ['comment-replies' => 'true'],
        ]);

        $resp->assertRedirect('/my-account/notifications');
        $resp->assertSessionHas('success', 'Notification preferences have been updated!');

        // View updates to preferences page
        $resp = $this->get('/my-account/notifications');
        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('preferences[comment-replies]', 'true');
    }

    public function test_notification_preferences_show_watches()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();

        $options = new UserEntityWatchOptions($editor, $book);
        $options->updateLevelByValue(WatchLevels::COMMENTS);

        $resp = $this->actingAs($editor)->get('/my-account/notifications');
        $resp->assertSee($book->name);
        $resp->assertSee('All Page Updates & Comments');

        $options->updateLevelByValue(WatchLevels::DEFAULT);

        $resp = $this->actingAs($editor)->get('/my-account/notifications');
        $resp->assertDontSee($book->name);
        $resp->assertDontSee('All Page Updates & Comments');
    }

    public function test_notification_preferences_dont_error_on_deleted_items()
    {
        $editor = $this->users->editor();
        $book = $this->entities->book();

        $options = new UserEntityWatchOptions($editor, $book);
        $options->updateLevelByValue(WatchLevels::COMMENTS);

        $this->actingAs($editor)->delete($book->getUrl());
        $book->refresh();
        $this->assertNotNull($book->deleted_at);

        $resp = $this->actingAs($editor)->get('/my-account/notifications');
        $resp->assertOk();
        $resp->assertDontSee($book->name);
    }

    public function test_notification_preferences_not_accessible_to_guest()
    {
        $this->setSettings(['app-public' => 'true']);
        $guest = $this->users->guest();
        $this->permissions->grantUserRolePermissions($guest, ['receive-notifications']);

        $resp = $this->get('/my-account/notifications');
        $this->assertPermissionError($resp);

        $resp = $this->put('/my-account/notifications', [
            'preferences' => ['comment-replies' => 'true'],
        ]);
        $this->assertPermissionError($resp);
    }

    public function test_notification_comment_options_only_exist_if_comments_active()
    {
        $resp = $this->asEditor()->get('/my-account/notifications');
        $resp->assertSee('Notify upon comments');
        $resp->assertSee('Notify upon replies');

        setting()->put('app-disable-comments', true);

        $resp = $this->get('/my-account/notifications');
        $resp->assertDontSee('Notify upon comments');
        $resp->assertDontSee('Notify upon replies');
    }
}
