<?php

namespace Tests\Sorting;

use BookStack\Activity\ActivityType;
use BookStack\Entities\Models\Book;
use BookStack\Sorting\SortRule;
use BookStack\Sorting\SortRuleOperation;
use Tests\Api\TestsApi;
use Tests\TestCase;

class SortRuleTest extends TestCase
{
    use TestsApi;

    public function test_manage_settings_permission_required()
    {
        $rule = SortRule::factory()->create();
        $user = $this->users->viewer();
        $this->actingAs($user);

        $actions = [
            ['GET', '/settings/sorting'],
            ['POST', '/settings/sorting/rules'],
            ['GET', "/settings/sorting/rules/{$rule->id}"],
            ['PUT', "/settings/sorting/rules/{$rule->id}"],
            ['DELETE', "/settings/sorting/rules/{$rule->id}"],
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
        $this->withHtml($resp)->assertLinkExists(url('/settings/sorting/rules/new'));

        $resp = $this->get('/settings/sorting/rules/new');
        $this->withHtml($resp)->assertElementExists('form[action$="/settings/sorting/rules"] input[name="name"]');
        $resp->assertSeeText('Name - Alphabetical (Asc)');

        $details = ['name' => 'My new sort', 'sequence' => 'name_asc'];
        $resp = $this->post('/settings/sorting/rules', $details);
        $resp->assertRedirect('/settings/sorting');

        $this->assertActivityExists(ActivityType::SORT_RULE_CREATE);
        $this->assertDatabaseHas('sort_rules', $details);
    }

    public function test_listing_in_settings()
    {
        $rule = SortRule::factory()->create(['name' => 'My super sort rule', 'sequence' => 'name_asc']);
        $books = Book::query()->limit(5)->get();
        foreach ($books as $book) {
            $book->sort_rule_id = $rule->id;
            $book->save();
        }

        $resp = $this->asAdmin()->get('/settings/sorting');
        $resp->assertSeeText('My super sort rule');
        $resp->assertSeeText('Name - Alphabetical (Asc)');
        $this->withHtml($resp)->assertElementContains('.item-list-row [title="Assigned to 5 Books"]', '5');
    }

    public function test_update_flow()
    {
        $rule = SortRule::factory()->create(['name' => 'My sort rule to update', 'sequence' => 'name_asc']);

        $resp = $this->asAdmin()->get("/settings/sorting/rules/{$rule->id}");
        $respHtml = $this->withHtml($resp);
        $respHtml->assertElementContains('.configured-option-list', 'Name - Alphabetical (Asc)');
        $respHtml->assertElementNotContains('.available-option-list', 'Name - Alphabetical (Asc)');

        $updateData = ['name' => 'My updated sort', 'sequence' => 'name_desc,chapters_last'];
        $resp = $this->put("/settings/sorting/rules/{$rule->id}", $updateData);

        $resp->assertRedirect('/settings/sorting');
        $this->assertActivityExists(ActivityType::SORT_RULE_UPDATE);
        $this->assertDatabaseHas('sort_rules', $updateData);
    }

    public function test_update_triggers_resort_on_assigned_books()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $chapter = $book->chapters()->first();
        $rule = SortRule::factory()->create(['name' => 'My sort rule to update', 'sequence' => 'name_asc']);
        $book->sort_rule_id = $rule->id;
        $book->save();
        $chapter->priority = 10000;
        $chapter->save();

        $resp = $this->asAdmin()->put("/settings/sorting/rules/{$rule->id}", ['name' => $rule->name, 'sequence' => 'chapters_last']);
        $resp->assertRedirect('/settings/sorting');

        $chapter->refresh();
        $this->assertNotEquals(10000, $chapter->priority);
    }

