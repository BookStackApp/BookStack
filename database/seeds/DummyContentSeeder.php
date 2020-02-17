<?php

use BookStack\Api\ApiToken;
use BookStack\Auth\Permissions\PermissionService;
use BookStack\Auth\Permissions\RolePermission;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Bookshelf;
use BookStack\Entities\Chapter;
use BookStack\Entities\Page;
use BookStack\Entities\SearchService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

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
        $editorUser = factory(User::class)->create();
        $editorRole = Role::getRole('editor');
        $editorUser->attachRole($editorRole);

        // Create a viewer user
        $viewerUser = factory(User::class)->create();
        $role = Role::getRole('viewer');
        $viewerUser->attachRole($role);

        $byData = ['created_by' => $editorUser->id, 'updated_by' => $editorUser->id];

        factory(\BookStack\Entities\Book::class, 5)->create($byData)
            ->each(function($book) use ($editorUser, $byData) {
                $chapters = factory(Chapter::class, 3)->create($byData)
                    ->each(function($chapter) use ($editorUser, $book, $byData){
                        $pages = factory(Page::class, 3)->make(array_merge($byData, ['book_id' => $book->id]));
                        $chapter->pages()->saveMany($pages);
                    });
                $pages = factory(Page::class, 3)->make($byData);
                $book->chapters()->saveMany($chapters);
                $book->pages()->saveMany($pages);
            });

        $largeBook = factory(\BookStack\Entities\Book::class)->create(array_merge($byData, ['name' => 'Large book' . Str::random(10)]));
        $pages = factory(Page::class, 200)->make($byData);
        $chapters = factory(Chapter::class, 50)->make($byData);
        $largeBook->pages()->saveMany($pages);
        $largeBook->chapters()->saveMany($chapters);

        $shelves = factory(Bookshelf::class, 10)->create($byData);
        $largeBook->shelves()->attach($shelves->pluck('id'));

        // Assign API permission to editor role and create an API key
        $apiPermission = RolePermission::getByName('access-api');
        $editorRole->attachPermission($apiPermission);
        $token = (new ApiToken())->forceFill([
            'user_id' => $editorUser->id,
            'name' => 'Testing API key',
            'expires_at' => ApiToken::defaultExpiry(),
            'secret' => Hash::make('password'),
            'token_id' => 'apitoken',
        ]);
        $token->save();

        app(PermissionService::class)->buildJointPermissions();
        app(SearchService::class)->indexAllEntities();
    }
}
