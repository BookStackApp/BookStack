<?php

namespace Tests\Api;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use Tests\TestCase;

class RolesApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/roles';

    protected array $endpointMap = [
        ['get', '/api/roles'],
        ['post', '/api/roles'],
        ['get', '/api/roles/1'],
        ['put', '/api/roles/1'],
        ['delete', '/api/roles/1'],
    ];

    public function test_user_roles_manage_permission_needed_for_all_endpoints()
    {
        $this->actingAsApiEditor();
        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_index_endpoint_returns_expected_role_and_count()
    {
        $this->actingAsApiAdmin();
        /** @var Role $firstRole */
        $firstRole = Role::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id'          => $firstRole->id,
                'display_name'        => $firstRole->display_name,
                'description'        => $firstRole->description,
                'mfa_enforced'       => $firstRole->mfa_enforced,
                'permissions_count' => $firstRole->permissions()->count(),
                'users_count'    => $firstRole->users()->count(),
                'created_at'    => $firstRole->created_at->toJSON(),
                'updated_at'    => $firstRole->updated_at->toJSON(),
            ],
        ]]);

        $resp->assertJson(['total' => Role::query()->count()]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var Role $role */
        $role = Role::query()->first();

        $resp = $this->postJson($this->baseEndpoint, [
            'display_name' => 'My awesome role',
            'description'  => 'My great role description',
            'mfa_enforced' => true,
            'permissions'  => [
                'content-export',
                'users-manage',
                'page-view-own',
                'page-view-all',
            ]
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'display_name' => 'My awesome role',
            'description'  => 'My great role description',
            'mfa_enforced' => true,
            'permissions'  => [
                'content-export',
                'users-manage',
                'page-view-own',
                'page-view-all',
            ]
        ]);

        $this->assertDatabaseHas('roles', [
            'display_name' => 'My awesome role',
            'description'  => 'My great role description',
            'mfa_enforced' => true,
        ]);

        /** @var Role $role */
        $role = Role::query()->where('display_name', '=', 'My awesome role')->first();
        $this->assertActivityExists(ActivityType::ROLE_CREATE, null, $role->logDescriptor());
        $this->assertEquals(4, $role->permissions()->count());
    }

    public function test_create_name_and_description_validation()
    {
        $this->actingAsApiAdmin();
        /** @var User $existingUser */
        $existingUser = User::query()->first();

        $resp = $this->postJson($this->baseEndpoint, [
            'description' => 'My new role',
        ]);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse(['display_name' => ['The display_name field is required.']]));

        $resp = $this->postJson($this->baseEndpoint, [
            'name' => 'My great role with a too long desc',
            'description' => str_repeat('My great desc', 20),
        ]);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse(['description' => ['The description may not be greater than 180 characters.']]));
    }

    public function test_read_endpoint()
    {
        $this->actingAsApiAdmin();
        $role = $this->users->editor()->roles()->first();
        $resp = $this->getJson($this->baseEndpoint . "/{$role->id}");

        $resp->assertStatus(200);
        $resp->assertJson([
            'display_name' => $role->display_name,
            'description'  => $role->description,
            'mfa_enforced' => $role->mfa_enforced,
            'permissions'  => $role->permissions()->pluck('name')->toArray(),
            'users' => $role->users()->get()->map(function (User $user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'slug' => $user->slug,
                ];
            })->toArray(),
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiAdmin();
        $role = $this->users->editor()->roles()->first();
        $resp = $this->putJson($this->baseEndpoint . "/{$role->id}", [
            'display_name' => 'My updated role',
            'description'  => 'My great role description',
            'mfa_enforced' => true,
            'permissions'  => [
                'content-export',
                'users-manage',
                'page-view-own',
                'page-view-all',
            ]
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'id'           => $role->id,
            'display_name' => 'My updated role',
            'description'  => 'My great role description',
            'mfa_enforced' => true,
            'permissions'  => [
                'content-export',
                'users-manage',
                'page-view-own',
                'page-view-all',
            ]
        ]);

        $role->refresh();
        $this->assertEquals(4, $role->permissions()->count());
    }

    public function test_update_endpoint_does_not_remove_info_if_not_provided()
    {
        $this->actingAsApiAdmin();
        $role = $this->users->editor()->roles()->first();
        $resp = $this->putJson($this->baseEndpoint . "/{$role->id}", []);
        $permissionCount = $role->permissions()->count();

        $resp->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'           => $role->id,
            'display_name' => $role->display_name,
            'description'  => $role->description,
        ]);

        $role->refresh();
        $this->assertEquals($permissionCount, $role->permissions()->count());
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiAdmin();
        $role = $this->users->editor()->roles()->first();

        $resp = $this->deleteJson($this->baseEndpoint . "/{$role->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists(ActivityType::ROLE_DELETE, null, $role->logDescriptor());
    }

    public function test_delete_endpoint_fails_deleting_system_role()
    {
        $this->actingAsApiAdmin();
        $adminRole = Role::getSystemRole('admin');

        $resp = $this->deleteJson($this->baseEndpoint . "/{$adminRole->id}");

        $resp->assertStatus(500);
        $resp->assertJson($this->errorResponse('This role is a system role and cannot be deleted', 500));
    }

    public function test_delete_endpoint_fails_deleting_default_registration_role()
    {
        $this->actingAsApiAdmin();
        $role = $this->users->attachNewRole($this->users->editor());
        $this->setSettings(['registration-role' => $role->id]);

        $resp = $this->deleteJson($this->baseEndpoint . "/{$role->id}");

        $resp->assertStatus(500);
        $resp->assertJson($this->errorResponse('This role cannot be deleted while set as the default registration role', 500));
    }
}
