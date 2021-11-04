<?php

namespace Tests\Entity;

use BookStack\Actions\Tag;
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
}
