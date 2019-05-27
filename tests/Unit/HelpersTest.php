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

    public function test_base_url_force_domain_works_as_expected_with_full_url_given()
    {
        config()->set('app.url', 'http://example.com');
        $result = baseUrl('http://examps.com/books/test/page/cat', true);
        $this->assertEquals('http://example.com/books/test/page/cat', $result);
    }

    public function test_base_url_force_domain_works_when_app_domain_is_same_as_given_url()
    {
        config()->set('app.url', 'http://example.com');
        $result = baseUrl('http://example.com/books/test/page/cat', true);
        $this->assertEquals('http://example.com/books/test/page/cat', $result);
    }
}