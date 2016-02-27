<?php

class RolesTest extends TestCase
{
    protected $user;

    public function setUp()
    {
        parent::setUp();
    }

    protected function createNewRole()
    {
        return \BookStack\Role::forceCreate([
            'name' => 'test-role',
            'display_name' => 'Test Role',
            'description' => 'This is a role for testing'
        ]);
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

}
