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
        $shelf = $this->entities->shelf();
        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse($child->hasPermissions(), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->entities->setPermissions($shelf, ['view', 'update'], [$editorRole]);
        $this->artisan('bookstack:copy-shelf-permissions', [
            '--slug' => $shelf->slug,
        ]);
        $child = $shelf->books()->first();

        $this->assertTrue($child->hasPermissions(), 'Child book should now be restricted');
        $this->assertEquals(2, $child->permissions()->count(), 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', [
            'entity_type' => 'book',
            'entity_id' => $child->id,
            'role_id' => $editorRole->id,
            'view' => true, 'update' => true, 'create' => false, 'delete' => false,
        ]);
    }

    public function test_copy_shelf_permissions_command_using_all()
    {
        $shelf = $this->entities->shelf();
        Bookshelf::query()->where('id', '!=', $shelf->id)->delete();
        $child = $shelf->books()->first();
        $editorRole = $this->getEditor()->roles()->first();
        $this->assertFalse($child->hasPermissions(), 'Child book should not be restricted by default');
        $this->assertTrue($child->permissions()->count() === 0, 'Child book should have no permissions by default');

        $this->entities->setPermissions($shelf, ['view', 'update'], [$editorRole]);
        $this->artisan('bookstack:copy-shelf-permissions --all')
            ->expectsQuestion('Permission settings for all shelves will be cascaded. Books assigned to multiple shelves will receive only the permissions of it\'s last processed shelf. Are you sure you want to proceed?', 'y');
        $child = $shelf->books()->first();

        $this->assertTrue($child->hasPermissions(), 'Child book should now be restricted');
        $this->assertEquals(2, $child->permissions()->count(), 'Child book should have copied permissions');
        $this->assertDatabaseHas('entity_permissions', [
            'entity_type' => 'book',
            'entity_id' => $child->id,
            'role_id' => $editorRole->id,
            'view' => true, 'update' => true, 'create' => false, 'delete' => false,
        ]);
    }
}
