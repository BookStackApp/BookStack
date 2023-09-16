<?php

namespace Tests\User;

use BookStack\Users\Models\User;
use Tests\TestCase;

class UserSearchTest extends TestCase
{
    public function test_select_search_matches_by_name()
    {
        $viewer = $this->users->viewer();
        $admin = $this->users->admin();
        $resp = $this->actingAs($admin)->get('/search/users/select?search=' . urlencode($viewer->name));

        $resp->assertOk();
        $resp->assertSee($viewer->name);
        $resp->assertDontSee($admin->name);
    }

    public function test_select_search_shows_first_by_name_without_search()
    {
        /** @var User $firstUser */
        $firstUser = User::query()->orderBy('name', 'desc')->first();
        $resp = $this->asAdmin()->get('/search/users/select');

        $resp->assertOk();
        $resp->assertSee($firstUser->name);
    }

    public function test_select_search_does_not_match_by_email()
    {
        $viewer = $this->users->viewer();
        $editor = $this->users->editor();
        $resp = $this->actingAs($editor)->get('/search/users/select?search=' . urlencode($viewer->email));

        $resp->assertDontSee($viewer->name);
    }

    public function test_select_requires_right_permission()
    {
        $permissions = ['users-manage', 'restrictions-manage-own', 'restrictions-manage-all'];
        $user = $this->users->viewer();

        foreach ($permissions as $permission) {
            $resp = $this->actingAs($user)->get('/search/users/select?search=a');
            $this->assertPermissionError($resp);

            $this->permissions->grantUserRolePermissions($user, [$permission]);
            $resp = $this->actingAs($user)->get('/search/users/select?search=a');
            $resp->assertOk();
            $user->roles()->delete();
            $user->clearPermissionCache();
        }
    }

    public function test_select_requires_logged_in_user()
    {
        $this->setSettings(['app-public' => true]);
        $this->permissions->grantUserRolePermissions($this->users->guest(), ['users-manage']);

        $resp = $this->get('/search/users/select?search=a');
        $this->assertPermissionError($resp);
    }
}