    public function test_delete_flow()
    {
        $rule = SortRule::factory()->create();

        $resp = $this->asAdmin()->get("/settings/sorting/rules/{$rule->id}");
        $resp->assertSeeText('Delete Sort Rule');

        $resp = $this->delete("settings/sorting/rules/{$rule->id}");
        $resp->assertRedirect('/settings/sorting');

        $this->assertActivityExists(ActivityType::SORT_RULE_DELETE);
        $this->assertDatabaseMissing('sort_rules', ['id' => $rule->id]);
    }

    public function test_delete_requires_confirmation_if_books_assigned()
    {
        $rule = SortRule::factory()->create();
        $books = Book::query()->limit(5)->get();
        foreach ($books as $book) {
            $book->sort_rule_id = $rule->id;
            $book->save();
        }

        $resp = $this->asAdmin()->get("/settings/sorting/rules/{$rule->id}");
        $resp->assertSeeText('Delete Sort Rule');

        $resp = $this->delete("settings/sorting/rules/{$rule->id}");
        $resp->assertRedirect("/settings/sorting/rules/{$rule->id}#delete");
        $resp = $this->followRedirects($resp);

        $resp->assertSeeText('This sort rule is currently used on 5 book(s). Are you sure you want to delete this?');
        $this->assertDatabaseHas('sort_rules', ['id' => $rule->id]);

        $resp = $this->delete("settings/sorting/rules/{$rule->id}", ['confirm' => 'true']);
        $resp->assertRedirect('/settings/sorting');
        $this->assertDatabaseMissing('sort_rules', ['id' => $rule->id]);
        $this->assertDatabaseMissing('books', ['sort_rule_id' => $rule->id]);
    }

    public function test_page_create_triggers_book_sort()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $rule = SortRule::factory()->create(['sequence' => 'name_asc,chapters_first']);
        $book->sort_rule_id = $rule->id;
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

    public function test_auto_book_sort_does_not_touch_timestamps()
    {
        $book = $this->entities->bookHasChaptersAndPages();
        $rule = SortRule::factory()->create(['sequence' => 'name_asc,chapters_first']);
        $book->sort_rule_id = $rule->id;
        $book->save();
        $page = $book->pages()->first();
        $chapter = $book->chapters()->first();

        $resp = $this->actingAsApiEditor()->put("/api/pages/{$page->id}", [
            'name' => '1111 page',
        ]);
        $resp->assertOk();

        $oldTime = $chapter->updated_at->unix();
        $oldPriority = $chapter->priority;
        $chapter->refresh();
        $this->assertEquals($oldTime, $chapter->updated_at->unix());
        $this->assertNotEquals($oldPriority, $chapter->priority);
    }

    public function test_name_alphabetical_ordering()
    {
        $book = Book::factory()->create();
        $rule = SortRule::factory()->create(['sequence' => 'name_asc']);
        $book->sort_rule_id = $rule->id;
        $book->save();
        $this->permissions->regenerateForEntity($book);

        $namesToAdd = [
            "Beans",
            "bread",
            "Éclaire",
            "egg",
            "É😀ire",
            "É🫠ire",
            "Milk",
            "pizza",
            "Tomato",
        ];

        $reverseNamesToAdd = array_reverse($namesToAdd);
        foreach ($reverseNamesToAdd as $name) {
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

    public function test_name_numeric_ordering()
    {
        $book = Book::factory()->create();
        $rule = SortRule::factory()->create(['sequence' => 'name_numeric_asc']);
        $book->sort_rule_id = $rule->id;
        $book->save();
        $this->permissions->regenerateForEntity($book);

        $namesToAdd = [
            "1 - Pizza",
            "2.0 - Tomato",
            "2.5 - Beans",
            "10 - Bread",
            "20 - Milk",
        ];

        $reverseNamesToAdd = array_reverse($namesToAdd);
        foreach ($reverseNamesToAdd as $name) {
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

    public function test_each_sort_rule_operation_has_a_comparison_function()
    {
        $operations = SortRuleOperation::cases();

        foreach ($operations as $operation) {
            $comparisonFunc = $operation->getSortFunction();
            $this->assertIsCallable($comparisonFunc);
        }
    }
}
