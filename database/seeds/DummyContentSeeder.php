<?php

use Illuminate\Database\Seeder;

class DummyContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        // Create an editor user
        $editorUser = factory(\BookStack\User::class)->create();
        $editorRole = \BookStack\Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        // Create a viewer user
        $viewerUser = factory(\BookStack\User::class)->create();
        $role = \BookStack\Role::getRole('viewer');
        $viewerUser->attachRole($role);

        factory(\BookStack\Book::class, 5)->create(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id])
            ->each(function($book) use ($editorUser) {
                $chapters = factory(\BookStack\Chapter::class, 3)->create(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id])
                    ->each(function($chapter) use ($editorUser, $book){
                        $pages = factory(\BookStack\Page::class, 3)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id, 'book_id' => $book->id]);
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = factory(\BookStack\Page::class, 3)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });

        $largeBook = factory(\BookStack\Book::class)->create(['name' => 'Large book' . str_random(10), 'created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $pages = factory(\BookStack\Page::class, 200)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $chapters = factory(\BookStack\Chapter::class, 50)->make(['created_by' => $editorUser->id, 'updated_by' => $editorUser->id]);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);
        app(\BookStack\Services\PermissionService::class)->buildJointPermissions();
        app(\BookStack\Services\SearchService::class)->indexAllEntities();
    }
}
