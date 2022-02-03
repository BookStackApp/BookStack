<?php

namespace Tests\Api;

use BookStack\Auth\Role;
use BookStack\Auth\User;
use Tests\TestCase;

class UsersApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/users';

    public function test_users_manage_permission_needed_for_all_endpoints()
    {
        // TODO
    }

    public function test_index_endpoint_returns_expected_shelf()
    {
        $this->actingAsApiAdmin();
        /** @var User $firstUser */
        $firstUser = User::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id'   => $firstUser->id,
                'name' => $firstUser->name,
                'slug' => $firstUser->slug,
                'email' => $firstUser->email,
                'profile_url' => $firstUser->getProfileUrl(),
                'edit_url' => $firstUser->getEditUrl(),
                'avatar_url' => $firstUser->getAvatar(),
            ],
        ]]);
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var User $user */
        $user = User::query()->first();
        /** @var Role $userRole */
        $userRole = $user->roles()->first();

        $resp = $this->getJson($this->baseEndpoint . "/{$user->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'id'         => $user->id,
            'slug'       => $user->slug,
            'email'      => $user->email,
            'external_auth_id' => $user->external_auth_id,
            'roles' => [
                [
                    'id' => $userRole->id,
                    'display_name' => $userRole->display_name,
                ]
            ],
        ]);
    }
}
