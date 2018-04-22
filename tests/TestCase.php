<?php namespace Tests;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\TestCase as BaseTestCase;
use Illuminate\Foundation\Testing\TestResponse;

abstract class TestCase extends BaseTestCase
{
    use CreatesApplication;
    use DatabaseTransactions;
    use SharedTestHelpers;
    /**
     * The base URL to use while testing the application.
     * @var string
     */
    protected $baseUrl = 'http://localhost';

    /**
     * Assert a permission error has occurred.
     * @param TestResponse $response
     */
    protected function assertPermissionError(TestResponse $response)
    {
        $response->assertRedirect('/');
        $this->assertTrue(session()->has('error'));
        session()->remove('error');
    }
}