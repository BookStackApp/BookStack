<?php

namespace Tests;

use BookStack\Notifications\TestEmail;
use Illuminate\Contracts\Notifications\Dispatcher;
use Illuminate\Support\Facades\Notification;

class TestEmailTest extends TestCase
{
    public function test_a_send_test_button_shows()
    {
        $pageView = $this->asAdmin()->get('/settings/maintenance');
        $formCssSelector = 'form[action$="/settings/maintenance/send-test-email"]';
        $this->withHtml($pageView)->assertElementExists($formCssSelector);
        $this->withHtml($pageView)->assertElementContains($formCssSelector . ' button', 'Send Test Email');
    }

    public function test_send_test_email_endpoint_sends_email_and_redirects_user_and_shows_notification()
    {
        Notification::fake();
        $admin = $this->getAdmin();

        $sendReq = $this->actingAs($admin)->post('/settings/maintenance/send-test-email');
        $sendReq->assertRedirect('/settings/maintenance#image-cleanup');
        $this->assertSessionHas('success', 'Email sent to ' . $admin->email);

        Notification::assertSentTo($admin, TestEmail::class);
    }

    public function test_send_test_email_failure_displays_error_notification()
    {
        $mockDispatcher = $this->mock(Dispatcher::class);
        $this->app[Dispatcher::class] = $mockDispatcher;

        $exception = new \Exception('A random error occurred when testing an email');
        $mockDispatcher->shouldReceive('sendNow')->andThrow($exception);

        $admin = $this->getAdmin();
        $sendReq = $this->actingAs($admin)->post('/settings/maintenance/send-test-email');
        $sendReq->assertRedirect('/settings/maintenance#image-cleanup');
        $this->assertSessionHas('error');

        $message = session()->get('error');
        $this->assertStringContainsString('Error thrown when sending a test email:', $message);
        $this->assertStringContainsString('A random error occurred when testing an email', $message);
    }

    public function test_send_test_email_requires_settings_manage_permission()
    {
        Notification::fake();
        $user = $this->getViewer();

        $sendReq = $this->actingAs($user)->post('/settings/maintenance/send-test-email');
        Notification::assertNothingSent();

        $this->giveUserPermissions($user, ['settings-manage']);
        $sendReq = $this->actingAs($user)->post('/settings/maintenance/send-test-email');
        Notification::assertSentTo($user, TestEmail::class);
    }
}
