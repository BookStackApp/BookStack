<?php namespace Tests\Auth;

use Tests\TestCase;

class OpenIdTest extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // Set default config for OpenID Connect
        config()->set([
            'auth.method' => 'openid',
            'auth.defaults.guard' => 'openid',
            'openid.name' => 'SingleSignOn-Testing',
            'openid.email_attribute' => 'email',
            'openid.display_name_attributes' => ['given_name', 'family_name'],
            'openid.external_id_attribute' => 'uid',
            'openid.openid_overrides' => null,
            'openid.openid.clientId' => 'testapp',
            'openid.openid.clientSecret' => 'testpass',
            'openid.openid.publicKey' => $this->testCert,
            'openid.openid.idTokenIssuer' => 'https://openid.local',
            'openid.openid.urlAuthorize' => 'https://openid.local/auth',
            'openid.openid.urlAccessToken' => 'https://openid.local/token',
        ]);
    }

    public function test_openid_overrides_functions_as_expected()
    {
        $json = '{"urlAuthorize": "https://openid.local/custom"}';
        config()->set(['openid.openid_overrides' => $json]);

        $req = $this->get('/openid/login');
        $redirect = $req->headers->get('location');
        $this->assertStringStartsWith('https://openid.local/custom', $redirect, 'Login redirects to SSO location');
    }

    public function test_login_option_shows_on_login_page()
    {
        $req = $this->get('/login');
        $req->assertSeeText('SingleSignOn-Testing');
        $req->assertElementExists('form[action$="/openid/login"][method=POST] button');
    }

    public function test_login()
    {
        $req = $this->post('/openid/login');
        $redirect = $req->headers->get('location');

        $this->assertStringStartsWith('https://openid.local/auth', $redirect, 'Login redirects to SSO location');
        $this->assertFalse($this->isAuthenticated());
    }

    public function test_openid_routes_are_only_active_if_openid_enabled()
    {
        config()->set(['auth.method' => 'standard']);
        $getRoutes = ['/logout', '/metadata', '/sls'];
        foreach ($getRoutes as $route) {
            $req = $this->get('/openid' . $route);
            $this->assertPermissionError($req);
        }

        $postRoutes = ['/login', '/acs'];
        foreach ($postRoutes as $route) {
            $req = $this->post('/openid' . $route);
            $this->assertPermissionError($req);
        }
    }

    public function test_forgot_password_routes_inaccessible()
    {
        $resp = $this->get('/password/email');
        $this->assertPermissionError($resp);

        $resp = $this->post('/password/email');
        $this->assertPermissionError($resp);

        $resp = $this->get('/password/reset/abc123');
        $this->assertPermissionError($resp);

        $resp = $this->post('/password/reset');
        $this->assertPermissionError($resp);
    }

    public function test_standard_login_routes_inaccessible()
    {
        $resp = $this->post('/login');
        $this->assertPermissionError($resp);

        $resp = $this->get('/logout');
        $this->assertPermissionError($resp);
    }

    public function test_user_invite_routes_inaccessible()
    {
        $resp = $this->get('/register/invite/abc123');
        $this->assertPermissionError($resp);

        $resp = $this->post('/register/invite/abc123');
        $this->assertPermissionError($resp);
    }

    public function test_user_register_routes_inaccessible()
    {
        $resp = $this->get('/register');
        $this->assertPermissionError($resp);

        $resp = $this->post('/register');
        $this->assertPermissionError($resp);
    }
}
