<?php

namespace Tests\Permissions\Scenarios;

class EntityUserPermissionsTest extends PermissionScenarioTestCase
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

    public function test_50_role_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_51_role_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, [], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_60_inherited_role_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], []);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_61_inherited_role_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, [], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_61_inherited_role_override_deny_on_own()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-own']);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, [], null, $user);
        $this->permissions->changeEntityOwner($page, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_70_all_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], []);
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, [], $roleA, null);
        $this->permissions->addEntityPermission($page, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_71_all_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, ['view'], $roleA, null);
        $this->permissions->addEntityPermission($page, [], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_80_inherited_all_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], []);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, [], $roleA, null);
        $this->permissions->addEntityPermission($chapter, ['view'], null, $user);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_81_inherited_all_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, ['view'], $roleA, null);
        $this->permissions->addEntityPermission($chapter, [], null, $user);

        $this->assertNotVisibleToUser($page, $user);
    }
}
