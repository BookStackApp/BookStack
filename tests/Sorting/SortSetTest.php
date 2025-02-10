<?php

namespace Sorting;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Sorting\SortSet;
use Tests\Api\TestsApi;
use Tests\TestCase;

class SortSetTest extends TestCase
{
    use TestsApi;

    public function test_manage_settings_permission_required()
    {
        $set = SortSet::factory()->create();
        $user = $this->users->viewer();
        $this->actingAs($user);

        $actions = [
            ['GET', '/settings/sorting'],
            ['POST', '/settings/sorting/sets'],
            ['GET', "/settings/sorting/sets/{$set->id}"],
            ['PUT', "/settings/sorting/sets/{$set->id}"],
            ['DELETE', "/settings/sorting/sets/{$set->id}"],
        ];

        foreach ($actions as [$method, $path]) {
            $resp = $this->call($method, $path);
            $this->assertPermissionError($resp);
        }

        $this->permissions->grantUserRolePermissions($user, ['settings-manage']);

        foreach ($actions as [$method, $path]) {
            $resp = $this->call($method, $path);
            $this->assertNotPermissionError($resp);
        }
    }

    public function test_create_flow()
    {
        $resp = $this->asAdmin()->get('/settings/sorting');
        $this->withHtml($resp)->assertLinkExists(url('/settings/sorting/sets/new'));

        $resp = $this->get('/settings/sorting/sets/new');
        $this->withHtml($resp)->assertElementExists('form[action$="/settings/sorting/sets"] input[name="name"]');
        $resp->assertSeeText('Name - Alphabetical (Asc)');

        $details = ['name' => 'My new sort', 'sequence' => 'name_asc'];
        $resp = $this->post('/settings/sorting/sets', $details);
        $resp->assertRedirect('/settings/sorting');

        $this->assertActivityExists(ActivityType::SORT_SET_CREATE);
        $this->assertDatabaseHas('sort_sets', $details);
    }

    public function test_listing_in_settings()
    {
        $set = SortSet::factory()->create(['name' => 'My super sort set', 'sequence' => 'name_asc']);
        $books = Book::query()->limit(5)->get();
        foreach ($books as $book) {
            $book->sort_set_id = $set->id;
            $book->save();
        }

        $resp = $this->asAdmin()->get('/settings/sorting');
        $resp->assertSeeText('My super sort set');
        $resp->assertSeeText('Name - Alphabetical (Asc)');
        $this->withHtml($resp)->assertElementContains('.item-list-row [title="Assigned to 5 Books"]', '5');
    }

    public function test_update_flow()
    {
        $set = SortSet::factory()->create(['name' => 'My sort set to update', 'sequence' => 'name_asc']);

        $resp = $this->asAdmin()->get("/settings/sorting/sets/{$set->id}");
        $respHtml = $this->withHtml($resp);
        $respHtml->assertElementContains('.configured-option-list', 'Name - Alphabetical (Asc)');
        $respHtml->assertElementNotContains('.available-option-list', 'Name - Alphabetical (Asc)');

        $updateData = ['name' => 'My updated sort', 'sequence' => 'name_desc,chapters_last'];
        $resp = $this->put("/settings/sorting/sets/{$set->id}", $updateData);

        $resp->assertRedirect('/settings/sorting');
        $this->assertActivityExists(ActivityType::SORT_SET_UPDATE);
        $this->assertDatabaseHas('sort_sets', $updateData);
    }

    public function test_update_triggers_resort_on_assigned_books()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $set = SortSet::factory()->create(['name' => 'My sort set to update', 'sequence' => 'name_asc']);
        $book->sort_set_id = $set->id;
        $book->save();
        $chapter->priority = 10000;
        $chapter->save();

        $resp = $this->asAdmin()->put("/settings/sorting/sets/{$set->id}", ['name' => $set->name, 'sequence' => 'chapters_last']);
        $resp->assertRedirect('/settings/sorting');

        $chapter->refresh();
        $this->assertNotEquals(10000, $chapter->priority);
    }

    public function test_delete_flow()
    {
        $set = SortSet::factory()->create();

        $resp = $this->asAdmin()->get("/settings/sorting/sets/{$set->id}");
        $resp->assertSeeText('Delete Sort Set');

        $resp = $this->delete("settings/sorting/sets/{$set->id}");
        $resp->assertRedirect('/settings/sorting');

        $this->assertActivityExists(ActivityType::SORT_SET_DELETE);
        $this->assertDatabaseMissing('sort_sets', ['id' => $set->id]);
    }

    public function test_delete_requires_confirmation_if_books_assigned()
    {
        $set = SortSet::factory()->create();
        $books = Book::query()->limit(5)->get();
        foreach ($books as $book) {
            $book->sort_set_id = $set->id;
            $book->save();
        }

        $resp = $this->asAdmin()->get("/settings/sorting/sets/{$set->id}");
        $resp->assertSeeText('Delete Sort Set');

        $resp = $this->delete("settings/sorting/sets/{$set->id}");
        $resp->assertRedirect("/settings/sorting/sets/{$set->id}#delete");
        $resp = $this->followRedirects($resp);

        $resp->assertSeeText('This sort set is currently used on 5 book(s). Are you sure you want to delete this?');
        $this->assertDatabaseHas('sort_sets', ['id' => $set->id]);

        $resp = $this->delete("settings/sorting/sets/{$set->id}", ['confirm' => 'true']);
        $resp->assertRedirect('/settings/sorting');
        $this->assertDatabaseMissing('sort_sets', ['id' => $set->id]);
        $this->assertDatabaseMissing('books', ['sort_set_id' => $set->id]);
    }

    public function test_page_create_triggers_book_sort()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $set = SortSet::factory()->create(['sequence' => 'name_asc,chapters_first']);
        $book->sort_set_id = $set->id;
        $book->save();

        $resp = $this->actingAsApiEditor()->post("/api/pages", [
            'book_id' => $book->id,
            'name' => '1111 page',
            'markdown' => 'Hi'
        ]);
        $resp->assertOk();

        $this->assertDatabaseHas('pages', [
            'book_id' => $book->id,
            'name' => '1111 page',
            'priority' => $book->chapters()->count() + 1,
        ]);
    }

    public function test_name_numeric_ordering()
    {
        $book = Book::factory()->create();
        $set = SortSet::factory()->create(['sequence' => 'name_numeric_asc']);
        $book->sort_set_id = $set->id;
        $book->save();
        $this->permissions->regenerateForEntity($book);

        $namesToAdd = [
            "1 - Pizza",
            "2.0 - Tomato",
            "2.5 - Beans",
            "10 - Bread",
            "20 - Milk",
        ];

        foreach ($namesToAdd as $name) {
            $this->actingAsApiEditor()->post("/api/pages", [
                'book_id' => $book->id,
                'name' => $name,
                'markdown' => 'Hello'
            ]);
        }

        foreach ($namesToAdd as $index => $name) {
            $this->assertDatabaseHas('pages', [
                'book_id' => $book->id,
                'name' => $name,
                'priority' => $index + 1,
            ]);
        }
    }
}
