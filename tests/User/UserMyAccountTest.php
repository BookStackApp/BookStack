<?php

namespace Tests\User;

use BookStack\Access\Mfa\MfaValue;
use BookStack\Activity\Tools\UserEntityWatchOptions;
use BookStack\Activity\WatchLevels;
use BookStack\Api\ApiToken;
use BookStack\Uploads\Image;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
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

    public function test_profile_updating()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/my-account/profile');
        $resp->assertSee('Profile Details');

        $html = $this->withHtml($resp);
        $html->assertFieldHasValue('name', $editor->name);
        $html->assertFieldHasValue('email', $editor->email);

        $resp = $this->put('/my-account/profile', [
            'name' => 'Barryius',
            'email' => 'barryius@example.com',
            'language' => 'fr',
        ]);

        $resp->assertRedirect('/my-account/profile');
        $this->assertDatabaseHas('users', [
            'name' => 'Barryius',
            'email' => $editor->email, // No email change due to not having permissions
        ]);
        $this->assertEquals(setting()->getUser($editor, 'language'), 'fr');
    }

    public function test_profile_user_avatar_update_and_reset()
    {
        $user = $this->users->viewer();
        $avatarFile = $this->files->uploadedImage('avatar-icon.png');

        $this->assertEquals(0, $user->image_id);

        $upload = $this->actingAs($user)->call('PUT', "/my-account/profile", [
            'name' => 'Barry Scott',
        ], [], ['profile_image' => $avatarFile], []);
        $upload->assertRedirect('/my-account/profile');


        $user->refresh();
        $this->assertNotEquals(0, $user->image_id);
        /** @var Image $image */
        $image = Image::query()->findOrFail($user->image_id);
        $this->assertFileExists(public_path($image->path));

        $reset = $this->put("/my-account/profile", [
            'name' => 'Barry Scott',
            'profile_image_reset' => 'true',
        ]);
        $upload->assertRedirect('/my-account/profile');

        $user->refresh();
        $this->assertFileDoesNotExist(public_path($image->path));
        $this->assertEquals(0, $user->image_id);
    }

    public function test_profile_admin_options_link_shows_if_permissions_allow()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/my-account/profile');
        $resp->assertDontSee('Administrator Options');
        $this->withHtml($resp)->assertLinkNotExists(url("/settings/users/{$editor->id}"));

        $this->permissions->grantUserRolePermissions($editor, ['users-manage']);

        $resp = $this->actingAs($editor)->get('/my-account/profile');
        $resp->assertSee('Administrator Options');
        $this->withHtml($resp)->assertLinkExists(url("/settings/users/{$editor->id}"));
    }

    public function test_profile_self_delete()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/my-account/profile');
        $this->withHtml($resp)->assertLinkExists(url('/my-account/delete'), 'Delete Account');

        $resp = $this->get('/my-account/delete');
        $resp->assertSee('Delete My Account');
        $this->withHtml($resp)->assertElementContains('form[action$="/my-account"] button', 'Confirm');

        $resp = $this->delete('/my-account');
        $resp->assertRedirect('/');

        $this->assertDatabaseMissing('users', ['id' => $editor->id]);
    }

    public function test_profile_self_delete_shows_ownership_migration_if_can_manage_users()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/my-account/delete');
        $resp->assertDontSee('Migrate Ownership');

        $this->permissions->grantUserRolePermissions($editor, ['users-manage']);

        $resp = $this->actingAs($editor)->get('/my-account/delete');
        $resp->assertSee('Migrate Ownership');
    }

    public function test_auth_password_change()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/my-account/auth');
        $resp->assertSee('Change Password');
        $this->withHtml($resp)->assertElementExists('form[action$="/my-account/auth/password"]');

        $password = Str::random();
        $resp = $this->put('/my-account/auth/password', [
            'password' => $password,
            'password-confirm' => $password,
        ]);
        $resp->assertRedirect('/my-account/auth');

        $editor->refresh();
        $this->assertTrue(Hash::check($password, $editor->password));
    }

    public function test_auth_password_change_hides_if_not_using_email_auth()
    {
        $editor = $this->users->editor();

        $resp = $this->actingAs($editor)->get('/my-account/auth');
        $resp->assertSee('Change Password');

        config()->set('auth.method', 'oidc');

        $resp = $this->actingAs($editor)->get('/my-account/auth');
        $resp->assertDontSee('Change Password');
    }

    public function test_auth_page_has_mfa_links()
    {
        $editor = $this->users->editor();
        $resp = $this->actingAs($editor)->get('/my-account/auth');
        $resp->assertSee('0 methods configured');
        $this->withHtml($resp)->assertLinkExists(url('/mfa/setup'));

        MfaValue::upsertWithValue($editor, 'totp', 'testval');

        $resp = $this->get('/my-account/auth');
        $resp->assertSee('1 method configured');
    }

    public function test_auth_page_api_tokens()
    {
        $editor = $this->users->editor();
        $resp = $this->actingAs($editor)->get('/my-account/auth');
        $resp->assertSee('API Tokens');
        $this->withHtml($resp)->assertLinkExists(url("/api-tokens/{$editor->id}/create?context=my-account"));

        ApiToken::factory()->create(['user_id' => $editor->id, 'name' => 'My great token']);
        $editor->unsetRelations();

        $resp = $this->get('/my-account/auth');
        $resp->assertSee('My great token');
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
