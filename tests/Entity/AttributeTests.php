<?php namespace Entity;

use BookStack\Attribute;
use BookStack\Page;
use BookStack\Services\PermissionService;

class AttributeTests extends \TestCase
{

    protected $defaultAttrCount = 20;

    /**
     * Get an instance of a page that has many attributes.
     * @param Attribute[]|bool $attributes
     * @return mixed
     */
    protected function getPageWithAttributes($attributes = false)
    {
        $page = Page::first();

        if (!$attributes) {
            $attributes = factory(Attribute::class, $this->defaultAttrCount)->make();
        }

        $page->attributes()->saveMany($attributes);
        return $page;
    }

    public function test_get_page_attributes()
    {
        $page = $this->getPageWithAttributes();

        // Add some other attributes to check they don't interfere
        factory(Attribute::class, $this->defaultAttrCount)->create();

        $this->asAdmin()->get("/ajax/attributes/get/page/" . $page->id)
            ->shouldReturnJson();

        $json = json_decode($this->response->getContent());
        $this->assertTrue(count($json) === $this->defaultAttrCount, "Returned JSON item count is not as expected");
    }

    public function test_attribute_name_suggestions()
    {
        // Create some attributes with similar names to test with
        $attrs = collect();
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'country']));
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'color']));
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'city']));
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'county']));
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'planet']));
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'plans']));
        $page = $this->getPageWithAttributes($attrs);

        $this->asAdmin()->get('/ajax/attributes/suggest?search=dog')->seeJsonEquals([]);
        $this->get('/ajax/attributes/suggest?search=co')->seeJsonEquals(['color', 'country', 'county']);
        $this->get('/ajax/attributes/suggest?search=cou')->seeJsonEquals(['country', 'county']);
        $this->get('/ajax/attributes/suggest?search=pla')->seeJsonEquals(['planet', 'plans']);
    }

    public function test_entity_permissions_effect_attribute_suggestions()
    {
        $permissionService = $this->app->make(PermissionService::class);

        // Create some attributes with similar names to test with and save to a page
        $attrs = collect();
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'country']));
        $attrs = $attrs->merge(factory(Attribute::class, 5)->make(['name' => 'color']));
        $page = $this->getPageWithAttributes($attrs);

        $this->asAdmin()->get('/ajax/attributes/suggest?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/attributes/suggest?search=co')->seeJsonEquals(['color', 'country']);

        // Set restricted permission the page
        $page->restricted = true;
        $page->save();
        $permissionService->buildJointPermissionsForEntity($page);

        $this->asAdmin()->get('/ajax/attributes/suggest?search=co')->seeJsonEquals(['color', 'country']);
        $this->asEditor()->get('/ajax/attributes/suggest?search=co')->seeJsonEquals([]);
    }

    public function test_entity_attribute_updating()
    {
        $page = $this->getPageWithAttributes();

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
        $this->asAdmin()->json("POST", "/ajax/attributes/update/page/" . $page->id, ['attributes' => $testJsonData]);
        $updateData = json_decode($this->response->getContent());
        // Check data is correct
        $testDataCorrect = true;
        foreach ($updateData->attributes as $data) {
            $testItem = ['name' => $data->name, 'value' => $data->value];
            if (!in_array($testItem, $testResponseJsonData)) $testDataCorrect = false;
        }
        $testMessage = "Expected data was not found in the response.\nExpected Data: %s\nRecieved Data: %s";
        $this->assertTrue($testDataCorrect, sprintf($testMessage, json_encode($testResponseJsonData), json_encode($updateData)));
        $this->assertTrue(isset($updateData->message), "No message returned in attribute update response");

        // Do get request
        $this->asAdmin()->get("/ajax/attributes/get/page/" . $page->id);
        $getResponseData = json_decode($this->response->getContent());
        // Check counts
        $this->assertTrue(count($getResponseData) === count($testJsonData), "The received attribute count is incorrect");
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
