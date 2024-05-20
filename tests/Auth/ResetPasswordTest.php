<?php

namespace Tests\Auth;

use BookStack\Access\Notifications\ResetPasswordNotification;
use BookStack\Users\Models\User;
use Carbon\CarbonInterval;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Sleep;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Sleep::fake();
    }

    public function test_reset_flow()
    {
        Notification::fake();

        $resp = $this->get('/login');
        $this->withHtml($resp)->assertElementContains('a[href="' . url('/password/email') . '"]', 'Forgot Password?');

        $resp = $this->get('/password/email');
        $this->withHtml($resp)->assertElementContains('form[action="' . url('/password/email') . '"]', 'Send Reset Link');

        $resp = $this->post('/password/email', [
            'email' => 'admin@admin.com',
        ]);
        $resp->assertRedirect('/password/email');

        $resp = $this->get('/password/email');
        $resp->assertSee('A password reset link will be sent to admin@admin.com if that email address is found in the system.');

        $this->assertDatabaseHas('password_resets', [
            'email' => 'admin@admin.com',
        ]);

        /** @var User $user */
        $user = User::query()->where('email', '=', 'admin@admin.com')->first();

        Notification::assertSentTo($user, ResetPasswordNotification::class);
        $n = Notification::sent($user, ResetPasswordNotification::class);

        $this->get('/password/reset/' . $n->first()->token)
            ->assertOk()
            ->assertSee('Reset Password');

        $resp = $this->post('/password/reset', [
            'email'                 => 'admin@admin.com',
            'password'              => 'randompass',
            'password_confirmation' => 'randompass',
            'token'                 => $n->first()->token,
        ]);
        $resp->assertRedirect('/');

        $this->get('/')->assertSee('Your password has been successfully reset');
    }

    public function test_reset_flow_shows_success_message_even_if_wrong_password_to_prevent_user_discovery()
    {
        $this->get('/password/email');
        $resp = $this->followingRedirects()->post('/password/email', [
            'email' => 'barry@admin.com',
        ]);
        $resp->assertSee('A password reset link will be sent to barry@admin.com if that email address is found in the system.');
        $resp->assertDontSee('We can\'t find a user');

        $this->get('/password/reset/arandometokenvalue')->assertSee('Reset Password');
        $resp = $this->post('/password/reset', [
            'email'                 => 'barry@admin.com',
            'password'              => 'randompass',
            'password_confirmation' => 'randompass',
            'token'                 => 'arandometokenvalue',
        ]);
        $resp->assertRedirect('/password/reset/arandometokenvalue');

        $this->get('/password/reset/arandometokenvalue')
            ->assertDontSee('We can\'t find a user')
            ->assertSee('The password reset token is invalid for this email address.');
    }

    public function test_reset_request_with_not_found_user_still_has_delay()
    {
        $this->followingRedirects()->post('/password/email', [
            'email' => 'barrynotfoundrandomuser@example.com',
        ]);

        Sleep::assertSlept(function (CarbonInterval $duration): bool {
            return $duration->totalMilliseconds > 999;
        }, 1);
    }

    public function test_reset_page_shows_sign_links()
    {
        $this->setSettings(['registration-enabled' => 'true']);
        $resp = $this->get('/password/email');
        $this->withHtml($resp)->assertElementContains('a', 'Log in')
            ->assertElementContains('a', 'Sign up');
    }

    public function test_reset_request_is_throttled()
    {
        $editor = $this->users->editor();
        Notification::fake();
        $this->get('/password/email');
        $this->followingRedirects()->post('/password/email', [
            'email' => $editor->email,
        ]);

        $resp = $this->followingRedirects()->post('/password/email', [
            'email' => $editor->email,
        ]);
        Notification::assertSentTimes(ResetPasswordNotification::class, 1);
        $resp->assertSee('A password reset link will be sent to ' . $editor->email . ' if that email address is found in the system.');
    }

    public function test_reset_request_with_not_found_user_is_throttled()
    {
        for ($i = 0; $i < 11; $i++) {
            $response = $this->post('/password/email', [
                'email' => 'barrynotfoundrandomuser@example.com',
            ]);
        }

        $response->assertStatus(429);
    }

    public function test_reset_call_is_throttled()
    {
        for ($i = 0; $i < 11; $i++) {
            $response = $this->post('/password/reset', [
                'email' => "arandomuser{$i}@example.com",
                'token' => "randomtoken{$i}",
            ]);
        }

        $response->assertStatus(429);
    }
}
