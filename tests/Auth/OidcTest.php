<?php

namespace Tests\Auth;

use BookStack\Activity\ActivityType;
use BookStack\Facades\Theme;
use BookStack\Theming\ThemeEvents;
use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
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
            'oidc.display_name_claims'    => 'name',
            'oidc.client_id'              => OidcJwtHelper::defaultClientId(),
            'oidc.client_secret'          => 'testpass',
            'oidc.jwt_public_key'         => $this->keyFilePath,
            'oidc.issuer'                 => OidcJwtHelper::defaultIssuer(),
            'oidc.authorization_endpoint' => 'https://oidc.local/auth',
            'oidc.token_endpoint'         => 'https://oidc.local/token',
            'oidc.userinfo_endpoint'      => 'https://oidc.local/userinfo',
            'oidc.discover'               => false,
            'oidc.dump_user_details'      => false,
            'oidc.additional_scopes'      => '',
            'odic.fetch_avatar'           => false,
            'oidc.user_to_groups'         => false,
            'oidc.groups_claim'           => 'group',
            'oidc.remove_from_groups'     => false,
            'oidc.external_id_claim'      => 'sub',
            'oidc.end_session_endpoint'   => false,
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

        $transactions = $this->mockHttpClient([$this->getMockAuthorizationResponse([
            'email' => 'benny@example.com',
            'sub'   => 'benny1010101',
        ])]);

        // Callback from auth provider
        // App calls token endpoint to get id token
        $resp = $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=' . $state);
        $resp->assertRedirect('/');
        $this->assertEquals(1, $transactions->requestCount());
        $tokenRequest = $transactions->latestRequest();
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
        config()->set('oidc.userinfo_endpoint', null);

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

        $resp->assertSeeText('ID token validation failed with error: Missing token subject value');
        $this->assertFalse(auth()->check());
    }

    public function test_auth_fails_if_endpoints_start_with_https()
    {
        $endpointConfigKeys = [
            'oidc.token_endpoint' => 'tokenEndpoint',
            'oidc.authorization_endpoint' => 'authorizationEndpoint',
            'oidc.userinfo_endpoint' => 'userinfoEndpoint',
        ];

        foreach ($endpointConfigKeys as $endpointConfigKey => $endpointName) {
            $logger = $this->withTestLogger();
            $original = config()->get($endpointConfigKey);
            $new = str_replace('https://', 'http://', $original);
            config()->set($endpointConfigKey, $new);

            $this->withoutExceptionHandling();
            $err = null;
            try {
                $resp = $this->runLogin();
                $resp->assertRedirect('/login');
            } catch (\Exception $exception) {
                $err = $exception;
            }
            $this->assertEquals("Endpoint value for \"{$endpointName}\" must start with https://", $err->getMessage());

            config()->set($endpointConfigKey, $original);
        }
    }

    public function test_auth_login_with_autodiscovery()
    {
        $this->withAutodiscovery();

        $transactions = $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
        ]);

        $this->assertFalse(auth()->check());

        $this->runLogin();

        $this->assertTrue(auth()->check());

        $discoverRequest = $transactions->requestAt(0);
        $keysRequest = $transactions->requestAt(1);
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

        $transactions = $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
            $this->getAutoDiscoveryResponse([
                'issuer' => 'https://auto.example.com',
            ]),
            $this->getJwksResponse(),
        ]);

        // Initial run
        $this->post('/oidc/login');
        $this->assertEquals(2, $transactions->requestCount());
        // Second run, hits cache
        $this->post('/oidc/login');
        $this->assertEquals(2, $transactions->requestCount());

        // Third run, different issuer, new cache key
        config()->set(['oidc.issuer' => 'https://auto.example.com']);
        $this->post('/oidc/login');
        $this->assertEquals(4, $transactions->requestCount());
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

    public function test_auth_uses_mulitple_display_name_claims_if_configured()
    {
        config()->set(['oidc.display_name_claims' => 'first_name|last_name']);

        $this->runLogin([
            'email'      => 'benny@example.com',
            'sub'        => 'benny1010101',
            'first_name' => 'Benny',
            'last_name'  => 'Jenkins'
        ]);

        $this->assertDatabaseHas('users', [
            'name' => 'Benny Jenkins',
            'email' => 'benny@example.com',
        ]);
    }

    public function test_user_avatar_fetched_from_picture_on_first_login_if_enabled()
    {
        config()->set(['oidc.fetch_avatar' => true]);

        $this->runLogin([
            'email' => 'avatar@example.com',
            'picture' => 'https://example.com/my-avatar.jpg',
        ], [
            new Response(200, ['Content-Type' => 'image/jpeg'], $this->files->jpegImageData())
        ]);

        $user = User::query()->where('email', '=', 'avatar@example.com')->first();
        $this->assertNotNull($user);

        $this->assertTrue($user->avatar()->exists());
    }

    public function test_user_avatar_fetched_for_existing_user_when_no_avatar_already_assigned()
    {
        config()->set(['oidc.fetch_avatar' => true]);
        $editor = $this->users->editor();
        $editor->external_auth_id = 'benny509';
        $editor->save();

        $this->assertFalse($editor->avatar()->exists());

        $this->runLogin([
            'picture' => 'https://example.com/my-avatar.jpg',
            'sub' => 'benny509',
        ], [
            new Response(200, ['Content-Type' => 'image/jpeg'], $this->files->jpegImageData())
        ]);

        $editor->refresh();
        $this->assertTrue($editor->avatar()->exists());
    }

    public function test_user_avatar_not_fetched_if_image_data_format_unknown()
    {
        config()->set(['oidc.fetch_avatar' => true]);

        $this->runLogin([
            'email' => 'avatar-format@example.com',
            'picture' => 'https://example.com/my-avatar.jpg',
        ], [
            new Response(200, ['Content-Type' => 'image/jpeg'], str_repeat('abc123', 5))
        ]);

        $user = User::query()->where('email', '=', 'avatar-format@example.com')->first();
        $this->assertNotNull($user);

        $this->assertFalse($user->avatar()->exists());
    }

    public function test_user_avatar_not_fetched_when_avatar_already_assigned()
    {
        config()->set(['oidc.fetch_avatar' => true]);
        $editor = $this->users->editor();
        $editor->external_auth_id = 'benny509';
        $editor->save();

        $avatars = $this->app->make(UserAvatars::class);
        $originalImageData = $this->files->pngImageData();
        $avatars->assignToUserFromExistingData($editor, $originalImageData, 'png');

        $this->runLogin([
            'picture' => 'https://example.com/my-avatar.jpg',
            'sub' => 'benny509',
        ], [
            new Response(200, ['Content-Type' => 'image/jpeg'], $this->files->jpegImageData())
        ]);

        $editor->refresh();
        $newAvatarData = file_get_contents($this->files->relativeToFullPath($editor->avatar->path));
        $this->assertEquals($originalImageData, $newAvatarData);
    }

    public function test_user_avatar_fetch_follows_up_to_three_redirects()
    {
        config()->set(['oidc.fetch_avatar' => true]);

        $logger = $this->withTestLogger();

        $this->runLogin([
            'email' => 'avatar@example.com',
            'picture' => 'https://example.com/my-avatar.jpg',
        ], [
            new Response(302, ['Location' => 'https://example.com/a']),
            new Response(302, ['Location' => 'https://example.com/b']),
            new Response(302, ['Location' => 'https://example.com/c']),
            new Response(302, ['Location' => 'https://example.com/d']),
        ]);

        $user = User::query()->where('email', '=', 'avatar@example.com')->first();
        $this->assertFalse($user->avatar()->exists());

        $this->assertStringContainsString('"Failed to fetch image, max redirect limit of 3 tries reached. Last fetched URL: https://example.com/c"', $logger->getRecords()[0]->formatted);
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

    public function test_oidc_logout_form_active_when_oidc_active()
    {
        $this->runLogin();

        $resp = $this->get('/');
        $this->withHtml($resp)->assertElementExists('header form[action$="/oidc/logout"] button');
    }
    public function test_logout_with_autodiscovery_with_oidc_logout_enabled()
    {
        config()->set(['oidc.end_session_endpoint' => true]);
        $this->withAutodiscovery();

        $transactions = $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
        ]);

        $resp = $this->asEditor()->post('/oidc/logout');
        $resp->assertRedirect('https://auth.example.com/oidc/logout?post_logout_redirect_uri=' . urlencode(url('/')));

        $this->assertEquals(2, $transactions->requestCount());
        $this->assertFalse(auth()->check());
    }

    public function test_logout_with_autodiscovery_with_oidc_logout_disabled()
    {
        $this->withAutodiscovery();
        config()->set(['oidc.end_session_endpoint' => false]);

        $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
        ]);

        $resp = $this->asEditor()->post('/oidc/logout');
        $resp->assertRedirect('/');
        $this->assertFalse(auth()->check());
    }

    public function test_logout_without_autodiscovery_but_with_endpoint_configured()
    {
        config()->set(['oidc.end_session_endpoint' => 'https://example.com/logout']);

        $resp = $this->asEditor()->post('/oidc/logout');
        $resp->assertRedirect('https://example.com/logout?post_logout_redirect_uri=' . urlencode(url('/')));
        $this->assertFalse(auth()->check());
    }

    public function test_logout_without_autodiscovery_with_configured_endpoint_adds_to_query_if_existing()
    {
        config()->set(['oidc.end_session_endpoint' => 'https://example.com/logout?a=b']);

        $resp = $this->asEditor()->post('/oidc/logout');
        $resp->assertRedirect('https://example.com/logout?a=b&post_logout_redirect_uri=' . urlencode(url('/')));
        $this->assertFalse(auth()->check());
    }

    public function test_logout_with_autodiscovery_and_auto_initiate_returns_to_auto_prevented_login()
    {
        $this->withAutodiscovery();
        config()->set([
            'auth.auto_initiate' => true,
            'services.google.client_id' => false,
            'services.github.client_id' => false,
            'oidc.end_session_endpoint' => true,
        ]);

        $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
        ]);

        $resp = $this->asEditor()->post('/oidc/logout');

        $redirectUrl = url('/login?prevent_auto_init=true');
        $resp->assertRedirect('https://auth.example.com/oidc/logout?post_logout_redirect_uri=' . urlencode($redirectUrl));
        $this->assertFalse(auth()->check());
    }

    public function test_logout_endpoint_url_overrides_autodiscovery_endpoint()
    {
        config()->set(['oidc.end_session_endpoint' => 'https://a.example.com']);
        $this->withAutodiscovery();

        $transactions = $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(),
            $this->getJwksResponse(),
        ]);

        $resp = $this->asEditor()->post('/oidc/logout');
        $resp->assertRedirect('https://a.example.com?post_logout_redirect_uri=' . urlencode(url('/')));

        $this->assertEquals(2, $transactions->requestCount());
        $this->assertFalse(auth()->check());
    }

    public function test_logout_with_autodiscovery_does_not_use_rp_logout_if_no_url_via_autodiscovery()
    {
        config()->set(['oidc.end_session_endpoint' => true]);
        $this->withAutodiscovery();

        $this->mockHttpClient([
            $this->getAutoDiscoveryResponse(['end_session_endpoint' => null]),
            $this->getJwksResponse(),
        ]);

        $resp = $this->asEditor()->post('/oidc/logout');
        $resp->assertRedirect('/');
        $this->assertFalse(auth()->check());
    }

    public function test_logout_redirect_contains_id_token_hint_if_existing()
    {
        config()->set(['oidc.end_session_endpoint' => 'https://example.com/logout']);

        // Fix times so our token is predictable
        $claimOverrides = [
            'iat' => time(),
            'exp' => time() + 720,
            'auth_time' => time()
        ];
        $this->runLogin($claimOverrides);

        $resp = $this->asEditor()->post('/oidc/logout');
        $query = 'id_token_hint=' . urlencode(OidcJwtHelper::idToken($claimOverrides)) .  '&post_logout_redirect_uri=' . urlencode(url('/'));
        $resp->assertRedirect('https://example.com/logout?' . $query);
    }

    public function test_oidc_id_token_pre_validate_theme_event_without_return()
    {
        $args = [];
        $callback = function (...$eventArgs) use (&$args) {
            $args = $eventArgs;
        };
        Theme::listen(ThemeEvents::OIDC_ID_TOKEN_PRE_VALIDATE, $callback);

        $resp = $this->runLogin([
            'email' => 'benny@example.com',
            'sub'   => 'benny1010101',
            'name'  => 'Benny',
        ]);
        $resp->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'external_auth_id' => 'benny1010101',
        ]);

        $this->assertArrayHasKey('iss', $args[0]);
        $this->assertArrayHasKey('sub', $args[0]);
        $this->assertEquals('Benny', $args[0]['name']);
        $this->assertEquals('benny1010101', $args[0]['sub']);

        $this->assertArrayHasKey('access_token', $args[1]);
        $this->assertArrayHasKey('expires_in', $args[1]);
        $this->assertArrayHasKey('refresh_token', $args[1]);
    }

    public function test_oidc_id_token_pre_validate_theme_event_with_return()
    {
        $callback = function (...$eventArgs) {
            return array_merge($eventArgs[0], [
                'email' => 'lenny@example.com',
                'sub' => 'lenny1010101',
                'name' => 'Lenny',
            ]);
        };
        Theme::listen(ThemeEvents::OIDC_ID_TOKEN_PRE_VALIDATE, $callback);

        $resp = $this->runLogin([
            'email' => 'benny@example.com',
            'sub'   => 'benny1010101',
            'name'  => 'Benny',
        ]);
        $resp->assertRedirect('/');

        $this->assertDatabaseHas('users', [
            'email' => 'lenny@example.com',
            'external_auth_id' => 'lenny1010101',
            'name' => 'Lenny',
        ]);
    }

    public function test_pkce_used_on_authorize_and_access()
    {
        // Start auth
        $resp = $this->post('/oidc/login');
        $state = session()->get('oidc_state');

        $pkceCode = session()->get('oidc_pkce_code');
        $this->assertGreaterThan(30, strlen($pkceCode));

        $expectedCodeChallenge = trim(strtr(base64_encode(hash('sha256', $pkceCode, true)), '+/', '-_'), '=');
        $redirect = $resp->headers->get('Location');
        $redirectParams = [];
        parse_str(parse_url($redirect, PHP_URL_QUERY), $redirectParams);
        $this->assertEquals($expectedCodeChallenge, $redirectParams['code_challenge']);
        $this->assertEquals('S256', $redirectParams['code_challenge_method']);

        $transactions = $this->mockHttpClient([$this->getMockAuthorizationResponse([
            'email' => 'benny@example.com',
            'sub'   => 'benny1010101',
        ])]);

        $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=' . $state);
        $tokenRequest = $transactions->latestRequest();
        $bodyParams = [];
        parse_str($tokenRequest->getBody(), $bodyParams);
        $this->assertEquals($pkceCode, $bodyParams['code_verifier']);
    }

    public function test_userinfo_endpoint_used_if_missing_claims_in_id_token()
    {
        config()->set('oidc.display_name_claims', 'first_name|last_name');
        $this->post('/oidc/login');
        $state = session()->get('oidc_state');

        $client = $this->mockHttpClient([
            $this->getMockAuthorizationResponse(['name' => null]),
            new Response(200, [
                'Content-Type'  => 'application/json',
            ], json_encode([
                'sub' => OidcJwtHelper::defaultPayload()['sub'],
                'first_name' => 'Barry',
                'last_name' => 'Userinfo',
            ]))
        ]);

        $resp = $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=' . $state);
        $resp->assertRedirect('/');
        $this->assertEquals(2, $client->requestCount());

        $userinfoRequest = $client->requestAt(1);
        $this->assertEquals('GET', $userinfoRequest->getMethod());
        $this->assertEquals('https://oidc.local/userinfo', (string) $userinfoRequest->getUri());

        $this->assertEquals('Barry Userinfo', user()->name);
    }

    public function test_userinfo_endpoint_fetch_with_different_sub_throws_error()
    {
        $userinfoResponseData = ['sub' => 'dcba4321'];
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/json'], json_encode($userinfoResponseData));
        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: Subject value provided in the userinfo endpoint does not match the provided ID token value');
    }

    public function test_userinfo_endpoint_fetch_returning_no_sub_throws_error()
    {
        $userinfoResponseData = ['name' => 'testing'];
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/json'], json_encode($userinfoResponseData));
        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: No valid subject value found in userinfo data');
    }

    public function test_userinfo_endpoint_fetch_can_parsed_nested_groups()
    {
        config()->set([
            'oidc.user_to_groups'     => true,
            'oidc.groups_claim'       => 'my.nested.groups.attr',
            'oidc.remove_from_groups' => false,
        ]);

        $roleA = Role::factory()->create(['display_name' => 'Ducks']);
        $userinfoResponseData = [
            'sub' => OidcJwtHelper::defaultPayload()['sub'],
            'my' => ['nested' => ['groups' => ['attr' => ['Ducks', 'Donkeys']]]]
        ];
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/json'], json_encode($userinfoResponseData));
        $resp = $this->runLogin(['groups' => null], [$userinfoResponse]);
        $resp->assertRedirect('/');

        $user = User::where('email', OidcJwtHelper::defaultPayload()['email'])->first();
        $this->assertTrue($user->hasRole($roleA->id));
    }

    public function test_userinfo_endpoint_response_with_complex_json_content_type_handled()
    {
        $userinfoResponseData = [
            'sub' => OidcJwtHelper::defaultPayload()['sub'],
            'name' => 'Barry',
        ];
        $userinfoResponse = new Response(200, ['Content-Type'  => 'Application/Json ; charset=utf-8'], json_encode($userinfoResponseData));
        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/');

        $user = User::where('email', OidcJwtHelper::defaultPayload()['email'])->first();
        $this->assertEquals('Barry', $user->name);
    }

    public function test_userinfo_endpoint_jwks_response_handled()
    {
        $userinfoResponseData = OidcJwtHelper::idToken(['name' => 'Barry Jwks']);
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/jwt'], $userinfoResponseData);

        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/');

        $user = User::where('email', OidcJwtHelper::defaultPayload()['email'])->first();
        $this->assertEquals('Barry Jwks', $user->name);
    }

    public function test_userinfo_endpoint_jwks_response_returning_no_sub_throws()
    {
        $userinfoResponseData = OidcJwtHelper::idToken(['sub' => null]);
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/jwt'], $userinfoResponseData);

        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: No valid subject value found in userinfo data');
    }

    public function test_userinfo_endpoint_jwks_response_returning_non_matching_sub_throws()
    {
        $userinfoResponseData = OidcJwtHelper::idToken(['sub' => 'zzz123']);
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/jwt'], $userinfoResponseData);

        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: Subject value provided in the userinfo endpoint does not match the provided ID token value');
    }

    public function test_userinfo_endpoint_jwks_response_with_invalid_signature_throws()
    {
        $userinfoResponseData = OidcJwtHelper::idToken();
        $exploded = explode('.', $userinfoResponseData);
        $exploded[2] = base64_encode(base64_decode($exploded[2]) . 'ABC');
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/jwt'], implode('.', $exploded));

        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: Token signature could not be validated using the provided keys');
    }

    public function test_userinfo_endpoint_jwks_response_with_invalid_signature_alg_throws()
    {
        $userinfoResponseData = OidcJwtHelper::idToken([], ['alg' => 'ZZ512']);
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/jwt'], $userinfoResponseData);

        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: Only RS256 signature validation is supported. Token reports using ZZ512');
    }

    public function test_userinfo_endpoint_response_with_invalid_content_type_throws()
    {
        $userinfoResponse = new Response(200, ['Content-Type'  => 'application/beans'], json_encode(OidcJwtHelper::defaultPayload()));
        $resp = $this->runLogin(['name' => null], [$userinfoResponse]);
        $resp->assertRedirect('/login');
        $this->assertSessionError('Userinfo endpoint response validation failed with error: No valid subject value found in userinfo data');
    }

    public function test_userinfo_endpoint_not_called_if_empty_groups_array_provided_in_id_token()
    {
        config()->set([
            'oidc.user_to_groups'     => true,
            'oidc.groups_claim'       => 'groups',
            'oidc.remove_from_groups' => false,
        ]);

        $this->post('/oidc/login');
        $state = session()->get('oidc_state');
        $client = $this->mockHttpClient([$this->getMockAuthorizationResponse([
            'groups' => [],
        ])]);

        $resp = $this->get('/oidc/callback?code=SplxlOBeZQQYbYS6WxSbIA&state=' . $state);
        $resp->assertRedirect('/');
        $this->assertEquals(1, $client->requestCount());
        $this->assertTrue(auth()->check());
    }

    protected function withAutodiscovery(): void
    {
        config()->set([
            'oidc.issuer'                 => OidcJwtHelper::defaultIssuer(),
            'oidc.discover'               => true,
            'oidc.authorization_endpoint' => null,
            'oidc.token_endpoint'         => null,
            'oidc.userinfo_endpoint'      => null,
            'oidc.jwt_public_key'         => null,
        ]);
    }

    protected function runLogin($claimOverrides = [], $additionalHttpResponses = []): TestResponse
    {
        $this->post('/oidc/login');
        $state = session()->get('oidc_state');
        $this->mockHttpClient([$this->getMockAuthorizationResponse($claimOverrides), ...$additionalHttpResponses]);

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
            'userinfo_endpoint'      => OidcJwtHelper::defaultIssuer() . '/oidc/userinfo',
            'jwks_uri'               => OidcJwtHelper::defaultIssuer() . '/oidc/keys',
            'issuer'                 => OidcJwtHelper::defaultIssuer(),
            'end_session_endpoint'   => OidcJwtHelper::defaultIssuer() . '/oidc/logout',
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
