<?php

namespace Tests\Commands;

use BookStack\Entities\Models\Bookshelf;
use Tests\TestCase;

class CopyShelfPermissionsCommandTest extends TestCase
{
    public function test_copy_shelf_permissions_command_shows_error_when_no_required_option_given()
    {
        $this->artisan('bookstack:copy-shelf-permissions')
            ->expectsOutput('Either a --slug or --all option must be provided.')
            ->assertExitCode(0);
    }

    public function test_copy_shelf_permissions_command_using_slug()
    {
        $shelf = Bookshelf::first();
        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->setEntityRestrictions($shelf, ['view', 'update'], [$editorRole]);
        $this->artisan('bookstack:copy-shelf-permissions', [
            '--slug' => $shelf->slug,
        ]);
        $child = $shelf->books()->first();

        $this->assertTrue(boolval($child->restricted), 'Child book should now be restricted');
        $this->assertTrue($child->permissions()->count() === 2, 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }

    public function test_copy_shelf_permissions_command_using_all()
    {
        $shelf = Bookshelf::query()->first();
        Bookshelf::query()->where('id', '!=', $shelf->id)->delete();
        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse(boolval($child->restricted), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->setEntityRestrictions($shelf, ['view', 'update'], [$editorRole]);
        $this->artisan('bookstack:copy-shelf-permissions --all')
            ->expectsQuestion('Permission settings for all shelves will be cascaded. Books assigned to multiple shelves will receive only the permissions of it\'s last processed shelf. Are you sure you want to proceed?', 'y');
        $child = $shelf->books()->first();

        $this->assertTrue(boolval($child->restricted), 'Child book should now be restricted');
        $this->assertTrue($child->permissions()->count() === 2, 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'view', 'role_id' => $editorRole->id]);
        $this->assertDatabaseHas('entity_permissions', ['restrictable_id' => $child->id, 'action' => 'update', 'role_id' => $editorRole->id]);
    }
}
