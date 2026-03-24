<?php

namespace Tests\Auth;

use Tests\TestCase;

class MultiAuthTest extends TestCase
{
    public function test_login_page_shows_multiple_configured_auth_methods()
    {
        config()->set([
            'auth.methods' => ['standard', 'oidc'],
            'auth.primary_method' => 'oidc',
            'oidc.name' => 'Entra ID',
        ]);
        $this->setSettings(['registration-enabled' => 'true']);

        $resp = $this->get('/login');

        $this->withHtml($resp)->assertElementExists('form[action$="/login"] input[name="login_method"][value="standard"]');
        $this->withHtml($resp)->assertElementExists('form[action$="/oidc/login"] button#oidc-login');
        $resp->assertSee('Sign up');
        $resp->assertSee('Entra ID');
    }

    public function test_standard_login_works_when_oidc_is_primary_method()
    {
        config()->set([
            'auth.methods' => ['standard', 'oidc'],
            'auth.primary_method' => 'oidc',
        ]);

        $resp = $this->post('/login', [
            'login_method' => 'standard',
            'email' => 'admin@admin.com',
            'password' => 'password',
        ]);

        $resp->assertRedirect('/');
        $this->assertTrue(auth()->check());
        $this->assertTrue(auth('standard')->check());
    }

    public function test_auto_initiate_does_not_run_when_multiple_methods_are_enabled()
    {
        config()->set([
            'auth.methods' => ['standard', 'oidc'],
            'auth.primary_method' => 'oidc',
            'auth.auto_initiate' => true,
        ]);

        $resp = $this->get('/login');

        $resp->assertDontSeeText('Attempting Login');
        $resp->assertSee('Log In');
    }

    public function test_header_logout_uses_session_auth_method()
    {
        config()->set([
            'auth.methods' => ['standard', 'oidc'],
            'auth.primary_method' => 'oidc',
        ]);

        $resp = $this->actingAs($this->users->admin())
            ->withSession(['auth-login-method' => 'oidc'])
            ->get('/');

        $this->withHtml($resp)->assertElementExists('form[action="' . url('/oidc/logout') . '"]');
    }
}
