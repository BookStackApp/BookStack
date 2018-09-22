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

        $byData = ['created_by' => $editorUser->id, 'updated_by' => $editorUser->id];

        factory(\BookStack\Book::class, 5)->create($byData)
            ->each(function($book) use ($editorUser, $byData) {
                $chapters = factory(\BookStack\Chapter::class, 3)->create($byData)
                    ->each(function($chapter) use ($editorUser, $book, $byData){
                        $pages = factory(\BookStack\Page::class, 3)->make(array_merge($byData, ['book_id' => $book->id]));
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = factory(\BookStack\Page::class, 3)->make($byData);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });

        $largeBook = factory(\BookStack\Book::class)->create(array_merge($byData, ['name' => 'Large book' . str_random(10)]));
        $pages = factory(\BookStack\Page::class, 200)->make($byData);
        $chapters = factory(\BookStack\Chapter::class, 50)->make($byData);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);

        $shelves = factory(\BookStack\Bookshelf::class, 10)->create($byData);
        $largeBook->shelves()->attach($shelves->pluck('id'));

        app(\BookStack\Services\PermissionService::class)->buildJointPermissions();
        app(\BookStack\Services\SearchService::class)->indexAllEntities();
    }
}
