<?php

namespace Tests\Commands;

use BookStack\Auth\Permissions\CollapsedPermission;
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
        $this->permissions->addEntityPermission($page, ['view'], null, $editor);
        CollapsedPermission::query()->truncate();

        $this->assertDatabaseMissing('entity_permissions_collapsed', ['entity_id' => $page->id]);

        $exitCode = Artisan::call('bookstack:regenerate-permissions');
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseHas('entity_permissions_collapsed', [
            'entity_id' => $page->id,
            'user_id' => $editor->id,
            'view' => 1,
        ]);

        CollapsedPermission::query()->truncate();
        DB::beginTransaction();
    }
}
