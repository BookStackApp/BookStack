<?php

namespace Tests\Auth;

use Tests\TestCase;

class LoginAutoInitiateTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set([
            'auth.auto_initiate'        => true,
            'services.google.client_id' => false,
            'services.github.client_id' => false,
        ]);
    }

    public function test_with_oidc()
    {
        config()->set([
            'auth.method' => 'oidc',
        ]);

        $req = $this->get('/login');
        $req->assertSeeText('Attempting Login');
        $this->withHtml($req)->assertElementExists('form[action$="/oidc/login"][method=POST][id="login-form"] button');
        $this->withHtml($req)->assertElementExists('button[form="login-form"]');
    }

    public function test_with_saml2()
    {
        config()->set([
            'auth.method' => 'saml2',
        ]);

        $req = $this->get('/login');
        $req->assertSeeText('Attempting Login');
        $this->withHtml($req)->assertElementExists('form[action$="/saml2/login"][method=POST][id="login-form"] button');
        $this->withHtml($req)->assertElementExists('button[form="login-form"]');
    }

    public function test_it_does_not_run_if_social_provider_is_active()
    {
        config()->set([
            'auth.method'                   => 'oidc',
            'services.google.client_id'     => 'abc123a',
            'services.google.client_secret' => 'def456',
        ]);

        $req = $this->get('/login');
        $req->assertDontSeeText('Attempting Login');
        $req->assertSee('Log In');
    }

    public function test_it_does_not_run_if_prevent_query_string_exists()
    {
        config()->set([
            'auth.method' => 'oidc',
        ]);

        $req = $this->get('/login?prevent_auto_init=true');
        $req->assertDontSeeText('Attempting Login');
        $req->assertSee('Log In');
    }

    public function test_logout_with_auto_init_leads_to_login_page_with_prevention_query()
    {
        config()->set([
            'auth.method' => 'oidc',
        ]);
        $this->actingAs($this->users->editor());

        $req = $this->post('/logout');
        $req->assertRedirect('/login?prevent_auto_init=true');
    }
}
