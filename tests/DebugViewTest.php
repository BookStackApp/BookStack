<?php

namespace Tests;

use BookStack\Auth\Access\SocialAuthService;

class DebugViewTest extends TestCase
{
    public function test_debug_view_shows_expected_details()
    {
        config()->set('app.debug', true);
        $resp = $this->getDebugViewForException(new \InvalidArgumentException('An error occurred during testing'));

        // Error message
        $resp->assertSeeText('An error occurred during testing');
        // Exception Class
        $resp->assertSeeText('InvalidArgumentException');
        // Stack trace
        $resp->assertSeeText('#0');
        $resp->assertSeeText('#1');
        // Warning message
        $resp->assertSeeText('WARNING: Application is in debug mode. This mode has the potential to leak');
        // PHP version
        $resp->assertSeeText('PHP Version: ' . phpversion());
        // BookStack version
        $resp->assertSeeText('BookStack Version: ' . trim(file_get_contents(base_path('version'))));
        // Dynamic help links
        $resp->assertElementExists('a[href*="q=' . urlencode('BookStack An error occurred during testing') . '"]');
        $resp->assertElementExists('a[href*="?q=is%3Aissue+' . urlencode('An error occurred during testing') . '"]');
    }

    public function test_debug_view_only_shows_when_debug_mode_is_enabled()
    {
        config()->set('app.debug', true);
        $resp = $this->getDebugViewForException(new \InvalidArgumentException('An error occurred during testing'));
        $resp->assertSeeText('Stack Trace');
        $resp->assertDontSeeText('An unknown error occurred');

        config()->set('app.debug', false);
        $resp = $this->getDebugViewForException(new \InvalidArgumentException('An error occurred during testing'));
        $resp->assertDontSeeText('Stack Trace');
        $resp->assertSeeText('An unknown error occurred');
    }


    protected function getDebugViewForException(\Exception $exception): TestResponse
    {
        // Fake an error via social auth service used on login page
        $mockService = $this->mock(SocialAuthService::class);
        $mockService->shouldReceive('getActiveDrivers')->andThrow($exception);
        return $this->get('/login');
    }

}