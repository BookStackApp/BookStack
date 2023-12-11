<?php

namespace Tests\Auth;

use BookStack\Access\Mfa\MfaSession;
use Illuminate\Testing\TestResponse;
use Tests\TestCase;

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

    public function test_sign_up_link_on_login()
    {
        $this->get('/login')->assertDontSee('Sign up');

        $this->setSettings(['registration-enabled' => 'true']);

        $this->get('/login')->assertSee('Sign up');
    }

    public function test_logout()
    {
        $this->asAdmin()->get('/')->assertOk();
        $this->post('/logout')->assertRedirect('/');
        $this->get('/')->assertRedirect('/login');
    }

    public function test_mfa_session_cleared_on_logout()
    {
        $user = $this->users->editor();
        $mfaSession = $this->app->make(MfaSession::class);

        $mfaSession->markVerifiedForUser($user);
        $this->assertTrue($mfaSession->isVerifiedForUser($user));

        $this->asAdmin()->post('/logout');
        $this->assertFalse($mfaSession->isVerifiedForUser($user));
    }

    public function test_login_redirects_to_initially_requested_url_correctly()
    {
        config()->set('app.url', 'http://localhost');
        $page = $this->entities->page();

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
        $editor = $this->users->editor();
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
        $user = $this->users->editor();
        $user->email_confirmed = false;
        $user->save();

        auth()->login($user);
        $this->assertTrue(auth()->check());

        $this->get('/books')->assertRedirect('/');
        $this->assertFalse(auth()->check());
    }

    public function test_login_attempts_are_rate_limited()
    {
        for ($i = 0; $i < 5; $i++) {
            $resp = $this->login('bennynotexisting@example.com', 'pw123');
        }
        $resp = $this->followRedirects($resp);
        $resp->assertSee('These credentials do not match our records.');

        // Check the fifth attempt provides a lockout response
        $resp = $this->followRedirects($this->login('bennynotexisting@example.com', 'pw123'));
        $resp->assertSee('Too many login attempts. Please try again in');
    }

    /**
     * Perform a login.
     */
    protected function login(string $email, string $password): TestResponse
    {
        return $this->post('/login', compact('email', 'password'));
    }
}
