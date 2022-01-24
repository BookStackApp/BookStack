<?php

namespace Tests\User;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Access\UserInviteService;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Mockery\MockInterface;
use RuntimeException;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    public function test_user_creation()
    {
        /** @var User $user */
        $user = User::factory()->make();
        $adminRole = Role::getRole('admin');

        $resp = $this->asAdmin()->get('/settings/users');
        $resp->assertElementContains('a[href="' . url('/settings/users/create') . '"]', 'Add New User');

        $this->get('/settings/users/create')
            ->assertElementContains('form[action="' . url('/settings/users/create') . '"]', 'Save');

        $resp = $this->post('/settings/users/create', [
            'name'                          => $user->name,
            'email'                         => $user->email,
            'password'                      => $user->password,
            'password-confirm'              => $user->password,
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
        $user = $this->getNormalUser();
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
        $user = $this->getNormalUser();
        $userProfilePage = '/settings/users/' . $user->id;

        $this->asAdmin()->get($userProfilePage);
        $this->put($userProfilePage, [
            'password' => 'newpassword',
        ])->assertRedirect($userProfilePage);

        $this->get($userProfilePage)->assertSee('Password confirmation required');

        $this->put($userProfilePage, [
            'password'         => 'newpassword',
            'password-confirm' => 'newpassword',
        ])->assertRedirect('/settings/users');

        $userPassword = User::query()->find($user->id)->password;
        $this->assertTrue(Hash::check('newpassword', $userPassword));
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
        $editor = $this->getEditor();
        $resp = $this->asAdmin()->delete("settings/users/{$editor->id}");
        $resp->assertRedirect('/settings/users');
        $resp = $this->followRedirects($resp);

        $resp->assertSee('User successfully removed');
        $this->assertActivityExists(ActivityType::USER_DELETE);

        $this->assertDatabaseMissing('users', ['id' => $editor->id]);
    }

    public function test_delete_offers_migrate_option()
    {
        $editor = $this->getEditor();
        $resp = $this->asAdmin()->get("settings/users/{$editor->id}/delete");
        $resp->assertSee('Migrate Ownership');
        $resp->assertSee('new_owner_id');
    }

    public function test_migrate_option_hidden_if_user_cannot_manage_users()
    {
        $editor = $this->getEditor();

        $resp = $this->asEditor()->get("settings/users/{$editor->id}/delete");
        $resp->assertDontSee('Migrate Ownership');
        $resp->assertDontSee('new_owner_id');

        $this->giveUserPermissions($editor, ['users-manage']);

        $resp = $this->asEditor()->get("settings/users/{$editor->id}/delete");
        $resp->assertSee('Migrate Ownership');
        $resp->assertSee('new_owner_id');
    }

    public function test_delete_with_new_owner_id_changes_ownership()
    {
        $page = Page::query()->first();
        $owner = $page->ownedBy;
        $newOwner = User::query()->where('id', '!=', $owner->id)->first();

        $this->asAdmin()->delete("settings/users/{$owner->id}", ['new_owner_id' => $newOwner->id]);
        $this->assertDatabaseHas('pages', [
            'id'       => $page->id,
            'owned_by' => $newOwner->id,
        ]);
    }

    public function test_guest_profile_shows_limited_form()
    {
        $guest = User::getDefault();
        $resp = $this->asAdmin()->get('/settings/users/' . $guest->id);
        $resp->assertSee('Guest');
        $resp->assertElementNotExists('#password');
    }

    public function test_guest_profile_cannot_be_deleted()
    {
        $guestUser = User::getDefault();
        $resp = $this->asAdmin()->get('/settings/users/' . $guestUser->id . '/delete');
        $resp->assertSee('Delete User');
        $resp->assertSee('Guest');
        $resp->assertElementContains('form[action$="/settings/users/' . $guestUser->id . '"] button', 'Confirm');

        $resp = $this->delete('/settings/users/' . $guestUser->id);
        $resp->assertRedirect('/settings/users/' . $guestUser->id);
        $resp = $this->followRedirects($resp);
        $resp->assertSee('cannot delete the guest user');
    }

    public function test_user_creation_is_not_performed_if_the_invitation_sending_fails()
    {
        /** @var User $user */
        $user = User::factory()->make();
        $adminRole = Role::getRole('admin');

        // Simulate an invitation sending failure
        $this->mock(UserInviteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendInvitation')->once()->andThrow(RuntimeException::class);
        });

        $this->asAdmin()->post('/settings/users/create', [
            'name'                          => $user->name,
            'email'                         => $user->email,
            'send_invite'                   => 'true',
            'roles[' . $adminRole->id . ']' => 'true',
        ]);

        // Since the invitation failed, the user should not exist in the database
        $this->assertDatabaseMissing('users', $user->only('name', 'email'));
    }

    public function test_user_create_activity_is_not_persisted_if_the_invitation_sending_fails()
    {
        /** @var User $user */
        $user = User::factory()->make();
        $adminRole = Role::getRole('admin');

        $this->mock(UserInviteService::class, function (MockInterface $mock) {
            $mock->shouldReceive('sendInvitation')->once()->andThrow(RuntimeException::class);
        });

        $this->asAdmin()->post('/settings/users/create', [
            'name'                          => $user->name,
            'email'                         => $user->email,
            'send_invite'                   => 'true',
            'roles[' . $adminRole->id . ']' => 'true',
        ]);

        $this->assertDatabaseMissing('activities', ['type' => 'USER_CREATE']);
    }
}
