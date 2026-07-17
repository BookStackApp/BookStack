<?php

namespace Tests;

use Exception;
use Illuminate\Cache\ArrayStore;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Mockery;

class StatusTest extends TestCase
{
    public function test_returns_json_with_expected_results()
    {
        $resp = $this->get('/status');
        $resp->assertStatus(200);
        $resp->assertJson([
            'database' => true,
            'cache'    => true,
            'session'  => true,
        ]);
    }

    public function test_returns_500_status_and_false_on_db_error()
    {
        DB::shouldReceive('table')->andThrow(new Exception());

        $resp = $this->get('/status');
        $resp->assertStatus(500);
        $resp->assertJson([
            'database' => false,
        ]);
    }

    public function test_returns_500_status_and_false_on_wrong_cache_return()
    {
        $mockStore = Mockery::mock(new ArrayStore())->makePartial();
        Cache::swap($mockStore);
        $mockStore->shouldReceive('pull')->andReturn('cat');

        $resp = $this->get('/status');
        $resp->assertStatus(500);
        $resp->assertJson([
            'cache' => false,
        ]);
    }

    public function test_returns_500_status_and_false_on_wrong_session_return()
    {
        $session = Session::getFacadeRoot();
        $mockSession = Mockery::mock($session)->makePartial();
        Session::swap($mockSession);
        $mockSession->shouldReceive('get')->andReturn('cat');

        $resp = $this->get('/status');
        $resp->assertStatus(500);
        $resp->assertJson([
            'session' => false,
        ]);
    }
}
