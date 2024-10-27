<?php

namespace Tests\Activity;

use BookStack\Activity\ActivityType;
use BookStack\Facades\Activity;
use Tests\Api\TestsApi;
use Tests\TestCase;

class AuditLogApiTest extends TestCase
{
    use TestsApi;

    public function test_user_and_settings_manage_permissions_needed()
    {
        $editor = $this->users->editor();

        $assertPermissionErrorOnCall = function () use ($editor) {
            $resp = $this->actingAsForApi($editor)->getJson('/api/audit-log');
            $resp->assertStatus(403);
            $resp->assertJson($this->permissionErrorResponse());
        };

        $assertPermissionErrorOnCall();
        $this->permissions->grantUserRolePermissions($editor, ['users-manage']);
        $assertPermissionErrorOnCall();
        $this->permissions->removeUserRolePermissions($editor, ['users-manage']);
        $this->permissions->grantUserRolePermissions($editor, ['settings-manage']);
        $assertPermissionErrorOnCall();

        $this->permissions->grantUserRolePermissions($editor, ['settings-manage', 'users-manage']);
        $resp = $this->actingAsForApi($editor)->getJson('/api/audit-log');
        $resp->assertOk();
    }

    public function test_index_endpoint_returns_expected_data()
    {
        $page = $this->entities->page();
        $admin = $this->users->admin();
        $this->actingAsForApi($admin);
        Activity::add(ActivityType::PAGE_UPDATE, $page);

        $resp = $this->get("/api/audit-log?filter[loggable_id]={$page->id}");
        $resp->assertJson(['data' => [
            [
                'type' => 'page_update',
                'detail' => "({$page->id}) {$page->name}",
                'user_id' => $admin->id,
                'loggable_id' => $page->id,
                'loggable_type' => 'page',
                'ip' => '127.0.0.1',
                'user' => [
                    'id' => $admin->id,
                    'name' => $admin->name,
                    'slug' => $admin->slug,
                ],
            ]
        ]]);
    }
}
