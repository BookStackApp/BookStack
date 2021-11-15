<?php

namespace Tests\Entity;

use BookStack\Actions\Tag;
use BookStack\Entities\Models\Book;
use BookStack\Entities\Models\Entity;
use BookStack\Entities\Models\Page;
use Tests\TestCase;

class TagTest extends TestCase
{
    protected $defaultTagCount = 20;

    /**
     * Get an instance of a page that has many tags.
     */
    protected function getEntityWithTags($class, ?array $tags = null): Entity
    {
        $entity = $class::first();

        if (is_null($tags)) {
            $tags = Tag::factory()->count($this->defaultTagCount)->make();
        }

        $entity->tags()->saveMany($tags);

        return $entity;
    }

    public function test_tag_name_suggestions()
    {
        // Create some tags with similar names to test with
        $attrs = collect();
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'country']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'color']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'city']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'county']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'planet']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'plans']));
        $page = $this->getEntityWithTags(Page::class, $attrs->all());

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=dog')->assertSimilarJson([]);
        $this->get('/ajax/tags/suggest/names?search=co')->assertSimilarJson(['color', 'country', 'county']);
        $this->get('/ajax/tags/suggest/names?search=cou')->assertSimilarJson(['country', 'county']);
        $this->get('/ajax/tags/suggest/names?search=pla')->assertSimilarJson(['planet', 'plans']);
    }

    public function test_tag_value_suggestions()
    {
        // Create some tags with similar values to test with
        $attrs = collect();
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'country', 'value' => 'cats']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'color', 'value' => 'cattery']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'city', 'value' => 'castle']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'county', 'value' => 'dog']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'planet', 'value' => 'catapult']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'plans', 'value' => 'dodgy']));
        $page = $this->getEntityWithTags(Page::class, $attrs->all());

        $this->asAdmin()->get('/ajax/tags/suggest/values?search=ora')->assertSimilarJson([]);
        $this->get('/ajax/tags/suggest/values?search=cat')->assertSimilarJson(['cats', 'cattery', 'catapult']);
        $this->get('/ajax/tags/suggest/values?search=do')->assertSimilarJson(['dog', 'dodgy']);
        $this->get('/ajax/tags/suggest/values?search=cas')->assertSimilarJson(['castle']);
    }

    public function test_entity_permissions_effect_tag_suggestions()
    {
        // Create some tags with similar names to test with and save to a page
        $attrs = collect();
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'country']));
        $attrs = $attrs->merge(Tag::factory()->count(5)->make(['name' => 'color']));
        $page = $this->getEntityWithTags(Page::class, $attrs->all());

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=co')->assertSimilarJson(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest/names?search=co')->assertSimilarJson(['color', 'country']);

        // Set restricted permission the page
        $page->restricted = true;
        $page->save();
        $page->rebuildPermissions();

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=co')->assertSimilarJson(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest/names?search=co')->assertSimilarJson([]);
    }

    public function test_tags_shown_on_search_listing()
    {
        $tags = [
            Tag::factory()->make(['name' => 'category', 'value' => 'buckets']),
            Tag::factory()->make(['name' => 'color', 'value' => 'red']),
        ];

        $page = $this->getEntityWithTags(Page::class, $tags);
        $resp = $this->asEditor()->get('/search?term=[category]');
        $resp->assertSee($page->name);
        $resp->assertElementContains('[href="' . $page->getUrl() . '"]', 'category');
        $resp->assertElementContains('[href="' . $page->getUrl() . '"]', 'buckets');
        $resp->assertElementContains('[href="' . $page->getUrl() . '"]', 'color');
        $resp->assertElementContains('[href="' . $page->getUrl() . '"]', 'red');
    }

    public function test_tags_index_shows_tag_name_as_expected_with_right_counts()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->tags()->create(['name' => 'Category', 'value' => 'GreatTestContent']);
        $page->tags()->create(['name' => 'Category', 'value' => 'OtherTestContent']);

        $resp = $this->asEditor()->get('/tags');
        $resp->assertSee('Category');
        $resp->assertElementCount('.tag-item', 1);
        $resp->assertDontSee('GreatTestContent');
        $resp->assertDontSee('OtherTestContent');
        $resp->assertElementContains('a[title="Total tag usages"]', '2');
        $resp->assertElementContains('a[title="Assigned to Pages"]', '2');
        $resp->assertElementContains('a[title="Assigned to Books"]', '0');
        $resp->assertElementContains('a[title="Assigned to Chapters"]', '0');
        $resp->assertElementContains('a[title="Assigned to Shelves"]', '0');
        $resp->assertElementContains('a[href$="/tags?name=Category"]', '2 unique values');

        /** @var Book $book */
        $book = Book::query()->first();
        $book->tags()->create(['name' => 'Category', 'value' => 'GreatTestContent']);
        $resp = $this->asEditor()->get('/tags');
        $resp->assertElementContains('a[title="Total tag usages"]', '3');
        $resp->assertElementContains('a[title="Assigned to Books"]', '1');
        $resp->assertElementContains('a[href$="/tags?name=Category"]', '2 unique values');
    }

    public function test_tag_index_can_be_searched()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->tags()->create(['name' => 'Category', 'value' => 'GreatTestContent']);

        $resp = $this->asEditor()->get('/tags?search=cat');
        $resp->assertElementContains('.tag-item .tag-name', 'Category');

        $resp = $this->asEditor()->get('/tags?search=content');
        $resp->assertElementContains('.tag-item .tag-name', 'Category');
        $resp->assertElementContains('.tag-item .tag-value', 'GreatTestContent');

        $resp = $this->asEditor()->get('/tags?search=other');
        $resp->assertElementNotExists('.tag-item .tag-name');
    }

    public function test_tag_index_search_will_show_mulitple_values_of_a_single_tag_name()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->tags()->create(['name' => 'Animal', 'value' => 'Catfish']);
        $page->tags()->create(['name' => 'Animal', 'value' => 'Catdog']);

        $resp = $this->asEditor()->get('/tags?search=cat');
        $resp->assertElementContains('.tag-item .tag-value', 'Catfish');
        $resp->assertElementContains('.tag-item .tag-value', 'Catdog');
    }

    public function test_tag_index_can_be_scoped_to_specific_tag_name()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->tags()->create(['name' => 'Category', 'value' => 'GreatTestContent']);
        $page->tags()->create(['name' => 'Category', 'value' => 'OtherTestContent']);
        $page->tags()->create(['name' => 'OtherTagName', 'value' => 'OtherValue']);

        $resp = $this->asEditor()->get('/tags?name=Category');
        $resp->assertSee('Category');
        $resp->assertSee('GreatTestContent');
        $resp->assertSee('OtherTestContent');
        $resp->assertDontSee('OtherTagName');
        $resp->assertElementCount('table .tag-item', 2);
        $resp->assertSee('Active Filter:');
        $resp->assertElementContains('form[action$="/tags"]', 'Clear Filter');
    }

    public function test_tags_index_adheres_to_page_permissions()
    {
        /** @var Page $page */
        $page = Page::query()->first();
        $page->tags()->create(['name' => 'SuperCategory', 'value' => 'GreatTestContent']);

        $resp = $this->asEditor()->get('/tags');
        $resp->assertSee('SuperCategory');
        $resp = $this->get('/tags?name=SuperCategory');
        $resp->assertSee('GreatTestContent');

        $page->restricted = true;
        $this->regenEntityPermissions($page);

        $resp = $this->asEditor()->get('/tags');
        $resp->assertDontSee('SuperCategory');
        $resp = $this->get('/tags?name=SuperCategory');
        $resp->assertDontSee('GreatTestContent');
    }

    public function test_tag_index_shows_message_on_no_results()
    {
        /** @var Page $page */
        $resp = $this->asEditor()->get('/tags?search=testingval');
        $resp->assertSee('No items available');
        $resp->assertSee('Tags can be assigned via the page editor sidebar');
    }
}
