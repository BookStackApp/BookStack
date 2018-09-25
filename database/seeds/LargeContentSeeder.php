<?php

use Illuminate\Database\Seeder;

class LargeContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an editor user
        $editorUser = factory(\BookStack\Auth\User::class)->create();
        $editorRole = \BookStack\Auth\Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        $largeBook = factory(\BookStack\Entities\Book::class)->create(['name' => 'Large book' . str_random(10), 'created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $pages = factory(\BookStack\Entities\Page::class, 200)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $chapters = factory(\BookStack\Entities\Chapter::class, 50)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);
        app(\BookStack\Auth\Permissions\PermissionService::class)->buildJointPermissions();
        app(\BookStack\Entities\SearchService::class)->indexAllEntities();
    }
}
