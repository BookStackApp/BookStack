<?php

namespace Tests\Api;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use BookStack\Notifications\UserInvite;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class UsersApiTest extends TestCase
{
    use TestsApi;

    protected $baseEndpoint = '/api/users';

    protected $endpointMap = [
        ['get', '/api/users'],
        ['post', '/api/users'],
        ['get', '/api/users/1'],
        ['put', '/api/users/1'],
        ['delete', '/api/users/1'],
    ];

    public function test_users_manage_permission_needed_for_all_endpoints()
    {
        $this->actingAsApiEditor();
        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_no_endpoints_accessible_in_demo_mode()
    {
        config()->set('app.env', 'demo');
        $this->actingAsApiAdmin();

        foreach ($this->endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }
    }

    public function test_index_endpoint_returns_expected_shelf()
    {
        $this->actingAsApiAdmin();
        /** @var User $firstUser */
        $firstUser = User::query()->orderBy('id', 'asc')->first();

        $resp = $this->getJson($this->baseEndpoint . '?count=1&sort=+id');
        $resp->assertJson(['data' => [
            [
                'id'          => $firstUser->id,
                'name'        => $firstUser->name,
                'slug'        => $firstUser->slug,
                'email'       => $firstUser->email,
                'profile_url' => $firstUser->getProfileUrl(),
                'edit_url'    => $firstUser->getEditUrl(),
                'avatar_url'  => $firstUser->getAvatar(),
            ],
        ]]);
    }

    public function test_create_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var Role $role */
        $role = Role::query()->first();

        $resp = $this->postJson($this->baseEndpoint, [
            'name'        => 'Benny Boris',
            'email'       => 'bboris@example.com',
            'password'    => 'mysuperpass',
            'language'    => 'it',
            'roles'       => [$role->id],
            'send_invite' => false,
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'name'             => 'Benny Boris',
            'email'            => 'bboris@example.com',
            'external_auth_id' => '',
            'roles'            => [
                [
                    'id'           => $role->id,
                    'display_name' => $role->display_name,
                ],
            ],
        ]);
        $this->assertDatabaseHas('users', ['email' => 'bboris@example.com']);

        /** @var User $user */
        $user = User::query()->where('email', '=', 'bboris@example.com')->first();
        $this->assertActivityExists(ActivityType::USER_CREATE, null, $user->logDescriptor());
        $this->assertEquals(1, $user->roles()->count());
        $this->assertEquals('it', setting()->getUser($user, 'language'));
    }

    public function test_create_with_send_invite()
    {
        $this->actingAsApiAdmin();
        Notification::fake();

        $resp = $this->postJson($this->baseEndpoint, [
            'name'        => 'Benny Boris',
            'email'       => 'bboris@example.com',
            'send_invite' => true,
        ]);

        $resp->assertStatus(200);
        /** @var User $user */
        $user = User::query()->where('email', '=', 'bboris@example.com')->first();
        Notification::assertSentTo($user, UserInvite::class);
    }

    public function test_create_name_and_email_validation()
    {
        $this->actingAsApiAdmin();
        /** @var User $existingUser */
        $existingUser = User::query()->first();

        $resp = $this->postJson($this->baseEndpoint, [
            'email' => 'bboris@example.com',
        ]);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse(['name' => ['The name field is required.']]));

