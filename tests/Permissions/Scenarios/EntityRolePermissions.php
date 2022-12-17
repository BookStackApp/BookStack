<?php

namespace Tests\Permissions\Scenarios;

use BookStack\Auth\User;
use BookStack\Entities\Models\Entity;
use Tests\TestCase;

// Cases defined in dev/docs/permission-scenario-testing.md

class EntityRolePermissions extends TestCase
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

    protected function assertVisibleToUser(Entity $entity, User $user)
    {
        $this->actingAs($user);
        $funcView = userCan($entity->getMorphClass() . '-view', $entity);
        $queryView = $entity->newQuery()->scopes(['visible'])->find($entity->id) !== null;

        $id = $entity->getMorphClass() . ':' . $entity->id;
        $msg = "Item [{$id}] should be visible but was not found via ";
        $msg .= implode(' and ', array_filter([!$funcView ? 'userCan' : '', !$queryView ? 'query' : '']));

        static::assertTrue($funcView && $queryView, $msg);
    }

    protected function assertNotVisibleToUser(Entity $entity, User $user)
    {
        $this->actingAs($user);
        $funcView = userCan($entity->getMorphClass() . '-view', $entity);
        $queryView = $entity->newQuery()->scopes(['visible'])->find($entity->id) !== null;

        $id = $entity->getMorphClass() . ':' . $entity->id;
        $msg = "Item [{$id}] should not be visible but was found via ";
        $msg .= implode(' and ', array_filter([$funcView ? 'userCan' : '', $queryView ? 'query' : '']));

        static::assertTrue(!$funcView && !$queryView, $msg);
    }
}
