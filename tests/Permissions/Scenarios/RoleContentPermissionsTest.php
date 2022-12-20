<?php

namespace Tests\Permissions\Scenarios;

class RoleContentPermissionsTest extends PermissionScenarioTestCase
{
    public function test_01_allow()
    {
        [$user] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->page();

        $this->assertVisibleToUser($page, $user);
    }

    public function test_02_deny()
    {
        [$user] = $this->users->newUserWithRole([], []);
        $page = $this->entities->page();

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_10_allow_on_own_with_own()
    {
        [$user] = $this->users->newUserWithRole([], ['page-view-own']);
        $page = $this->entities->page();
        $this->permissions->changeEntityOwner($page, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_11_deny_on_other_with_own()
    {
        [$user] = $this->users->newUserWithRole([], ['page-view-own']);
        $page = $this->entities->page();
        $this->permissions->changeEntityOwner($page, $this->users->editor());

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_20_multiple_role_conflicting_all()
    {
        [$user] = $this->users->newUserWithRole([], ['page-view-all']);
        $this->users->attachNewRole($user, []);
        $page = $this->entities->page();

        $this->assertVisibleToUser($page, $user);
    }

    public function test_21_multiple_role_conflicting_own()
    {
        [$user] = $this->users->newUserWithRole([], ['page-view-own']);
        $this->users->attachNewRole($user, []);
        $page = $this->entities->page();
        $this->permissions->changeEntityOwner($page, $user);

        $this->assertVisibleToUser($page, $user);
    }
}
