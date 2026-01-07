<?php

namespace Database\Seeders;

use BookStack\Api\ApiToken;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Bookshelf;
use BookStack\Entities\Models\Chapter;
use BookStack\Entities\Models\Page;
use BookStack\Permissions\JointPermissionBuilder;
use BookStack\Permissions\Models\RolePermission;
use BookStack\Search\SearchIndex;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Seeder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyContentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create an editor user
        $editorUser = User::factory()->create();
        $editorRole = Role::getRole('editor');
        $additionalEditorPerms = ['receive-notifications', 'comment-create-all'];
        $editorRole->permissions()->syncWithoutDetaching(RolePermission::whereIn('name', $additionalEditorPerms)->pluck('id'));
        $editorUser->attachRole($editorRole);

        // Create a viewer user
        $viewerUser = User::factory()->create();
        $role = Role::getRole('viewer');
        $viewerUser->attachRole($role);

        $byData = ['created_by' => $editorUser->id, 'updated_by' => $editorUser->id, 'owned_by' => $editorUser->id];

        Book::factory()->count(5)->make($byData)
            ->each(function ($book) use ($byData) {
                $book->save();
                $chapters = Chapter::factory()->count(3)->create($byData)
                    ->each(function ($chapter) use ($book, $byData) {
                        $pages = Page::factory()->count(3)->make(array_merge($byData, ['book_id' => $book->id]));
                        $this->saveManyOnRelation($pages, $chapter->pages());
                    });
                $pages = Page::factory()->count(3)->make($byData);
                $this->saveManyOnRelation($chapters, $book->chapters());
                $this->saveManyOnRelation($pages, $book->pages());
            });

        $largeBook = Book::factory()->make(array_merge($byData, ['name' => 'Large book' . Str::random(10)]));
        $largeBook->save();

        $pages = Page::factory()->count(200)->make($byData);
        $chapters = Chapter::factory()->count(50)->make($byData);
        $this->saveManyOnRelation($pages, $largeBook->pages());
        $this->saveManyOnRelation($chapters, $largeBook->chapters());

        $shelves = Bookshelf::factory()->count(10)->make($byData);
        foreach ($shelves as $shelf) {
            $shelf->save();
        }

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

        app(JointPermissionBuilder::class)->rebuildForAll();
        app(SearchIndex::class)->indexAllEntities();
    }

    /**
     * Inefficient workaround for saving many on a relation since we can't directly insert
     * entities since we split them across tables.
     */
    protected function saveManyOnRelation(Collection $entities, HasMany $relation): void
    {
        foreach ($entities as $entity) {
            $relation->save($entity);
        }
    }
}
