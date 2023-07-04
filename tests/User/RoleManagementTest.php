<?php

namespace Tests\User;

use BookStack\Activity\ActivityType;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Tests\TestCase;

class RoleManagementTest extends TestCase
{
    public function test_cannot_delete_admin_role()
    {
        $adminRole = Role::getRole('admin');
        $deletePageUrl = '/settings/roles/delete/' . $adminRole->id;

        $this->asAdmin()->get($deletePageUrl);
        $this->delete($deletePageUrl)->assertRedirect($deletePageUrl);
        $this->get($deletePageUrl)->assertSee('cannot be deleted');
    }

    public function test_role_cannot_be_deleted_if_default()
    {
        $newRole = $this->users->createRole();
        $this->setSettings(['registration-role' => $newRole->id]);

        $deletePageUrl = '/settings/roles/delete/' . $newRole->id;
        $this->asAdmin()->get($deletePageUrl);
        $this->delete($deletePageUrl)->assertRedirect($deletePageUrl);
        $this->get($deletePageUrl)->assertSee('cannot be deleted');
    }

    public function test_role_create_update_delete_flow()
    {
        $testRoleName = 'Test Role';
        $testRoleDesc = 'a little test description';
        $testRoleUpdateName = 'An Super Updated role';

        // Creation
        $resp = $this->asAdmin()->get('/settings/features');
        $this->withHtml($resp)->assertElementContains('a[href="' . url('/settings/roles') . '"]', 'Roles');

        $resp = $this->get('/settings/roles');
        $this->withHtml($resp)->assertElementContains('a[href="' . url('/settings/roles/new') . '"]', 'Create New Role');

        $resp = $this->get('/settings/roles/new');
        $this->withHtml($resp)->assertElementContains('form[action="' . url('/settings/roles/new') . '"]', 'Save Role');

        $resp = $this->post('/settings/roles/new', [
            'display_name' => $testRoleName,
            'description'  => $testRoleDesc,
        ]);
        $resp->assertRedirect('/settings/roles');

        $resp = $this->get('/settings/roles');
        $resp->assertSee($testRoleName);
        $resp->assertSee($testRoleDesc);
        $this->assertDatabaseHas('roles', [
            'display_name' => $testRoleName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => false,
        ]);

        /** @var Role $role */
        $role = Role::query()->where('display_name', '=', $testRoleName)->first();

        // Updating
        $resp = $this->get('/settings/roles/' . $role->id);
        $resp->assertSee($testRoleName);
        $resp->assertSee($testRoleDesc);
        $this->withHtml($resp)->assertElementContains('form[action="' . url('/settings/roles/' . $role->id) . '"]', 'Save Role');

        $resp = $this->put('/settings/roles/' . $role->id, [
            'display_name' => $testRoleUpdateName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => 'true',
        ]);
        $resp->assertRedirect('/settings/roles');
        $this->assertDatabaseHas('roles', [
            'display_name' => $testRoleUpdateName,
            'description'  => $testRoleDesc,
            'mfa_enforced' => true,
        ]);

        // Deleting
        $resp = $this->get('/settings/roles/' . $role->id);
        $this->withHtml($resp)->assertElementContains('a[href="' . url("/settings/roles/delete/$role->id") . '"]', 'Delete Role');

        $resp = $this->get("/settings/roles/delete/$role->id");
        $resp->assertSee($testRoleUpdateName);
        $this->withHtml($resp)->assertElementContains('form[action="' . url("/settings/roles/delete/$role->id") . '"]', 'Confirm');

        $resp = $this->delete("/settings/roles/delete/$role->id");
        $resp->assertRedirect('/settings/roles');
        $this->get('/settings/roles')->assertSee('Role successfully deleted');
        $this->assertActivityExists(ActivityType::ROLE_DELETE);
    }

    public function test_admin_role_cannot_be_removed_if_user_last_admin()
    {
        /** @var Role $adminRole */
        $adminRole = Role::query()->where('system_name', '=', 'admin')->first();
        $adminUser = $this->users->admin();
        $adminRole->users()->where('id', '!=', $adminUser->id)->delete();
        $this->assertEquals(1, $adminRole->users()->count());

        $viewerRole = $this->users->viewer()->roles()->first();

        $editUrl = '/settings/users/' . $adminUser->id;
        $resp = $this->actingAs($adminUser)->put($editUrl, [
            'name'  => $adminUser->name,
            'email' => $adminUser->email,
            'roles' => [
                'viewer' => strval($viewerRole->id),
            ],
        ]);

        $resp->assertRedirect($editUrl);

        $resp = $this->get($editUrl);
        $resp->assertSee('This user is the only user assigned to the administrator role');
    }

    public function test_migrate_users_on_delete_works()
    {
        $roleA = $this->users->createRole();
        $roleB = $this->users->createRole();
        $user = $this->users->viewer();
        $user->attachRole($roleB);

        $this->assertCount(0, $roleA->users()->get());
        $this->assertCount(1, $roleB->users()->get());

        $deletePage = $this->asAdmin()->get("/settings/roles/delete/$roleB->id");
        $this->withHtml($deletePage)->assertElementExists('select[name=migrate_role_id]');
        $this->asAdmin()->delete("/settings/roles/delete/$roleB->id", [
            'migrate_role_id' => $roleA->id,
        ]);

        $this->assertCount(1, $roleA->users()->get());
        $this->assertEquals($user->id, $roleA->users()->first()->id);
    }

