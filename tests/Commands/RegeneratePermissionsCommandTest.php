<?php

namespace Tests\Commands;

use BookStack\Auth\Permissions\CollapsedPermission;
use BookStack\Permissions\Models\JointPermission;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class RegeneratePermissionsCommandTest extends TestCase
{
    public function test_regen_permissions_command()
    {
        DB::rollBack();
        $page = $this->entities->page();
        $editor = $this->users->editor();
        $role = $editor->roles()->first();
        $this->permissions->addEntityPermission($page, ['view'], $role);
        JointPermission::query()->truncate();

        $this->assertDatabaseMissing('joint_permissions', ['entity_id' => $page->id]);

        $exitCode = Artisan::call('bookstack:regenerate-permissions');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseHas('joint_permissions', [
            'entity_id' => $page->id,
            'entity_type' => 'page',
            'role_id' => $role->id,
            'status' => 3, // Explicit allow
        ]);

        $page->permissions()->delete();
        $page->rebuildPermissions();

        DB::beginTransaction();
    }
}
