<?php namespace Entity;

use BookStack\Tag;
use BookStack\Page;
use BookStack\Services\PermissionService;

class TagTests extends \TestCase
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

        $this->asAdmin()->get('/ajax/tags/suggest?search=dog')->seeJsonEquals([]);
        $this->get('/ajax/tags/suggest?search=co')->seeJsonEquals(['color', 'country', 'county']);
        $this->get('/ajax/tags/suggest?search=cou')->seeJsonEquals(['country', 'county']);
        $this->get('/ajax/tags/suggest?search=pla')->seeJsonEquals(['planet', 'plans']);
    }

    public function test_entity_permissions_effect_tag_suggestions()
    {
        $permissionService = $this->app->make(PermissionService::class);

        // Create some tags with similar names to test with and save to a page
        $attrs = collect();
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'country']));
        $attrs = $attrs->merge(factory(Tag::class, 5)->make(['name' => 'color']));
        $page = $this->getPageWithTags($attrs);

        $this->asAdmin()->get('/ajax/tags/suggest?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest?search=co')->seeJsonEquals(['color', 'country']);

        // Set restricted permission the page
        $page->restricted = true;
        $page->save();
        $permissionService->buildJointPermissionsForEntity($page);

        $this->asAdmin()->get('/ajax/tags/suggest?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/tags/suggest?search=co')->seeJsonEquals([]);
    }

    public function test_entity_tag_updating()
    {
        $page = $this->getPageWithTags();

        $testJsonData = [
            ['name' => 'color', 'value' => 'red'],
            ['name' => 'color', 'value' => ' blue '],
            ['name' => 'city', 'value' => 'London '],
            ['name' => 'country', 'value' => ' England'],
        ];
        $testResponseJsonData = [
            ['name' => 'color', 'value' => 'red'],
            ['name' => 'color', 'value' => 'blue'],
            ['name' => 'city', 'value' => 'London'],
            ['name' => 'country', 'value' => 'England'],
        ];

        // Do update request
        $this->asAdmin()->json("POST", "/ajax/tags/update/page/" . $page->id, ['tags' => $testJsonData]);
        $updateData = json_decode($this->response->getContent());
        // Check data is correct
        $testDataCorrect = true;
        foreach ($updateData->tags as $data) {
            $testItem = ['name' => $data->name, 'value' => $data->value];
            if (!in_array($testItem, $testResponseJsonData)) $testDataCorrect = false;
        }
        $testMessage = "Expected data was not found in the response.\nExpected Data: %s\nRecieved Data: %s";
        $this->assertTrue($testDataCorrect, sprintf($testMessage, json_encode($testResponseJsonData), json_encode($updateData)));
        $this->assertTrue(isset($updateData->message), "No message returned in tag update response");

        // Do get request
        $this->asAdmin()->get("/ajax/tags/get/page/" . $page->id);
        $getResponseData = json_decode($this->response->getContent());
        // Check counts
        $this->assertTrue(count($getResponseData) === count($testJsonData), "The received tag count is incorrect");
        // Check data is correct
        $testDataCorrect = true;
        foreach ($getResponseData as $data) {
            $testItem = ['name' => $data->name, 'value' => $data->value];
            if (!in_array($testItem, $testResponseJsonData)) $testDataCorrect = false;
        }
        $testMessage = "Expected data was not found in the response.\nExpected Data: %s\nRecieved Data: %s";
        $this->assertTrue($testDataCorrect, sprintf($testMessage, json_encode($testResponseJsonData), json_encode($getResponseData)));
    }

}
