<?php

namespace Tests\Auth;

use BookStack\Actions\ActivityType;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use Illuminate\Testing\TestResponse;
use Tests\Helpers\OidcJwtHelper;
use Tests\TestCase;

class OidcTest extends TestCase
{
    protected string $keyFilePath;
    protected $keyFile;

    protected function setUp(): void
    {
        parent::setUp();
        // Set default config for OpenID Connect

        $this->keyFile = tmpfile();
        $this->keyFilePath = 'file://' . stream_get_meta_data($this->keyFile)['uri'];
        file_put_contents($this->keyFilePath, OidcJwtHelper::publicPemKey());

        config()->set([
            'auth.method'                 => 'oidc',
            'auth.defaults.guard'         => 'oidc',
            'oidc.name'                   => 'SingleSignOn-Testing',
            'oidc.display_name_claims'    => ['name'],
            'oidc.client_id'              => OidcJwtHelper::defaultClientId(),
            'oidc.client_secret'          => 'testpass',
            'oidc.jwt_public_key'         => $this->keyFilePath,
            'oidc.issuer'                 => OidcJwtHelper::defaultIssuer(),
            'oidc.authorization_endpoint' => 'https://oidc.local/auth',
            'oidc.token_endpoint'         => 'https://oidc.local/token',
            'oidc.discover'               => false,
            'oidc.dump_user_details'      => false,
            'oidc.additional_scopes'      => '',
            'oidc.user_to_groups'         => false,
            'oidc.groups_claim'           => 'group',
            'oidc.remove_from_groups'     => false,
            'oidc.external_id_claim'      => 'sub',
        ]);
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        if (file_exists($this->keyFilePath)) {
            unlink($this->keyFilePath);
        }
    }

    public function test_login_option_shows_on_login_page()
    {
        $req = $this->get('/login');
        $req->assertSeeText('SingleSignOn-Testing');
        $this->withHtml($req)->assertElementExists('form[action$="/oidc/login"][method=POST] button');
    }

