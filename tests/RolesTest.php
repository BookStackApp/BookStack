<?php

class RolesTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
    }

    /**
     * Create a new basic role for testing purposes.
     * @return static
     */
    protected function createNewRole()
    {
        $permissionRepo = app('BookStack\Repos\PermissionsRepo');
        return $permissionRepo->saveNewRole(factory(\BookStack\Role::class)->make()->toArray());
    }

    public function test_admin_can_see_settings()
    {
        $this->asAdmin()->visit('/settings')->see('Settings');
    }

    public function test_cannot_delete_admin_role()
    {
        $adminRole = \BookStack\Role::getRole('admin');
        $deletePageUrl = '/settings/roles/delete/' . $adminRole->id;
        $this->asAdmin()->visit($deletePageUrl)
            ->press('Confirm')
            ->seePageIs($deletePageUrl)
            ->see('cannot be deleted');
    }

    public function test_role_cannot_be_deleted_if_default()
    {
        $newRole = $this->createNewRole();
        $this->setSettings(['registration-role' => $newRole->id]);

        $deletePageUrl = '/settings/roles/delete/' . $newRole->id;
        $this->asAdmin()->visit($deletePageUrl)
            ->press('Confirm')
            ->seePageIs($deletePageUrl)
            ->see('cannot be deleted');
    }

    public function test_role_create_update_delete_flow()
    {
        $testRoleName = 'Test Role';
        $testRoleDesc = 'a little test description';
        $testRoleUpdateName = 'An Super Updated role';

        // Creation
        $this->asAdmin()->visit('/settings')
            ->click('Roles')
            ->seePageIs('/settings/roles')
            ->click('Add new role')
            ->type('Test Role', 'display_name')
            ->type('A little test description', 'description')
            ->press('Save Role')
            ->seeInDatabase('roles', ['display_name' => $testRoleName, 'name' => 'test-role', 'description' => $testRoleDesc])
            ->seePageIs('/settings/roles');
        // Updating
        $this->asAdmin()->visit('/settings/roles')
            ->see($testRoleDesc)
            ->click($testRoleName)
            ->type($testRoleUpdateName, '#display_name')
            ->press('Save Role')
            ->seeInDatabase('roles', ['display_name' => $testRoleUpdateName, 'name' => 'test-role', 'description' => $testRoleDesc])
            ->seePageIs('/settings/roles');
        // Deleting
        $this->asAdmin()->visit('/settings/roles')
            ->click($testRoleUpdateName)
            ->click('Delete Role')
            ->see($testRoleUpdateName)
            ->press('Confirm')
            ->seePageIs('/settings/roles')
            ->dontSee($testRoleUpdateName);
    }

}
