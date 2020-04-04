<?php namespace Tests\Unit;

use Tests\TestCase;

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
        $this->runWithEnv('APP_URL', 'http://example.com/bookstack', function() {
            $this->assertEquals('http://example.com/bookstack/books', url('/books'));
        });
    }

    public function test_url_helper_sets_correct_scheme_even_when_request_scheme_is_different()
    {
        $this->runWithEnv('APP_URL', 'https://example.com/', function() {
            $this->get('http://example.com/login')->assertSee('https://example.com/dist/styles.css');
        });
    }

}