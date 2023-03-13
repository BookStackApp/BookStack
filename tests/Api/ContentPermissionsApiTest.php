<?php

namespace Tests\Api;

use Tests\TestCase;

class ContentPermissionsApiTest extends TestCase
{
    use TestsApi;

    protected string $baseEndpoint = '/api/content-permissions';

    public function test_user_roles_manage_permission_needed_for_all_endpoints()
    {
        $page = $this->entities->page();
        $endpointMap = [
            ['get', "/api/content-permissions/page/{$page->id}"],
            ['put', "/api/content-permissions/page/{$page->id}"],
        ];
        $editor = $this->users->editor();

        $this->actingAs($editor, 'api');
        foreach ($endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        }

        $this->permissions->grantUserRolePermissions($editor, ['restrictions-manage-all']);

        foreach ($endpointMap as [$method, $uri]) {
            $resp = $this->json($method, $uri);
            $this->assertNotEquals(403, $resp->getStatusCode());
        }
    }

    public function test_read_endpoint_shows_expected_detail()
    {
        $page = $this->entities->page();
        $owner = $this->users->newUser();
        $role = $this->users->createRole();
        $this->permissions->addEntityPermission($page, ['view', 'delete'], $role);
        $this->permissions->changeEntityOwner($page, $owner);
        $this->permissions->setFallbackPermissions($page, ['update', 'create']);

        $this->actingAsApiAdmin();
        $resp = $this->getJson($this->baseEndpoint . "/page/{$page->id}");

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => [
                'id' => $owner->id, 'name' => $owner->name, 'slug' => $owner->slug,
            ],
            'role_permissions' => [
                [
                    'role_id' => $role->id,
                    'view' => true,
                    'create' => false,
                    'update' => false,
                    'delete' => true,
                    'role' => [
                        'id' => $role->id,
                        'display_name' => $role->display_name,
                    ]
                ]
            ],
            'fallback_permissions' => [
                'inheriting' => false,
                'view' => false,
                'create' => true,
                'update' => true,
                'delete' => false,
            ],
        ]);
    }

    public function test_read_endpoint_shows_expected_detail_when_items_are_empty()
    {
        $page = $this->entities->page();
        $page->permissions()->delete();
        $page->owned_by = null;
        $page->save();

        $this->actingAsApiAdmin();
        $resp = $this->getJson($this->baseEndpoint . "/page/{$page->id}");

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => null,
            'role_permissions' => [],
            'fallback_permissions' => [
                'inheriting' => true,
                'view' => null,
                'create' => null,
                'update' => null,
                'delete' => null,
            ],
        ]);
    }

    public function test_update_endpoint_can_change_owner()
    {
        $page = $this->entities->page();
        $newOwner = $this->users->newUser();

        $this->actingAsApiAdmin();
        $resp = $this->putJson($this->baseEndpoint . "/page/{$page->id}", [
            'owner_id' => $newOwner->id,
        ]);

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => ['id' => $newOwner->id, 'name' => $newOwner->name, 'slug' => $newOwner->slug],
            'role_permissions' => [],
            'fallback_permissions' => [
                'inheriting' => true,
                'view' => null,
                'create' => null,
                'update' => null,
                'delete' => null,
            ],
        ]);
    }

    public function test_update_can_set_role_permissions()
    {
        $page = $this->entities->page();
        $page->owned_by = null;
        $page->save();
        $newRoleA = $this->users->createRole();
        $newRoleB = $this->users->createRole();

        $this->actingAsApiAdmin();
        $resp = $this->putJson($this->baseEndpoint . "/page/{$page->id}", [
            'role_permissions' => [
                ['role_id' => $newRoleA->id, 'view' => true, 'create' => false, 'update' => false, 'delete' => false],
                ['role_id' => $newRoleB->id, 'view' => true, 'create' => false, 'update' => true, 'delete' => true],
            ],
        ]);

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => null,
            'role_permissions' => [
                [
                    'role_id' => $newRoleA->id,
                    'view' => true,
                    'create' => false,
                    'update' => false,
                    'delete' => false,
                    'role' => [
                        'id' => $newRoleA->id,
                        'display_name' => $newRoleA->display_name,
                    ]
                ],
                [
                    'role_id' => $newRoleB->id,
                    'view' => true,
                    'create' => false,
                    'update' => true,
                    'delete' => true,
                    'role' => [
                        'id' => $newRoleB->id,
                        'display_name' => $newRoleB->display_name,
                    ]
                ]
            ],
            'fallback_permissions' => [
                'inheriting' => true,
                'view' => null,
                'create' => null,
                'update' => null,
                'delete' => null,
            ],
        ]);
    }

    public function test_update_can_set_fallback_permissions()
    {
        $page = $this->entities->page();
        $page->owned_by = null;
        $page->save();

        $this->actingAsApiAdmin();
        $resp = $this->putJson($this->baseEndpoint . "/page/{$page->id}", [
            'fallback_permissions' => [
                'inheriting' => false,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => false,
            ],
        ]);

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => null,
            'role_permissions' => [],
            'fallback_permissions' => [
                'inheriting' => false,
                'view' => true,
                'create' => true,
                'update' => true,
                'delete' => false,
            ],
        ]);
    }

    public function test_update_can_clear_roles_permissions()
    {
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, ['view'], $this->users->createRole());
        $page->owned_by = null;
        $page->save();

        $this->actingAsApiAdmin();
        $resp = $this->putJson($this->baseEndpoint . "/page/{$page->id}", [
            'role_permissions' => [],
        ]);

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => null,
            'role_permissions' => [],
            'fallback_permissions' => [
                'inheriting' => true,
                'view' => null,
                'create' => null,
                'update' => null,
                'delete' => null,
            ],
        ]);
    }

    public function test_update_can_clear_fallback_permissions()
    {
        $page = $this->entities->page();
        $this->permissions->setFallbackPermissions($page, ['view', 'update']);
        $page->owned_by = null;
        $page->save();

        $this->actingAsApiAdmin();
        $resp = $this->putJson($this->baseEndpoint . "/page/{$page->id}", [
            'fallback_permissions' => [
                'inheriting' => true,
            ],
        ]);

        $resp->assertOk();
        $resp->assertExactJson([
            'owner' => null,
            'role_permissions' => [],
            'fallback_permissions' => [
                'inheriting' => true,
                'view' => null,
                'create' => null,
                'update' => null,
                'delete' => null,
            ],
        ]);
    }
}
