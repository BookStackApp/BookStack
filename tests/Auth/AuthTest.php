<?php

namespace Tests\Auth;

use BookStack\Auth\Access\Mfa\MfaSession;
use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
use BookStack\Notifications\ConfirmEmail;
use BookStack\Notifications\ResetPassword;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;
use Tests\TestResponse;

class AuthTest extends TestCase
{
    public function test_auth_working()
    {
        $this->get('/')->assertRedirect('/login');
    }

    public function test_login()
    {
        $this->login('admin@admin.com', 'password')->assertRedirect('/');
    }

    public function test_public_viewing()
    {
        $this->setSettings(['app-public' => 'true']);
        $this->get('/')
            ->assertOk()
            ->assertSee('Log in');
    }

    public function test_registration_showing()
    {
        // Ensure registration form is showing
        $this->setSettings(['registration-enabled' => 'true']);
        $this->get('/login')
            ->assertElementContains('a[href="' . url('/register') . '"]', 'Sign up');
    }

    public function test_normal_registration()
    {
        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true']);
        $user = User::factory()->make();

        // Test form and ensure user is created
        $this->get('/register')
            ->assertSee('Sign Up')
            ->assertElementContains('form[action="' . url('/register') . '"]', 'Create Account');

        $resp = $this->post('/register', $user->only('password', 'name', 'email'));
        $resp->assertRedirect('/');

        $resp = $this->get('/');
        $resp->assertOk();
        $resp->assertSee($user->name);
        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email]);
    }

    public function test_empty_registration_redirects_back_with_errors()
    {
        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true']);

        // Test form and ensure user is created
        $this->get('/register');
        $this->post('/register', [])->assertRedirect('/register');
        $this->get('/register')->assertSee('The name field is required');
    }

    public function test_registration_validation()
    {
        $this->setSettings(['registration-enabled' => 'true']);

        $this->get('/register');
        $resp = $this->followingRedirects()->post('/register', [
            'name'     => '1',
            'email'    => '1',
            'password' => '1',
        ]);
        $resp->assertSee('The name must be at least 2 characters.');
        $resp->assertSee('The email must be a valid email address.');
        $resp->assertSee('The password must be at least 8 characters.');
    }

    public function test_sign_up_link_on_login()
    {
        $this->get('/login')->assertDontSee('Sign up');

        $this->setSettings(['registration-enabled' => 'true']);

        $this->get('/login')->assertSee('Sign up');
    }

    public function test_confirmed_registration()
    {
        // Fake notifications
        Notification::fake();

        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'true']);
        $user = User::factory()->make();

        // Go through registration process
        $resp = $this->post('/register', $user->only('name', 'email', 'password'));
        $resp->assertRedirect('/register/confirm');
        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => false]);

        // Ensure notification sent
        /** @var User $dbUser */
        $dbUser = User::query()->where('email', '=', $user->email)->first();
        Notification::assertSentTo($dbUser, ConfirmEmail::class);

        // Test access and resend confirmation email
        $resp = $this->login($user->email, $user->password);
        $resp->assertRedirect('/register/confirm/awaiting');

        $resp = $this->get('/register/confirm/awaiting');
        $resp->assertElementContains('form[action="' . url('/register/confirm/resend') . '"]', 'Resend');

        $this->get('/books')->assertRedirect('/login');
        $this->post('/register/confirm/resend', $user->only('email'));

        // Get confirmation and confirm notification matches
        $emailConfirmation = DB::table('email_confirmations')->where('user_id', '=', $dbUser->id)->first();
        Notification::assertSentTo($dbUser, ConfirmEmail::class, function ($notification, $channels) use ($emailConfirmation) {
            return $notification->token === $emailConfirmation->token;
        });

        // Check confirmation email confirmation activation.
        $this->get('/register/confirm/' . $emailConfirmation->token)->assertRedirect('/');
        $this->get('/')->assertSee($user->name);
        $this->assertDatabaseMissing('email_confirmations', ['token' => $emailConfirmation->token]);
        $this->assertDatabaseHas('users', ['name' => $dbUser->name, 'email' => $dbUser->email, 'email_confirmed' => true]);
    }

    public function test_restricted_registration()
    {
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'true', 'registration-restrict' => 'example.com']);
        $user = User::factory()->make();

        // Go through registration process
        $this->post('/register', $user->only('name', 'email', 'password'))
            ->assertRedirect('/register');
        $resp = $this->get('/register');
        $resp->assertSee('That email domain does not have access to this application');
        $this->assertDatabaseMissing('users', $user->only('email'));

        $user->email = 'barry@example.com';

        $this->post('/register', $user->only('name', 'email', 'password'))
            ->assertRedirect('/register/confirm');
        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => false]);

        $this->assertNull(auth()->user());

        $this->get('/')->assertRedirect('/login');
        $resp = $this->followingRedirects()->post('/login', $user->only('email', 'password'));
        $resp->assertSee('Email Address Not Confirmed');
        $this->assertNull(auth()->user());
    }

    public function test_restricted_registration_with_confirmation_disabled()
    {
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'false', 'registration-restrict' => 'example.com']);
        $user = User::factory()->make();

        // Go through registration process
        $this->post('/register', $user->only('name', 'email', 'password'))
            ->assertRedirect('/register');
        $this->assertDatabaseMissing('users', $user->only('email'));
        $this->get('/register')->assertSee('That email domain does not have access to this application');

        $user->email = 'barry@example.com';

        $this->post('/register', $user->only('name', 'email', 'password'))
            ->assertRedirect('/register/confirm');
        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => false]);

        $this->assertNull(auth()->user());

        $this->get('/')->assertRedirect('/login');
        $resp = $this->post('/login', $user->only('email', 'password'));
        $resp->assertRedirect('/register/confirm/awaiting');
        $this->get('/register/confirm/awaiting')->assertSee('Email Address Not Confirmed');
        $this->assertNull(auth()->user());
    }

    public function test_logout()
    {
        $this->asAdmin()->get('/')->assertOk();
        $this->get('/logout')->assertRedirect('/');
        $this->get('/')->assertRedirect('/login');
    }

    public function test_mfa_session_cleared_on_logout()
    {
        $user = $this->getEditor();
        $mfaSession = $this->app->make(MfaSession::class);

        $mfaSession->markVerifiedForUser($user);
        $this->assertTrue($mfaSession->isVerifiedForUser($user));

        $this->asAdmin()->get('/logout');
        $this->assertFalse($mfaSession->isVerifiedForUser($user));
    }

    public function test_reset_password_flow()
    {
        Notification::fake();

        $this->get('/login')
            ->assertElementContains('a[href="' . url('/password/email') . '"]', 'Forgot Password?');

        $this->get('/password/email')
            ->assertElementContains('form[action="' . url('/password/email') . '"]', 'Send Reset Link');

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

        Notification::assertSentTo($user, ResetPassword::class);
        $n = Notification::sent($user, ResetPassword::class);

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

    public function test_reset_password_flow_shows_success_message_even_if_wrong_password_to_prevent_user_discovery()
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

    public function test_reset_password_page_shows_sign_links()
    {
        $this->setSettings(['registration-enabled' => 'true']);
        $this->get('/password/email')
            ->assertElementContains('a', 'Log in')
            ->assertElementContains('a', 'Sign up');
    }

    public function test_reset_password_request_is_throttled()
    {
        $editor = $this->getEditor();
        Notification::fake();
        $this->get('/password/email');
        $this->followingRedirects()->post('/password/email', [
            'email' => $editor->email,
        ]);

        $resp = $this->followingRedirects()->post('/password/email', [
            'email' => $editor->email,
        ]);
        Notification::assertTimesSent(1, ResetPassword::class);
        $resp->assertSee('A password reset link will be sent to ' . $editor->email . ' if that email address is found in the system.');
    }

    public function test_login_redirects_to_initially_requested_url_correctly()
    {
        config()->set('app.url', 'http://localhost');
        /** @var Page $page */
        $page = Page::query()->first();

        $this->get($page->getUrl())->assertRedirect(url('/login'));
        $this->login('admin@admin.com', 'password')
            ->assertRedirect($page->getUrl());
    }

    public function test_login_intended_redirect_does_not_redirect_to_external_pages()
    {
        config()->set('app.url', 'http://localhost');
        $this->setSettings(['app-public' => true]);

        $this->get('/login', ['referer' => 'https://example.com']);
        $login = $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);

        $login->assertRedirect('http://localhost');
    }

    public function test_login_intended_redirect_does_not_factor_mfa_routes()
    {
        $this->get('/books')->assertRedirect('/login');
        $this->get('/mfa/setup')->assertRedirect('/login');
        $login = $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $login->assertRedirect('/books');
    }

    public function test_login_authenticates_admins_on_all_guards()
    {
        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $this->assertTrue(auth()->check());
        $this->assertTrue(auth('ldap')->check());
        $this->assertTrue(auth('saml2')->check());
        $this->assertTrue(auth('oidc')->check());
    }

    public function test_login_authenticates_nonadmins_on_default_guard_only()
    {
        $editor = $this->getEditor();
        $editor->password = bcrypt('password');
        $editor->save();

        $this->post('/login', ['email' => $editor->email, 'password' => 'password']);
        $this->assertTrue(auth()->check());
        $this->assertFalse(auth('ldap')->check());
        $this->assertFalse(auth('saml2')->check());
        $this->assertFalse(auth('oidc')->check());
    }

    public function test_failed_logins_are_logged_when_message_configured()
    {
        $log = $this->withTestLogger();
        config()->set(['logging.failed_login.message' => 'Failed login for %u']);

        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'cattreedog']);
        $this->assertTrue($log->hasWarningThatContains('Failed login for admin@example.com'));

        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $this->assertFalse($log->hasWarningThatContains('Failed login for admin@admin.com'));
    }

    public function test_logged_in_user_with_unconfirmed_email_is_logged_out()
    {
        $this->setSettings(['registration-confirmation' => 'true']);
        $user = $this->getEditor();
        $user->email_confirmed = false;
        $user->save();

        auth()->login($user);
        $this->assertTrue(auth()->check());

        $this->get('/books')->assertRedirect('/');
        $this->assertFalse(auth()->check());
    }

    /**
     * Perform a login.
     */
    protected function login(string $email, string $password): TestResponse
    {
        return $this->post('/login', compact('email', 'password'));
    }
}
