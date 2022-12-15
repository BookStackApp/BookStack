<?php

namespace Tests\Permissions\Scenarios;

use BookStack\Entities\Models\Page;
use Tests\TestCase;

// Cases defined in dev/docs/permission-scenario-testing.md

class EntityRolePermissions extends TestCase
{
    public function test_01_explicit_allow()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->setEntityPermissions($page, ['view'], [$role], false);

        $this->actingAs($user);
        $this->assertTrue(userCan('page-view', $page));
        $this->assertNotNull(Page::visible()->findOrFail($page->id));
    }

    public function test_02_explicit_deny()
    {
        [$user, $role] = $this->users->newUserWithRole();
        $page = $this->entities->page();
        $this->permissions->setEntityPermissions($page, ['edit'], [$role], false);

        $this->actingAs($user);
        $this->assertFalse(userCan('page-view', $page));
        $this->assertNull(Page::visible()->find($page->id));
    }

    public function test_03_same_level_conflicting()
    {
        [$user, $roleA] = $this->users->newUserWithRole();
        $roleB = $this->users->attachRole($user);
        $page = $this->entities->page();

        $this->permissions->disableEntityInheritedPermissions($page);
        $this->permissions->addEntityPermission($page, ['update'], $roleA->id);
        $this->permissions->addEntityPermission($page, ['view'], $roleB->id);

        $this->actingAs($user);
        $this->assertTrue(userCan('page-view', $page));
        $this->assertNotNull(Page::visible()->find($page->id));
    }
}