        $resp = $this->postJson($this->baseEndpoint, [
            'name' => 'Benny Boris',
        ]);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse(['email' => ['The email field is required.']]));

        $resp = $this->postJson($this->baseEndpoint, [
            'email' => $existingUser->email,
            'name'  => 'Benny Boris',
        ]);
        $resp->assertStatus(422);
        $resp->assertJson($this->validationResponse(['email' => ['The email has already been taken.']]));
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
            'id'               => $user->id,
            'slug'             => $user->slug,
            'email'            => $user->email,
            'external_auth_id' => $user->external_auth_id,
            'roles'            => [
                [
                    'id'           => $userRole->id,
                    'display_name' => $userRole->display_name,
                ],
            ],
        ]);
    }

    public function test_update_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var User $user */
        $user = $this->users->admin();
        $roles = Role::query()->pluck('id');
        $resp = $this->putJson($this->baseEndpoint . "/{$user->id}", [
            'name'             => 'My updated user',
            'email'            => 'barrytest@example.com',
            'roles'            => $roles,
            'external_auth_id' => 'btest',
            'password'         => 'barrytester',
            'language'         => 'fr',
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'id'               => $user->id,
            'name'             => 'My updated user',
            'email'            => 'barrytest@example.com',
            'external_auth_id' => 'btest',
        ]);
        $user->refresh();
        $this->assertEquals('fr', setting()->getUser($user, 'language'));
        $this->assertEquals(count($roles), $user->roles()->count());
        $this->assertNotEquals('barrytester', $user->password);
        $this->assertTrue(Hash::check('barrytester', $user->password));
    }

    public function test_update_endpoint_does_not_remove_info_if_not_provided()
    {
        $this->actingAsApiAdmin();
        /** @var User $user */
        $user = $this->users->admin();
        $roleCount = $user->roles()->count();
        $resp = $this->putJson($this->baseEndpoint . "/{$user->id}", []);

        $resp->assertStatus(200);
        $this->assertDatabaseHas('users', [
            'id'       => $user->id,
            'name'     => $user->name,
            'email'    => $user->email,
            'password' => $user->password,
        ]);
        $this->assertEquals($roleCount, $user->roles()->count());
    }

    public function test_delete_endpoint()
    {
        $this->actingAsApiAdmin();
        /** @var User $user */
        $user = User::query()->where('id', '!=', $this->users->admin()->id)
            ->whereNull('system_name')
            ->first();

        $resp = $this->deleteJson($this->baseEndpoint . "/{$user->id}");

        $resp->assertStatus(204);
        $this->assertActivityExists('user_delete', null, $user->logDescriptor());
    }

    public function test_delete_endpoint_with_ownership_migration_user()
    {
        $this->actingAsApiAdmin();
        /** @var User $user */
        $user = User::query()->where('id', '!=', $this->users->admin()->id)
            ->whereNull('system_name')
            ->first();
        $entityChain = $this->entities->createChainBelongingToUser($user);
        /** @var User $newOwner */
        $newOwner = User::query()->where('id', '!=', $user->id)->first();

        /** @var Entity $entity */
        foreach ($entityChain as $entity) {
            $this->assertEquals($user->id, $entity->owned_by);
        }

        $resp = $this->deleteJson($this->baseEndpoint . "/{$user->id}", [
            'migrate_ownership_id' => $newOwner->id,
        ]);

        $resp->assertStatus(204);
        /** @var Entity $entity */
        foreach ($entityChain as $entity) {
            $this->assertEquals($newOwner->id, $entity->refresh()->owned_by);
        }
    }

    public function test_delete_endpoint_fails_deleting_only_admin()
    {
        $this->actingAsApiAdmin();
        $adminRole = Role::getSystemRole('admin');
        $adminToDelete = $adminRole->users()->first();
        $adminRole->users()->where('id', '!=', $adminToDelete->id)->delete();

        $resp = $this->deleteJson($this->baseEndpoint . "/{$adminToDelete->id}");

        $resp->assertStatus(500);
        $resp->assertJson($this->errorResponse('You cannot delete the only admin', 500));
    }

    public function test_delete_endpoint_fails_deleting_public_user()
    {
        $this->actingAsApiAdmin();
        /** @var User $publicUser */
        $publicUser = User::query()->where('system_name', '=', 'public')->first();

        $resp = $this->deleteJson($this->baseEndpoint . "/{$publicUser->id}");

        $resp->assertStatus(500);
        $resp->assertJson($this->errorResponse('You cannot delete the guest user', 500));
    }
}