    public function test_delete_with_empty_migrate_option_works()
    {
        $role = $this->users->attachNewRole($this->users->viewer());

        $this->assertCount(1, $role->users()->get());

        $deletePage = $this->asAdmin()->get("/settings/roles/delete/$role->id");
        $this->withHtml($deletePage)->assertElementExists('select[name=migrate_role_id]');
        $resp = $this->asAdmin()->delete("/settings/roles/delete/$role->id", [
            'migrate_role_id' => '',
        ]);

        $resp->assertRedirect('/settings/roles');
        $this->assertDatabaseMissing('roles', ['id' => $role->id]);
    }

    public function test_entity_permissions_are_removed_on_delete()
    {
        /** @var Role $roleA */
        $roleA = Role::query()->create(['display_name' => 'Entity Permissions Delete Test']);
        $page = $this->entities->page();

        $this->permissions->setEntityPermissions($page, ['view'], [$roleA]);

        $this->assertDatabaseHas('entity_permissions', [
            'role_id' => $roleA->id,
            'entity_id' => $page->id,
            'entity_type' => $page->getMorphClass(),
        ]);

        $this->asAdmin()->delete("/settings/roles/delete/$roleA->id");

        $this->assertDatabaseMissing('entity_permissions', [
            'role_id' => $roleA->id,
            'entity_id' => $page->id,
            'entity_type' => $page->getMorphClass(),
        ]);
    }

    public function test_image_view_notice_shown_on_role_form()
    {
        /** @var Role $role */
        $role = Role::query()->first();
        $this->asAdmin()->get("/settings/roles/{$role->id}")
            ->assertSee('Actual access of uploaded image files will be dependant upon system image storage option');
    }

    public function test_copy_role_button_shown()
    {
        /** @var Role $role */
        $role = Role::query()->first();
        $resp = $this->asAdmin()->get("/settings/roles/{$role->id}");
        $this->withHtml($resp)->assertElementContains('a[href$="/roles/new?copy_from=' . $role->id . '"]', 'Copy');
    }

    public function test_copy_from_param_on_create_prefills_with_other_role_data()
    {
        /** @var Role $role */
        $role = Role::query()->first();
        $resp = $this->asAdmin()->get("/settings/roles/new?copy_from={$role->id}");
        $resp->assertOk();
        $this->withHtml($resp)->assertElementExists('input[name="display_name"][value="' . ($role->display_name . ' (Copy)') . '"]');
    }

    public function test_public_role_visible_in_user_edit_screen()
    {
        /** @var User $user */
        $user = User::query()->first();
        $adminRole = Role::getSystemRole('admin');
        $publicRole = Role::getSystemRole('public');
        $resp = $this->asAdmin()->get('/settings/users/' . $user->id);
        $this->withHtml($resp)->assertElementExists('[name="roles[' . $adminRole->id . ']"]')
            ->assertElementExists('[name="roles[' . $publicRole->id . ']"]');
    }

    public function test_public_role_visible_in_role_listing()
    {
        $this->asAdmin()->get('/settings/roles')
            ->assertSee('Admin')
            ->assertSee('Public');
    }

    public function test_public_role_visible_in_default_role_setting()
    {
        $resp = $this->asAdmin()->get('/settings/registration');
        $this->withHtml($resp)->assertElementExists('[data-system-role-name="admin"]')
            ->assertElementExists('[data-system-role-name="public"]');
    }

    public function test_public_role_not_deletable()
    {
        /** @var Role $publicRole */
        $publicRole = Role::getSystemRole('public');
        $resp = $this->asAdmin()->delete('/settings/roles/delete/' . $publicRole->id);
        $resp->assertRedirect('/');

        $this->get('/settings/roles/delete/' . $publicRole->id);
        $resp = $this->delete('/settings/roles/delete/' . $publicRole->id);
        $resp->assertRedirect('/settings/roles/delete/' . $publicRole->id);
        $resp = $this->get('/settings/roles/delete/' . $publicRole->id);
        $resp->assertSee('This role is a system role and cannot be deleted');
    }

    public function test_role_permission_removal()
    {
        // To cover issue fixed in f99c8ff99aee9beb8c692f36d4b84dc6e651e50a.
        $page = $this->entities->page();
        $viewerRole = Role::getRole('viewer');
        $viewer = $this->users->viewer();
        $this->actingAs($viewer)->get($page->getUrl())->assertOk();

        $this->asAdmin()->put('/settings/roles/' . $viewerRole->id, [
            'display_name' => $viewerRole->display_name,
            'description'  => $viewerRole->description,
            'permissions'  => [],
        ])->assertStatus(302);

        $this->actingAs($viewer)->get($page->getUrl())->assertStatus(404);
    }

    public function test_index_listing_sorting()
    {
        $this->asAdmin();
        $role = $this->users->createRole();
        $role->display_name = 'zz test role';
        $role->created_at = now()->addDays(1);
        $role->save();

        $runTest = function (string $order, string $direction, bool $expectFirstResult) use ($role) {
            setting()->putForCurrentUser('roles_sort', $order);
            setting()->putForCurrentUser('roles_sort_order', $direction);
            $html = $this->withHtml($this->get('/settings/roles'));
            $selector = ".item-list-row:first-child a[href$=\"/roles/{$role->id}\"]";
            if ($expectFirstResult) {
                $html->assertElementExists($selector);
            } else {
                $html->assertElementNotExists($selector);
            }
        };

        $runTest('name', 'asc', false);
        $runTest('name', 'desc', true);
        $runTest('created_at', 'desc', true);
        $runTest('created_at', 'asc', false);
    }
}