    public function test_oidc_routes_are_only_active_if_oidc_enabled()
    {
        config()->set(['auth.method' => 'standard']);
        $routes = ['/login' => 'post', '/callback' => 'get'];
        foreach ($routes as $uri => $method) {
            $req = $this->call($method, '/oidc' . $uri);
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
    }

    public function test_logout_route_functions()
    {
        $this->actingAs($this->users->editor());
        $this->post('/logout');
        $this->assertFalse(auth()->check());
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

    public function test_login()
    {
        $req = $this->post('/oidc/login');
        $redirect = $req->headers->get('location');

        $this->assertStringStartsWith('https://oidc.local/auth', $redirect, 'Login redirects to SSO location');
        $this->assertFalse($this->isAuthenticated());
        $this->assertStringContainsString('scope=openid%20profile%20email', $redirect);
        $this->assertStringContainsString('client_id=' . OidcJwtHelper::defaultClientId(), $redirect);
        $this->assertStringContainsString('redirect_uri=' . urlencode(url('/oidc/callback')), $redirect);
    }

    public function test_login_success_flow()
    {
        // Start auth
        $this->post('/oidc/login');
        $state = session()->get('oidc_state');

        $transactions = &$this->mockHttpClient([$this->getMockAuthorizationResponse([
            'email' => 'benny@example.com',
            'sub'   => 'benny1010101',
        ])]);

        // Callback from auth provider
        // App calls token endpoint to get id token
        $resp = $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=' . $state);
        $resp->assertRedirect('/');
        $this->assertCount(1, $transactions);
        /** @var Request $tokenRequest */
        $tokenRequest = $transactions[0]['request'];
        $this->assertEquals('https://oidc.local/token', (string) $tokenRequest->getUri());
        $this->assertEquals('POST', $tokenRequest->getMethod());
        $this->assertEquals('Basic ' . base64_encode(OidcJwtHelper::defaultClientId() . ':testpass'), $tokenRequest->getHeader('Authorization')[0]);
        $this->assertStringContainsString('grant_type=authorization_code', $tokenRequest->getBody());
        $this->assertStringContainsString('code=SplxlOBeZQQYbYS6WxSbIA', $tokenRequest->getBody());
        $this->assertStringContainsString('redirect_uri=' . urlencode(url('/oidc/callback')), $tokenRequest->getBody());

        $this->assertTrue(auth()->check());
        $this->assertDatabaseHas('users', [
            'email'            => 'benny@example.com',
            'external_auth_id' => 'benny1010101',
            'email_confirmed'  => false,
        ]);

        $user = User::query()->where('email', '=', 'benny@example.com')->first();
        $this->assertActivityExists(ActivityType::AUTH_LOGIN, null, "oidc; ({$user->id}) Barry Scott");
    }

    public function test_login_uses_custom_additional_scopes_if_defined()
    {
        config()->set([
            'oidc.additional_scopes' => 'groups, badgers',
        ]);

        $redirect = $this->post('/oidc/login')->headers->get('location');

        $this->assertStringContainsString('scope=openid%20profile%20email%20groups%20badgers', $redirect);
    }

    public function test_callback_fails_if_no_state_present_or_matching()
    {
        $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=abc124');
        $this->assertSessionError('Login using SingleSignOn-Testing failed, system did not provide successful authorization');

        $this->post('/oidc/login');
        $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=abc124');
        $this->assertSessionError('Login using SingleSignOn-Testing failed, system did not provide successful authorization');
    }

    public function test_dump_user_details_option_outputs_as_expected()
    {
        config()->set('oidc.dump_user_details', true);

        $resp = $this->runLogin([
            'email' => 'benny@example.com',
            'sub'   => 'benny505',
        ]);

        $resp->assertStatus(200);
        $resp->assertJson([
            'email' => 'benny@example.com',
            'sub'   => 'benny505',
            'iss'   => OidcJwtHelper::defaultIssuer(),
            'aud'   => OidcJwtHelper::defaultClientId(),
        ]);
        $this->assertFalse(auth()->check());
    }

    public function test_auth_fails_if_no_email_exists_in_user_data()
    {
        $this->runLogin([
            'email' => '',
            'sub'   => 'benny505',
        ]);

        $this->assertSessionError('Could not find an email address, for this user, in the data provided by the external authentication system');
    }

    public function test_auth_fails_if_already_logged_in()
    {
        $this->asEditor();

        $this->runLogin([
            'email' => 'benny@example.com',
            'sub'   => 'benny505',
        ]);

        $this->assertSessionError('Already logged in');
    }

    public function test_auth_login_as_existing_user()
    {
        $editor = $this->users->editor();
        $editor->external_auth_id = 'benny505';
        $editor->save();

        $this->assertFalse(auth()->check());

        $this->runLogin([
            'email' => 'benny@example.com',
            'sub'   => 'benny505',
        ]);

        $this->assertTrue(auth()->check());
        $this->assertEquals($editor->id, auth()->user()->id);
    }

    public function test_auth_login_as_existing_user_email_with_different_auth_id_fails()
    {
        $editor = $this->users->editor();
        $editor->external_auth_id = 'editor101';
        $editor->save();

        $this->assertFalse(auth()->check());

        $resp = $this->runLogin([
            'email' => $editor->email,
            'sub'   => 'benny505',
        ]);
        $resp = $this->followRedirects($resp);

        $resp->assertSeeText('A user with the email ' . $editor->email . ' already exists but with different credentials.');
        $this->assertFalse(auth()->check());
    }

    public function test_auth_login_with_invalid_token_fails()
    {
        $resp = $this->runLogin([
            'sub' => null,
        ]);
        $resp = $this->followRedirects($resp);

        $resp->assertSeeText('ID token validate failed with error: Missing token subject value');
        $this->assertFalse(auth()->check());
    }

    public function test_auth_login_with_autodiscovery()
    {
        $this->withAutodiscovery();

        $transactions = &$this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
        ]);

        $this->assertFalse(auth()->check());

        $this->runLogin();

        $this->assertTrue(auth()->check());
        /** @var Request $discoverRequest */
        $discoverRequest = $transactions[0]['request'];
        /** @var Request $discoverRequest */
        $keysRequest = $transactions[1]['request'];

        $this->assertEquals('GET', $keysRequest->getMethod());
        $this->assertEquals('GET', $discoverRequest->getMethod());
        $this->assertEquals(OidcJwtHelper::defaultIssuer() . '/.well-known/openid-configuration', $discoverRequest->getUri());
        $this->assertEquals(OidcJwtHelper::defaultIssuer() . '/oidc/keys', $keysRequest->getUri());
    }

