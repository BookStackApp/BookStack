<?php

namespace Database\Seeders;

use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Book;
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
        $editorUser = User::factory()->create();
        $editorRole = Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        /** @var Book $largeBook */
        $largeBook = Book::factory()->create(['name' => 'Large book' . Str::random(10), 'created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $pages = Page::factory()->count(200)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $chapters = Chapter::factory()->count(50)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);

        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);

        app()->make(PermissionService::class)->buildJointPermissions();
        app()->make(SearchIndex::class)->indexAllEntities();
    }
}
