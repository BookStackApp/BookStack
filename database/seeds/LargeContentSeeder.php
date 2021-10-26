<?php

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Entities\Tools\SearchIndex;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
        $editorUser = factory(User::class)->create();
        $editorRole = Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        $largeBook = factory(\BookStack\Entities\Models\Book::class)->create(['name' => 'Large book' . Str::random(10), 'created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $pages = factory(Page::class, 200)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $chapters = factory(Chapter::class, 50)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);
        app(PermissionService::class)->buildJointPermissions();
        app(SearchIndex::class)->indexAllEntities();
    }
}