    public function test_auth_fails_if_autodiscovery_fails()
    {
        $this->withAutodiscovery();
        $this->mockHttpClient([
            new Response(404, [], 'Not found'),
        ]);

        $resp = $this->followRedirects($this->runLogin());
        $this->assertFalse(auth()->check());
        $resp->assertSeeText('Login using SingleSignOn-Testing failed, system did not provide successful authorization');
    }

    public function test_autodiscovery_calls_are_cached()
    {
        $this->withAutodiscovery();

        $transactions = &$this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
            $this->getAutoDiscoveryResponse([
                'issuer' => 'https://auto.example.com',
            ]),
            $this->getJwksResponse(),
        ]);

        // Initial run
        $this->post('/oidc/login');
        $this->assertCount(2, $transactions);
        // Second run, hits cache
        $this->post('/oidc/login');
        $this->assertCount(2, $transactions);

        // Third run, different issuer, new cache key
        config()->set(['oidc.issuer' => 'https://auto.example.com']);
        $this->post('/oidc/login');
        $this->assertCount(4, $transactions);
    }

    public function test_auth_login_with_autodiscovery_with_keys_that_do_not_have_alg_property()
    {
        $this->withAutodiscovery();

        $keyArray = OidcJwtHelper::publicJwkKeyArray();
        unset($keyArray['alg']);

        $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            new Response(200, [
                'Content-Type'  => 'application/json',
                'Cache-Control' => 'no-cache, no-store',
                'Pragma'        => 'no-cache',
            ], json_encode([
                'keys' => [
                    $keyArray,
                ],
            ])),
        ]);

        $this->assertFalse(auth()->check());
        $this->runLogin();
        $this->assertTrue(auth()->check());
    }

    public function test_auth_login_with_autodiscovery_with_keys_that_do_not_have_use_property()
    {
        // Based on reading the OIDC discovery spec:
        // > This contains the signing key(s) the RP uses to validate signatures from the OP. The JWK Set MAY also
        // > contain the Server's encryption key(s), which are used by RPs to encrypt requests to the Server. When
        // > both signing and encryption keys are made available, a use (Key Use) parameter value is REQUIRED for all
        // > keys in the referenced JWK Set to indicate each key's intended usage.
        // We can assume that keys without use are intended for signing.
        $this->withAutodiscovery();

        $keyArray = OidcJwtHelper::publicJwkKeyArray();
        unset($keyArray['use']);

        $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            new Response(200, [
                'Content-Type'  => 'application/json',
                'Cache-Control' => 'no-cache, no-store',
                'Pragma'        => 'no-cache',
            ], json_encode([
                'keys' => [
                    $keyArray,
                ],
            ])),
        ]);

        $this->assertFalse(auth()->check());
        $this->runLogin();
        $this->assertTrue(auth()->check());
    }

    public function test_auth_uses_configured_external_id_claim_option()
    {
        config()->set([
            'oidc.external_id_claim' => 'super_awesome_id',
        ]);
        $roleA = Role::factory()->create(['display_name' => 'Wizards']);

        $resp = $this->runLogin([
            'email'            => 'benny@example.com',
            'sub'              => 'benny1010101',
            'super_awesome_id' => 'xXBennyTheGeezXx',
        ]);
        $resp->assertRedirect('/');

        /** @var User $user */
        $user = User::query()->where('email', '=', 'benny@example.com')->first();
        $this->assertEquals('xXBennyTheGeezXx', $user->external_auth_id);
    }

    public function test_login_group_sync()
    {
        config()->set([
            'oidc.user_to_groups'     => true,
            'oidc.groups_claim'       => 'groups',
            'oidc.remove_from_groups' => false,
        ]);
        $roleA = Role::factory()->create(['display_name' => 'Wizards']);
        $roleB = Role::factory()->create(['display_name' => 'ZooFolks', 'external_auth_id' => 'zookeepers']);
        $roleC = Role::factory()->create(['display_name' => 'Another Role']);

        $resp = $this->runLogin([
            'email'  => 'benny@example.com',
            'sub'    => 'benny1010101',
            'groups' => ['Wizards', 'Zookeepers'],
        ]);
        $resp->assertRedirect('/');

        /** @var User $user */
        $user = User::query()->where('email', '=', 'benny@example.com')->first();

        $this->assertTrue($user->hasRole($roleA->id));
        $this->assertTrue($user->hasRole($roleB->id));
        $this->assertFalse($user->hasRole($roleC->id));
    }

    public function test_login_group_sync_with_nested_groups_in_token()
    {
        config()->set([
            'oidc.user_to_groups'     => true,
            'oidc.groups_claim'       => 'my.custom.groups.attr',
            'oidc.remove_from_groups' => false,
        ]);
        $roleA = Role::factory()->create(['display_name' => 'Wizards']);

        $resp = $this->runLogin([
            'email'  => 'benny@example.com',
            'sub'    => 'benny1010101',
            'my'     => [
                'custom' => [
                    'groups' => [
                        'attr' => ['Wizards'],
                    ],
                ],
            ],
        ]);
        $resp->assertRedirect('/');

        /** @var User $user */
        $user = User::query()->where('email', '=', 'benny@example.com')->first();
        $this->assertTrue($user->hasRole($roleA->id));
    }

    protected function withAutodiscovery()
    {
        config()->set([
            'oidc.issuer'                 => OidcJwtHelper::defaultIssuer(),
            'oidc.discover'               => true,
            'oidc.authorization_endpoint' => null,
            'oidc.token_endpoint'         => null,
            'oidc.jwt_public_key'         => null,
        ]);
    }

    protected function runLogin($claimOverrides = []): TestResponse
    {
        $this->post('/oidc/login');
        $state = session()->get('oidc_state');
        $this->mockHttpClient([$this->getMockAuthorizationResponse($claimOverrides)]);

        return $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=' . $state);
    }

    protected function getAutoDiscoveryResponse($responseOverrides = []): Response
    {
        return new Response(200, [
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache, no-store',
            'Pragma'        => 'no-cache',
        ], json_encode(array_merge([
            'token_endpoint'         => OidcJwtHelper::defaultIssuer() . '/oidc/token',
            'authorization_endpoint' => OidcJwtHelper::defaultIssuer() . '/oidc/authorize',
            'jwks_uri'               => OidcJwtHelper::defaultIssuer() . '/oidc/keys',
            'issuer'                 => OidcJwtHelper::defaultIssuer(),
        ], $responseOverrides)));
    }

    protected function getJwksResponse(): Response
    {
        return new Response(200, [
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache, no-store',
            'Pragma'        => 'no-cache',
        ], json_encode([
            'keys' => [
                OidcJwtHelper::publicJwkKeyArray(),
            ],
        ]));
    }

    protected function getMockAuthorizationResponse($claimOverrides = []): Response
    {
        return new Response(200, [
            'Content-Type'  => 'application/json',
            'Cache-Control' => 'no-cache, no-store',
            'Pragma'        => 'no-cache',
        ], json_encode([
            'access_token' => 'abc123',
            'token_type'   => 'Bearer',
            'expires_in'   => 3600,
            'id_token'     => OidcJwtHelper::idToken($claimOverrides),
        ]));
    }
}
