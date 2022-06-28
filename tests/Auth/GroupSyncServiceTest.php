<?php

namespace Tests\Auth;

use BookStack\Auth\Access\GroupSyncService;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use Tests\TestCase;

class GroupSyncServiceTest extends TestCase
{
    public function test_user_is_assigned_to_matching_roles()
    {
        $user = $this->getViewer();

        $roleA = Role::factory()->create(['display_name' => 'Wizards']);
        $roleB = Role::factory()->create(['display_name' => 'Gremlins']);
        $roleC = Role::factory()->create(['display_name' => 'ABC123', 'external_auth_id' => 'sales']);
        $roleD = Role::factory()->create(['display_name' => 'DEF456', 'external_auth_id' => 'admin-team']);

        foreach ([$roleA, $roleB, $roleC, $roleD] as $role) {
            $this->assertFalse($user->hasRole($role->id));
        }

        (new GroupSyncService())->syncUserWithFoundGroups($user, ['Wizards', 'Gremlinz', 'Sales', 'Admin Team'], false);

        $user = User::query()->find($user->id);
        $this->assertTrue($user->hasRole($roleA->id));
        $this->assertFalse($user->hasRole($roleB->id));
        $this->assertTrue($user->hasRole($roleC->id));
        $this->assertTrue($user->hasRole($roleD->id));
    }

    public function test_multiple_values_in_role_external_auth_id_handled()
    {
        $user = $this->getViewer();
        $role = Role::factory()->create(['display_name' => 'ABC123', 'external_auth_id' => 'sales, engineering, developers, marketers']);
        $this->assertFalse($user->hasRole($role->id));

        (new GroupSyncService())->syncUserWithFoundGroups($user, ['Developers'], false);

        $user = User::query()->find($user->id);
        $this->assertTrue($user->hasRole($role->id));
    }

    public function test_commas_can_be_used_in_external_auth_id_if_escaped()
    {
        $user = $this->getViewer();
        $role = Role::factory()->create(['display_name' => 'ABC123', 'external_auth_id' => 'sales\,-developers, marketers']);
        $this->assertFalse($user->hasRole($role->id));

        (new GroupSyncService())->syncUserWithFoundGroups($user, ['Sales, Developers'], false);

        $user = User::query()->find($user->id);
        $this->assertTrue($user->hasRole($role->id));
    }

    public function test_external_auth_id_matches_ignoring_case()
    {
        $user = $this->getViewer();
        $role = Role::factory()->create(['display_name' => 'ABC123', 'external_auth_id' => 'WaRRioRs']);
        $this->assertFalse($user->hasRole($role->id));

        (new GroupSyncService())->syncUserWithFoundGroups($user, ['wArriors', 'penguiNs'], false);

        $user = User::query()->find($user->id);
        $this->assertTrue($user->hasRole($role->id));
    }
}
