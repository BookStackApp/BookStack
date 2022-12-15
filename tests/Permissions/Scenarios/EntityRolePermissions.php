<?php

namespace Tests\Permissions\Scenarios;

use BookStack\Entities\Models\Page;
use Tests\TestCase;

// Cases defined in dev/docs/permission-scenario-testing.md

class EntityRolePermissions extends TestCase
{
    public function test_01_explicit_allow()
    {
        $user = $this->getViewer();
        $role = $user->roles->first();
        $page = $this->entities->page();
        $this->entities->setPermissions($page, ['view'], [$role], false);

        $this->actingAs($user);
        $this->assertTrue(userCan('page-view', $page));
        $this->assertNotNull(Page::visible()->findOrFail($page->id));
    }

    public function test_02_explicit_deny()
    {
        $user = $this->getViewer();
        $role = $user->roles->first();
        $page = $this->entities->page();
        $this->entities->setPermissions($page, ['edit'], [$role], false);

        $this->actingAs($user);
        $this->assertFalse(userCan('page-view', $page));
        $this->assertNull(Page::visible()->find($page->id));
    }

    public function test_03_same_level_conflicting()
    {
        $user = $this->getViewer();
        $roleA = $user->roles->first();
        $roleB = $this->createNewRole();
        $user->attachRole($roleB);

        $page = $this->entities->page();
        // TODO - Can't do this as second call will overwrite first
        $this->entities->setPermissions($page, ['edit'], [$roleA], false);
        $this->entities->setPermissions($page, ['view'], [$roleB], false);

        $this->actingAs($user);
        $this->assertFalse(userCan('page-view', $page));
        $this->assertNull(Page::visible()->find($page->id));
    }
}
