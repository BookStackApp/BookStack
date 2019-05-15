<?php namespace Tests;

class HelpersTest extends TestCase
{

    public function test_base_url_takes_config_into_account()
    {
        config()->set('app.url', 'http://example.com/bookstack');
        $result = baseUrl('/');
        $this->assertEquals('http://example.com/bookstack/', $result);
    }

    public function test_base_url_takes_extra_path_into_account_on_forced_domain()
    {
        config()->set('app.url', 'http://example.com/bookstack');
        $result = baseUrl('http://example.com/bookstack/', true);
        $this->assertEquals('http://example.com/bookstack/', $result);
    }
}