<?php namespace Tests;

class UrlTest extends TestCase
{

    public function test_request_url_takes_custom_url_into_account()
    {
        config()->set('app.url', 'http://example.com/bookstack');
        $this->get('/');
        $this->assertEquals('http://example.com/bookstack', request()->getUri());

        config()->set('app.url', 'http://example.com/docs/content');
        $this->get('/');
        $this->assertEquals('http://example.com/docs/content', request()->getUri());
    }

    public function test_url_helper_takes_custom_url_into_account()
    {
        putenv('APP_URL=http://example.com/bookstack');
        $this->refreshApplication();
        $this->assertEquals('http://example.com/bookstack/books', url('/books'));
        putenv('APP_URL=');
    }

}