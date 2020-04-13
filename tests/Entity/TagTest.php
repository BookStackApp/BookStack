<?php namespace Tests\Entity;

use BookStack\Entities\Book;
use BookStack\Entities\Chapter;
use BookStack\Actions\Tag;
use BookStack\Entities\Entity;
use BookStack\Entities\Page;
use BookStack\Auth\Permissions\PermissionService;
use Tests\BrowserKitTest;

class TagTest extends BrowserKitTest
{

    protected $defaultTagCount = 20;

    /**
     * Get an instance of a page that has many tags.
     * @param \BookStack\Actions\Tag[]|bool $tags
     * @return Entity
     */
    protected function getEntityWithTags($class, $tags = false): Entity
    {
        $entity = $class::first();

        if (!$tags) {
            $tags = factory(Tag::class, $this->defaultTagCount)->make();
        }

        $entity->tags()->saveMany($tags);
        return $entity;
    }

    public function test_get_page_tags()
    {
        $page = $this->getEntityWithTags(Page::class);

        // Add some other tags to check they don't interfere
        factory(Tag::class, $this->defaultTagCount)->create();

        $this->asAdmin()->get("/ajax/tags/get/page/" . $page->id)
            ->shouldReturnJson();

        $json = json_decode($this->response->getContent());
        $this->assertTrue(count($json) === $this->defaultTagCount, "Returned JSON item count is not as expected");
    }

    public function test_get_chapter_tags()
    {
        $chapter = $this->getEntityWithTags(Chapter::class);

        // Add some other tags to check they don't interfere
        factory(Tag::class, $this->defaultTagCount)->create();

        $this->asAdmin()->get("/ajax/tags/get/chapter/" . $chapter->id)
            ->shouldReturnJson();

        $json = json_decode($this->response->getContent());
        $this->assertTrue(count($json) === $this->defaultTagCount, "Returned JSON item count is not as expected");
    }

    public function test_get_book_tags()
    {
        $book = $this->getEntityWithTags(Book::class);

        // Add some other tags to check they don't interfere
        factory(Tag::class, $this->defaultTagCount)->create();

        $this->asAdmin()->get("/ajax/tags/get/book/" . $book->id)
            ->shouldReturnJson();

        $json = json_decode($this->response->getContent());
        $this->assertTrue(count($json) === $this->defaultTagCount, "Returned JSON item count is not as expected");
    }

    public function test_tag_name_suggestions()
    {
        // Create some tags with similar names to test with
        $attrs = collect();
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'country']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'color']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'city']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'county']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'planet']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'plans']));
        $page = $this->getEntityWithTags(Page::class, $attrs);

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=dog')->seeJsonEquals([]);
        $this->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country', 'county']);
        $this->get('/ajax/tags/suggest/names?search=cou')->seeJsonEquals(['country', 'county']);
        $this->get('/ajax/tags/suggest/names?search=pla')->seeJsonEquals(['planet', 'plans']);
    }

    public function test_tag_value_suggestions()
    {
        // Create some tags with similar values to test with
        $attrs = collect();
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'country', 'value' => 'cats']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'color', 'value' => 'cattery']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'city', 'value' => 'castle']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'county', 'value' => 'dog']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'planet', 'value' => 'catapult']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'plans', 'value' => 'dodgy']));
        $page = $this->getEntityWithTags(Page::class, $attrs);

        $this->asAdmin()->get('/ajax/tags/suggest/values?search=ora')->seeJsonEquals([]);
        $this->get('/ajax/tags/suggest/values?search=cat')->seeJsonEquals(['cats', 'cattery', 'catapult']);
        $this->get('/ajax/tags/suggest/values?search=do')->seeJsonEquals(['dog', 'dodgy']);
        $this->get('/ajax/tags/suggest/values?search=cas')->seeJsonEquals(['castle']);
    }

    public function test_entity_permissions_effect_tag_suggestions()
    {
        $permissionService = $this->app->make(PermissionService::class);

        // Create some tags with similar names to test with and save to a page
        $attrs = collect();
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'country']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'color']));
        $page = $this->getEntityWithTags(Page::class, $attrs);

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country']);

        // Set restricted permission the page
        $page->restricted = true;
        $page->save();
        $page->rebuildPermissions();

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals([]);
    }

}
