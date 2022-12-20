<?php

namespace Tests\Permissions\Scenarios;

class EntityUserPermissions extends PermissionScenarioTestCase
{
    public function test_01_explicit_allow()
    {
        $user = $this->users->newUser();
        $page = $this->entities->page();
        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_02_explicit_deny()
    {
        $user = $this->users->newUser();
        $page = $this->entities->page();
        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, [], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_10_allow_inherit()
    {
        $user = $this->users->newUser();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_11_deny_inherit()
    {
        $user = $this->users->newUser();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, [], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_12_allow_inherit_override()
    {
        $user = $this->users->newUser();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, [], null, $user);
        $this->permissions->addEntityPermission($page, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_13_deny_inherit_override()
    {
        $user = $this->users->newUser();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, ['view'], null, $user);
        $this->permissions->addEntityPermission($page, ['deny'], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_40_entity_role_override_allow()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, ['view'], null, $user);
        $this->permissions->addEntityPermission($page, [], $role);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_41_entity_role_override_deny()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, [], null, $user);
        $this->permissions->addEntityPermission($page, ['view'], $role);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_42_entity_role_override_allow_via_inherit()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, ['view'], null, $user);
        $this->permissions->addEntityPermission($page, [], $role);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_43_entity_role_override_deny_via_inherit()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, [], null, $user);
        $this->permissions->addEntityPermission($page, ['view'], $role);

        $this->assertNotVisibleToUser($page, $user);
    }
}
