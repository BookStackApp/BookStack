<?php

namespace Tests\User;

use BookStack\Access\Mfa\MfaValue;
use BookStack\Access\SocialAccount;
use BookStack\Access\UserInviteException;
use BookStack\Access\UserInviteService;
use BookStack\Activity\ActivityType;
use BookStack\Activity\Models\Activity;
use BookStack\Activity\Models\Comment;
use BookStack\Activity\Models\Favourite;
use BookStack\Activity\Models\View;
use BookStack\Activity\Models\Watch;
use BookStack\Api\ApiToken;
use BookStack\Entities\Models\Deletion;
use BookStack\Entities\Models\PageRevision;
use BookStack\Exports\Import;
use BookStack\Uploads\Attachment;
use BookStack\Uploads\Image;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_user_creation()
    {
        /** @var User $user */
        $user = User::factory()->make();
        $adminRole = Role::getRole('admin');

        $resp = $this->asAdmin()->get('/settings/users');
        $this->withHtml($resp)->assertElementContains('a[href="' . url('/settings/users/create') . '"]', 'Add New User');

        $resp = $this->get('/settings/users/create');
        $this->withHtml($resp)->assertElementContains('form[action="' . url('/settings/users/create') . '"]', 'Save');

        $resp = $this->post('/settings/users/create', [
            'name' => $user->name,
            'email' => $user->email,
            'password' => $user->password,
            'password-confirm' => $user->password,
            'roles[' . $adminRole->id . ']' => 'true',
        ]);
        $resp->assertRedirect('/settings/users');

        $resp = $this->get('/settings/users');
        $resp->assertSee($user->name);

        $this->assertDatabaseHas('users', $user->only('name', 'email'));

        $user->refresh();
        $this->assertStringStartsWith(Str::slug($user->name), $user->slug);
    }

    public function test_user_updating()
    {
        $user = $this->users->viewer();
        $password = $user->password;

        $resp = $this->asAdmin()->get('/settings/users/' . $user->id);
        $resp->assertSee($user->email);

        $this->put($user->getEditUrl(), [
            'name' => 'Barry Scott',
        ])->assertRedirect('/settings/users');

        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Barry Scott', 'password' => $password]);
        $this->assertDatabaseMissing('users', ['name' => $user->name]);

        $user->refresh();
        $this->assertStringStartsWith(Str::slug($user->name), $user->slug);
    }

    public function test_user_password_update()
    {
        $user = $this->users->viewer();
        $userProfilePage = '/settings/users/' . $user->id;

        $this->asAdmin()->get($userProfilePage);
        $this->put($userProfilePage, [
            'password' => 'newpassword',
        ])->assertRedirect($userProfilePage);

        $this->get($userProfilePage)->assertSee('Password confirmation required');

        $this->put($userProfilePage, [
            'password' => 'newpassword',
            'password-confirm' => 'newpassword',
        ])->assertRedirect('/settings/users');

        $userPassword = User::query()->find($user->id)->password;
        $this->assertTrue(Hash::check('newpassword', $userPassword));
    }

    public function test_user_can_be_updated_with_single_char_name()
    {
        $user = $this->users->viewer();
        $this->asAdmin()->put("/settings/users/{$user->id}", [
            'name' => 'b'
        ])->assertRedirect('/settings/users');

        $this->assertEquals('b', $user->refresh()->name);
    }

    public function test_user_cannot_be_deleted_if_last_admin()
    {
        $adminRole = Role::getRole('admin');

        // Delete all but one admin user if there are more than one
        $adminUsers = $adminRole->users;
        if (count($adminUsers) > 1) {
            /** @var User $user */
            foreach ($adminUsers->splice(1) as $user) {
                $user->delete();
            }
        }

        // Ensure we currently only have 1 admin user
        $this->assertEquals(1, $adminRole->users()->count());
        /** @var User $user */
        $user = $adminRole->users->first();

        $resp = $this->asAdmin()->delete('/settings/users/' . $user->id);
        $resp->assertRedirect('/settings/users/' . $user->id);

        $resp = $this->get('/settings/users/' . $user->id);
        $resp->assertSee('You cannot delete the only admin');

        $this->assertDatabaseHas('users', ['id' => $user->id]);
    }

    public function test_delete()
    {
        $editor = $this->users->editor();
        $resp = $this->asAdmin()->delete("settings/users/{$editor->id}");
        $resp->assertRedirect('/settings/users');
        $resp = $this->followRedirects($resp);

        $resp->assertSee('User successfully removed');
        $this->assertActivityExists(ActivityType::USER_DELETE);

        $this->assertDatabaseMissing('users', ['id' => $editor->id]);
    }

    public function test_delete_offers_migrate_option()
    {
        $editor = $this->users->editor();
        $resp = $this->asAdmin()->get("settings/users/{$editor->id}/delete");
        $resp->assertSee('Migrate Ownership');
        $resp->assertSee('new_owner_id');
    }

    public function test_migrate_option_hidden_if_user_cannot_manage_users()
    {
        $editor = $this->users->editor();

        $resp = $this->asEditor()->get("settings/users/{$editor->id}/delete");
        $resp->assertDontSee('Migrate Ownership');
        $resp->assertDontSee('new_owner_id');

        $this->permissions->grantUserRolePermissions($editor, ['users-manage']);

        $resp = $this->asEditor()->get("settings/users/{$editor->id}/delete");
        $resp->assertSee('Migrate Ownership');
        $this->withHtml($resp)->assertElementExists('form input[name="new_owner_id"]');
        $resp->assertSee('new_owner_id');
    }

    public function test_delete_with_new_owner_id_changes_ownership()
    {
        $page = $this->entities->page();
        $owner = $page->ownedBy;
        $newOwner = User::query()->where('id', '!=', $owner->id)->first();

        $this->asAdmin()->delete("settings/users/{$owner->id}", ['new_owner_id' => $newOwner->id])->assertRedirect();
        $this->assertDatabaseHasEntityData('page', [
            'id' => $page->id,
            'owned_by' => $newOwner->id,
        ]);
    }

    public function test_delete_with_empty_owner_migration_id_works()
    {
        $user = $this->users->editor();

        $resp = $this->asAdmin()->delete("settings/users/{$user->id}", ['new_owner_id' => '']);
        $resp->assertRedirect('/settings/users');
        $this->assertActivityExists(ActivityType::USER_DELETE);
        $this->assertSessionHas('success');
    }

    public function test_delete_with_empty_owner_migration_id_clears_relevant_id_uses()
    {
        $user = $this->users->editor();
        $page = $this->entities->page();
        $this->actingAs($user);

        // Create relations
        $activity = Activity::factory()->create(['user_id' => $user->id]);
        $attachment = Attachment::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        $comment = Comment::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        $deletion = Deletion::factory()->create(['deleted_by' => $user->id]);
        $page->forceFill(['owned_by' => $user->id, 'created_by' => $user->id, 'updated_by' => $user->id])->save();
        $page->rebuildPermissions();
        $image = Image::factory()->create(['created_by' => $user->id, 'updated_by' => $user->id]);
        $import = Import::factory()->create(['created_by' => $user->id]);
        $revision = PageRevision::factory()->create(['created_by' => $user->id]);

        $apiToken = ApiToken::factory()->create(['user_id' => $user->id]);
        \DB::table('email_confirmations')->insert(['user_id' => $user->id, 'token' => 'abc123']);
        $favourite = Favourite::factory()->create(['user_id' => $user->id]);
        $mfaValue = MfaValue::factory()->create(['user_id' => $user->id]);
        $socialAccount = SocialAccount::factory()->create(['user_id' => $user->id]);
        \DB::table('user_invites')->insert(['user_id' => $user->id, 'token' => 'abc123']);
        View::incrementFor($page);
        $watch = Watch::factory()->create(['user_id' => $user->id]);

        $userColumnsByTable = [
            'api_tokens' => ['user_id'],
            'attachments' => ['created_by', 'updated_by'],
            'comments' => ['created_by', 'updated_by'],
            'deletions' => ['deleted_by'],
            'email_confirmations' => ['user_id'],
            'entities' => ['created_by', 'updated_by', 'owned_by'],
            'favourites' => ['user_id'],
            'images' => ['created_by', 'updated_by'],
            'imports' => ['created_by'],
            'joint_permissions' => ['owner_id'],
            'mfa_values' => ['user_id'],
            'page_revisions' => ['created_by'],
            'role_user' => ['user_id'],
            'social_accounts' => ['user_id'],
            'user_invites' => ['user_id'],
            'views' => ['user_id'],
            'watches' => ['user_id'],
        ];

        // Ensure columns have user id before deletion
        foreach ($userColumnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                $this->assertDatabaseHas($table, [$column => $user->id]);
            }
        }

        $resp = $this->asAdmin()->delete("settings/users/{$user->id}", ['new_owner_id' => '']);
        $resp->assertRedirect('/settings/users');

        // Ensure columns missing user id after deletion
        foreach ($userColumnsByTable as $table => $columns) {
            foreach ($columns as $column) {
                $this->assertDatabaseMissing($table, [$column => $user->id]);
            }
        }

        // Check models exist where should be retained
        $this->assertDatabaseHas('attachments', ['id' => $attachment->id, 'created_by' => null, 'updated_by' => null]);
        $this->assertDatabaseHas('comments', ['id' => $comment->id, 'created_by' => null, 'updated_by' => null]);
        $this->assertDatabaseHas('deletions', ['id' => $deletion->id, 'deleted_by' => null]);
        $this->assertDatabaseHas('entities', ['id' => $page->id, 'created_by' => null, 'updated_by' => null, 'owned_by' => null]);
        $this->assertDatabaseHas('images', ['id' => $image->id, 'created_by' => null, 'updated_by' => null]);
        $this->assertDatabaseHas('imports', ['id' => $import->id, 'created_by' => null]);
        $this->assertDatabaseHas('page_revisions', ['id' => $revision->id, 'created_by' => null]);

        // Check models no longer exist where should have been deleted with the user
        $this->assertDatabaseMissing('api_tokens', ['id' => $apiToken->id]);
        $this->assertDatabaseMissing('email_confirmations', ['token' => 'abc123']);
        $this->assertDatabaseMissing('favourites', ['id' => $favourite->id]);
        $this->assertDatabaseMissing('mfa_values', ['id' => $mfaValue->id]);
        $this->assertDatabaseMissing('social_accounts', ['id' => $socialAccount->id]);
        $this->assertDatabaseMissing('user_invites', ['token' => 'abc123']);
        $this->assertDatabaseMissing('watches', ['id' => $watch->id]);

        // Ensure activity remains using the old ID (Special case for auditing changes)
        $this->assertDatabaseHas('activities', ['id' => $activity->id, 'user_id' => $user->id]);
    }

    public function test_delete_removes_user_preferences()
    {
        $editor = $this->users->editor();
        setting()->putUser($editor, 'dark-mode-enabled', 'true');

        $this->assertDatabaseHas('settings', [
            'setting_key' => 'user:' . $editor->id . ':dark-mode-enabled',
            'value' => 'true',
        ]);

        $this->asAdmin()->delete("settings/users/{$editor->id}");

        $this->assertDatabaseMissing('settings', [
            'setting_key' => 'user:' . $editor->id . ':dark-mode-enabled',
        ]);
    }

    public function test_guest_profile_shows_limited_form()
    {
        $guest = $this->users->guest();

        $resp = $this->asAdmin()->get('/settings/users/' . $guest->id);
        $resp->assertSee('Guest');
        $html = $this->withHtml($resp);

        $html->assertElementNotExists('#password');
        $html->assertElementNotExists('[name="language"]');
    }

    public function test_guest_profile_cannot_be_deleted()
    {
        $guestUser = $this->users->guest();
        $resp = $this->asAdmin()->get('/settings/users/' . $guestUser->id . '/delete');
        $resp->assertSee('Delete User');
        $resp->assertSee('Guest');
        $this->withHtml($resp)->assertElementContains('form[action$="/settings/users/' . $guestUser->id . '"] button', 'Confirm');

        $resp = $this->delete('/settings/users/' . $guestUser->id);
        $resp->assertRedirect('/settings/users/' . $guestUser->id);
        $resp = $this->followRedirects($resp);
        $resp->assertSee('cannot delete the guest user');
    }

    public function test_user_create_language_reflects_default_system_locale()
    {
        $langs = ['en', 'fr', 'hr'];
        foreach ($langs as $lang) {
            config()->set('app.default_locale', $lang);
            $resp = $this->asAdmin()->get('/settings/users/create');
            $this->withHtml($resp)->assertElementExists('select[name="language"] option[value="' . $lang . '"][selected]');
        }
    }

    public function test_user_creation_is_not_performed_if_the_invitation_sending_fails()
    {
        /** @var User $user */
        $user = User::factory()->make();
        $adminRole = Role::getRole('admin');

        // Simulate an invitation sending failure
        $this->mock(UserInviteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendInvitation')->once()->andThrow(UserInviteException::class);
        });

        $this->asAdmin()->post('/settings/users/create', [
            'name' => $user->name,
            'email' => $user->email,
            'send_invite' => 'true',
            'roles[' . $adminRole->id . ']' => 'true',
        ]);

        // Since the invitation failed, the user should not exist in the database
        $this->assertDatabaseMissing('users', $user->only('name', 'email'));
    }

    public function test_user_create_activity_is_not_persisted_if_the_invitation_sending_fails()
    {
        /** @var User $user */
        $user = User::factory()->make();

        $this->mock(UserInviteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendInvitation')->once()->andThrow(UserInviteException::class);
        });

        $this->asAdmin()->post('/settings/users/create', [
            'name' => $user->name,
            'email' => $user->email,
            'send_invite' => 'true',
        ]);

        $this->assertDatabaseMissing('activities', ['type' => 'USER_CREATE']);
    }

    public function test_return_to_form_with_warning_if_the_invitation_sending_fails()
    {
        $logger = $this->withTestLogger();
        /** @var User $user */
        $user = User::factory()->make();

        $this->mock(UserInviteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendInvitation')->once()->andThrow(UserInviteException::class);
        });

        $resp = $this->asAdmin()->post('/settings/users/create', [
            'name' => $user->name,
            'email' => $user->email,
            'send_invite' => 'true',
        ]);

        $resp->assertRedirect('/settings/users/create');
        $this->assertSessionError('Could not create user since invite email failed to send');
        $this->assertEquals($user->email, session()->getOldInput('email'));
        $this->assertTrue($logger->hasErrorThatContains('Failed to send user invite with error:'));
    }

    public function test_user_create_update_fails_if_locale_is_invalid()
    {
        $user = $this->users->editor();

        // Too long
        $resp = $this->asAdmin()->put($user->getEditUrl(), ['language' => 'this_is_too_long']);
        $resp->assertSessionHasErrors(['language' => 'The language may not be greater than 15 characters.']);
        session()->flush();

        // Invalid characters
        $resp = $this->put($user->getEditUrl(), ['language' => 'en<GB']);
        $resp->assertSessionHasErrors(['language' => 'The language may only contain letters, numbers, dashes and underscores.']);
        session()->flush();

        // Both on create
        $resp = $this->post('/settings/users/create', [
            'language' => 'en<GB_and_this_is_longer',
            'name' => 'My name',
            'email' => 'jimmy@example.com',
        ]);
        $resp->assertSessionHasErrors(['language' => 'The language may not be greater than 15 characters.']);
        $resp->assertSessionHasErrors(['language' => 'The language may only contain letters, numbers, dashes and underscores.']);
    }

    public function test_user_avatar_update_and_reset()
    {
        $user = $this->users->viewer();
        $avatarFile = $this->files->uploadedImage('avatar-icon.png');

        $this->assertEquals(0, $user->image_id);

        $upload = $this->asAdmin()->call('PUT', "/settings/users/{$user->id}", [
            'name' => 'Barry Scott',
        ], [], ['profile_image' => $avatarFile], []);
        $upload->assertRedirect('/settings/users');

        $user->refresh();
        $this->assertNotEquals(0, $user->image_id);
        /** @var Image $image */
        $image = Image::query()->findOrFail($user->image_id);
        $this->assertFileExists(public_path($image->path));

        $reset = $this->put("/settings/users/{$user->id}", [
            'name' => 'Barry Scott',
            'profile_image_reset' => 'true',
        ]);
        $upload->assertRedirect('/settings/users');

        $user->refresh();
        $this->assertFileDoesNotExist(public_path($image->path));
        $this->assertEquals(0, $user->image_id);
    }
}
