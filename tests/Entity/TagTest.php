<?php namespace Entity;

use BookStack\Tag;
use BookStack\Page;
use BookStack\Services\PermissionService;

class TagTest extends \TestCase
{

    protected $defaultTagCount = 20;

    /**
     * Get an instance of a page that has many tags.
     * @param Tag[]|bool $tags
     * @return mixed
     */
    protected function getPageWithTags($tags = false)
    {
        $page = Page::first();

        if (!$tags) {
            $tags = factory(Tag::class, $this->defaultTagCount)->make();
        }

        $page->tags()->saveMany($tags);
        return $page;
    }

    public function test_get_page_tags()
    {
        $page = $this->getPageWithTags();

        // Add some other tags to check they don't interfere
        factory(Tag::class, $this->defaultTagCount)->create();

        $this->asAdmin()->get("/ajax/tags/get/page/" . $page->id)
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
        $page = $this->getPageWithTags($attrs);

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
        $page = $this->getPageWithTags($attrs);

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
        $page = $this->getPageWithTags($attrs);

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country']);

        // Set restricted permission the page
        $page->restricted = true;
        $page->save();
        $permissionService->buildJointPermissionsForEntity($page);

        $this->asAdmin()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest/names?search=co')->seeJsonEquals([]);
    }

}
