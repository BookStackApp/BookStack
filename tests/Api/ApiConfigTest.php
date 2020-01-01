<?php

namespace Tests;

use BookStack\Auth\Permissions\RolePermission;
use Carbon\Carbon;

class ApiConfigTest extends TestCase
{
    use TestsApi;

    protected $endpoint = '/api/books';

    public function test_default_item_count_reflected_in_listing_requests()
    {
        $this->actingAsApiEditor();

        config()->set(['api.default_item_count' => 5]);
        $resp = $this->get($this->endpoint);
        $resp->assertJsonCount(5, 'data');

        config()->set(['api.default_item_count' => 1]);
        $resp = $this->get($this->endpoint);
        $resp->assertJsonCount(1, 'data');
    }

    public function test_default_item_count_does_not_limit_count_param()
    {
        $this->actingAsApiEditor();
        config()->set(['api.default_item_count' => 1]);
        $resp = $this->get($this->endpoint . '?count=5');
        $resp->assertJsonCount(5, 'data');
    }

    public function test_max_item_count_limits_listing_requests()
    {
        $this->actingAsApiEditor();

        config()->set(['api.max_item_count' => 2]);
        $resp = $this->get($this->endpoint);
        $resp->assertJsonCount(2, 'data');

        $resp = $this->get($this->endpoint . '?count=5');
        $resp->assertJsonCount(2, 'data');
    }

}