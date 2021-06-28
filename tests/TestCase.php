<?php

namespace Tests;

use BookStack\Entities\Models\Entity;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use SharedTestHelpers;

    /**
     * The base URL to use while testing the application.
     *
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Assert the session contains a specific entry.
     *
     * @param string $key
     *
     * @return $this
     */
    protected function assertSessionHas(string $key)
    {
        $this->assertTrue(session()->has($key), "Session does not contain a [{$key}] entry");

        return $this;
    }

    /**
     * Override of the get method so we can get visibility of custom TestResponse methods.
     *
     * @param string $uri
     * @param array  $headers
     *
     * @return TestResponse
     */
    public function get($uri, array $headers = [])
    {
        return parent::get($uri, $headers);
    }

    /**
     * Create the test response instance from the given response.
     *
     * @param \Illuminate\Http\Response $response
     *
     * @return TestResponse
     */
    protected function createTestResponse($response)
    {
        return TestResponse::fromBaseResponse($response);
    }

    /**
     * Assert that an activity entry exists of the given key.
     * Checks the activity belongs to the given entity if provided.
     */
    protected function assertActivityExists(string $type, Entity $entity = null)
    {
        $detailsToCheck = ['type' => $type];

        if ($entity) {
            $detailsToCheck['entity_type'] = $entity->getMorphClass();
            $detailsToCheck['entity_id'] = $entity->id;
        }

        $this->assertDatabaseHas('activities', $detailsToCheck);
    }
}
