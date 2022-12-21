<?php

namespace Tests\Permissions\Scenarios;

class EntityRolePermissionsTest extends PermissionScenarioTestCase
{
    public function test_01_explicit_allow()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->setEntityPermissions($page, ['view'], [$role], false);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_02_explicit_deny()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->setEntityPermissions($page, [], [$role], false);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_03_same_level_conflicting()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $roleB = $this->users->attachNewRole($user);
        $page = $this->entities->page();

        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, [], $roleA);
        $this->permissions->addEntityPermission($page, ['view'], $roleB);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_20_inherit_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, ['view'], $roleA);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_21_inherit_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, [], $roleA);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_22_same_level_conflict_inherit()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $roleB = $this->users->attachNewRole($user);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, [], $roleA);
        $this->permissions->addEntityPermission($chapter, ['view'], $roleB);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_30_child_inherit_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, [], $roleA);
        $this->permissions->addEntityPermission($page, ['view'], $roleA);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_31_child_inherit_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($chapter, ['view'], $roleA);
        $this->permissions->addEntityPermission($page, [], $roleA);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_40_multi_role_inherit_conflict_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $roleB = $this->users->attachNewRole($user);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($page, [], $roleA);
        $this->permissions->addEntityPermission($chapter, ['view'], $roleB);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_41_multi_role_inherit_conflict_retain_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $roleB = $this->users->attachNewRole($user);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->disableEntityInheritedPermissions($chapter);
        $this->permissions->addEntityPermission($page, ['view'], $roleA);
        $this->permissions->addEntityPermission($chapter, [], $roleB);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_50_role_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, ['view'], $roleA);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_51_role_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->page();
        $this->permissions->addEntityPermission($page, [], $roleA);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_60_inherited_role_override_allow()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], []);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, ['view'], $roleA);

        $this->assertVisibleToUser($page, $user);
    }

    public function test_61_inherited_role_override_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, [], $roleA);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_62_inherited_role_override_deny_on_own()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-own']);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;
        $this->permissions->addEntityPermission($chapter, [], $roleA);
        $this->permissions->changeEntityOwner($page, $user);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_70_multi_role_inheriting_deny()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $roleB = $this->users->attachNewRole($user);
        $page = $this->entities->page();

        $this->permissions->addEntityPermission($page, [], $roleB);

        $this->assertNotVisibleToUser($page, $user);
    }

    public function test_80_multi_role_inherited_deny_via_parent()
    {
        [$user, $roleA] = $this->users->newUserWithRole([], ['page-view-all']);
        $roleB = $this->users->attachNewRole($user);
        $page = $this->entities->pageWithinChapter();
        $chapter = $page->chapter;

        $this->permissions->addEntityPermission($chapter, [], $roleB);

        $this->assertNotVisibleToUser($page, $user);
    }
}
