<?php namespace Tests;

use BookStack\Auth\Role;
use BookStack\Auth\User;

class Saml2Test extends TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        // Set default config for SAML2
        config()->set([
            'saml2.name' => 'SingleSignOn-Testing',
            'saml2.enabled' => true,
            'saml2.auto_register' => true,
            'saml2.email_attribute' => 'email',
            'saml2.display_name_attributes' => ['first_name', 'last_name'],
            'saml2.external_id_attribute' => 'uid',
            'saml2.user_to_groups' => false,
            'saml2.group_attribute' => 'user_groups',
            'saml2.remove_from_groups' => false,
            'saml2.onelogin_overrides' => null,
            'saml2.onelogin.idp.entityId' => 'http://saml.local/saml2/idp/metadata.php',
            'saml2.onelogin.idp.singleSignOnService.url' => 'http://saml.local/saml2/idp/SSOService.php',
            'saml2.onelogin.idp.singleLogoutService.url' => 'http://saml.local/saml2/idp/SingleLogoutService.php',
            'saml2.autoload_from_metadata' => false,
            'saml2.onelogin.idp.x509cert' => $this->testCert,
            'saml2.onelogin.debug' => false,
        ]);
    }

    public function test_metadata_endpoint_displays_xml_as_expected()
    {
        $req = $this->get('/saml2/metadata');
        $req->assertHeader('Content-Type', 'text/xml; charset=UTF-8');
        $req->assertSee('md:EntityDescriptor');
        $req->assertSee(url('/saml2/acs'));
    }

    public function test_onelogin_overrides_functions_as_expected()
    {
        $json = '{"sp": {"assertionConsumerService": {"url": "https://example.com/super-cats"}}, "contactPerson": {"technical": {"givenName": "Barry Scott", "emailAddress": "barry@example.com"}}}';
        config()->set(['saml2.onelogin_overrides' => $json]);

        $req = $this->get('/saml2/metadata');
        $req->assertSee('https://example.com/super-cats');
        $req->assertSee('md:ContactPerson');
        $req->assertSee('<md:GivenName>Barry Scott</md:GivenName>');
    }

    public function test_login_option_shows_on_login_page()
    {
        $req = $this->get('/login');
        $req->assertSeeText('SingleSignOn-Testing');
        $req->assertElementExists('a[href$="/saml2/login"]');
    }

    public function test_login_option_shows_on_register_page_only_when_auto_register_enabled()
    {
        $this->setSettings(['app-public' => 'true', 'registration-enabled' => 'true']);

        $req = $this->get('/register');
        $req->assertSeeText('SingleSignOn-Testing');
        $req->assertElementExists('a[href$="/saml2/login"]');

        config()->set(['saml2.auto_register' => false]);

        $req = $this->get('/register');
        $req->assertDontSeeText('SingleSignOn-Testing');
        $req->assertElementNotExists('a[href$="/saml2/login"]');
    }

    public function test_login()
    {
        $req = $this->get('/saml2/login');
        $redirect = $req->headers->get('location');
        $this->assertStringStartsWith('http://saml.local/saml2/idp/SSOService.php', $redirect, 'Login redirects to SSO location');

        config()->set(['saml2.onelogin.strict' => false]);
        $this->assertFalse($this->isAuthenticated());

        $this->withPost(['SAMLResponse' => $this->acsPostData], function () {

            $acsPost = $this->post('/saml2/acs');
            $acsPost->assertRedirect('/');
            $this->assertTrue($this->isAuthenticated());
            $this->assertDatabaseHas('users', [
                'email' => 'user@example.com',
                'external_auth_id' => 'user',
                'email_confirmed' => true,
                'name' => 'Barry Scott'
            ]);

        });
    }

    public function test_group_role_sync_on_login()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
            'saml2.user_to_groups' => true,
            'saml2.remove_from_groups' => false,
        ]);

        $memberRole = factory(Role::class)->create(['external_auth_id' => 'member']);
        $adminRole = Role::getSystemRole('admin');

        $this->withPost(['SAMLResponse' => $this->acsPostData], function () use ($memberRole, $adminRole) {
            $acsPost = $this->post('/saml2/acs');
            $user = User::query()->where('external_auth_id', '=', 'user')->first();

            $userRoleIds = $user->roles()->pluck('id');
            $this->assertContains($memberRole->id, $userRoleIds, 'User was assigned to member role');
            $this->assertContains($adminRole->id, $userRoleIds, 'User was assigned to admin role');
        });
    }

    public function test_group_role_sync_removal_option_works_as_expected()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
            'saml2.user_to_groups' => true,
            'saml2.remove_from_groups' => true,
        ]);

        $this->withPost(['SAMLResponse' => $this->acsPostData], function () {
            $acsPost = $this->post('/saml2/acs');
            $user = User::query()->where('external_auth_id', '=', 'user')->first();

            $randomRole = factory(Role::class)->create(['external_auth_id' => 'random']);
            $user->attachRole($randomRole);
            $this->assertContains($randomRole->id, $user->roles()->pluck('id'));

            auth()->logout();
            $acsPost = $this->post('/saml2/acs');
            $this->assertNotContains($randomRole->id, $user->roles()->pluck('id'));
        });
    }

    public function test_logout_redirects_to_saml_logout_when_active_saml_session()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
        ]);

        $this->withPost(['SAMLResponse' => $this->acsPostData], function () {
            $acsPost = $this->post('/saml2/acs');
            $lastLoginType = session()->get('last_login_type');
            $this->assertEquals('saml2', $lastLoginType);

            $req = $this->get('/logout');
            $req->assertRedirect('/saml2/logout');
        });
    }

    public function test_logout_sls_flow()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
        ]);

        $handleLogoutResponse = function () {
            $this->assertTrue($this->isAuthenticated());

            $req = $this->get('/saml2/sls');
            $req->assertRedirect('/');
            $this->assertFalse($this->isAuthenticated());
        };

        $loginAndStartLogout = function () use ($handleLogoutResponse) {
            $this->post('/saml2/acs');

            $req = $this->get('/saml2/logout');
            $redirect = $req->headers->get('location');
            $this->assertStringStartsWith('http://saml.local/saml2/idp/SingleLogoutService.php', $redirect);
            $this->withGet(['SAMLResponse' => $this->sloResponseData], $handleLogoutResponse);
        };

        $this->withPost(['SAMLResponse' => $this->acsPostData], $loginAndStartLogout);
    }

    public function test_logout_sls_flow_when_sls_not_configured()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
            'saml2.onelogin.idp.singleLogoutService.url' => null,
        ]);

        $loginAndStartLogout = function () {
            $this->post('/saml2/acs');

            $req = $this->get('/saml2/logout');
            $req->assertRedirect('/');
            $this->assertFalse($this->isAuthenticated());
        };

        $this->withPost(['SAMLResponse' => $this->acsPostData], $loginAndStartLogout);
    }

    public function test_dump_user_details_option_works()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
            'saml2.dump_user_details' => true,
        ]);

        $this->withPost(['SAMLResponse' => $this->acsPostData], function () {
            $acsPost = $this->post('/saml2/acs');
            $acsPost->assertJsonStructure([
                'id_from_idp',
                'attrs_from_idp' => [],
                'attrs_after_parsing' => [],
            ]);
        });
    }

    public function test_user_registration_with_existing_email()
    {
        config()->set([
            'saml2.onelogin.strict' => false,
        ]);

        $viewer = $this->getViewer();
        $viewer->email = 'user@example.com';
        $viewer->save();

        $this->withPost(['SAMLResponse' => $this->acsPostData], function () {
            $acsPost = $this->post('/saml2/acs');
            $acsPost->assertRedirect('/');
            $errorMessage = session()->get('error');
            $this->assertEquals('Registration unsuccessful since a user already exists with email address "user@example.com"', $errorMessage);
        });
    }

    public function test_saml_routes_are_only_active_if_saml_enabled()
    {
        config()->set(['saml2.enabled' => false]);
        $getRoutes = ['/login', '/logout', '/metadata', '/sls'];
        foreach ($getRoutes as $route) {
            $req = $this->get('/saml2' . $route);
            $req->assertRedirect('/');
            $error = session()->get('error');
            $this->assertStringStartsWith('You do not have permission to access', $error);
            session()->flush();
        }

        $postRoutes = ['/acs'];
        foreach ($postRoutes as $route) {
            $req = $this->post('/saml2' . $route);
            $req->assertRedirect('/');
            $error = session()->get('error');
            $this->assertStringStartsWith('You do not have permission to access', $error);
            session()->flush();
        }
    }

    protected function withGet(array $options, callable $callback)
    {
        return $this->withGlobal($_GET, $options, $callback);
    }

    protected function withPost(array $options, callable $callback)
    {
        return $this->withGlobal($_POST, $options, $callback);
    }

    protected function withGlobal(array &$global, array $options, callable $callback)
    {
        $original = [];
        foreach ($options as $key => $val) {
            $original[$key] = $global[$key] ?? null;
            $global[$key] = $val;
        }

        $callback();

        foreach ($options as $key => $val) {
            $val = $original[$key];
            if ($val) {
                $global[$key] = $val;
            } else {
                unset($global[$key]);
            }
        }
    }

    /**
     * The post data for a callback for single-sign-in.
     * Provides the following attributes:
     * array:5 [
        "uid" => array:1 [
            0 => "user"
        ]
        "first_name" => array:1 [
            0 => "Barry"
        ]
        "last_name" => array:1 [
            0 => "Scott"
        ]
        "email" => array:1 [
            0 => "user@example.com"
        ]
        "user_groups" => array:2 [
            0 => "member"
            1 => "admin"
        ]
    ]
     */
    protected $acsPostData = 'PHNhbWxwOlJlc3BvbnNlIHhtbG5zOnNhbWxwPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6cHJvdG9jb2wiIHhtbG5zOnNhbWw9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphc3NlcnRpb24iIElEPSJfNGRkNDU2NGRjNzk0MDYxZWYxYmFhMDQ2N2Q3OTAyOGNlZDNjZTU0YmVlIiBWZXJzaW9uPSIyLjAiIElzc3VlSW5zdGFudD0iMjAxOS0xMS0xN1QxNzo1MzozOVoiIERlc3RpbmF0aW9uPSJodHRwOi8vYm9va3N0YWNrLmxvY2FsL3NhbWwyL2FjcyIgSW5SZXNwb25zZVRvPSJPTkVMT0dJTl82YTBmNGYzOTkzMDQwZjE5ODdmZDM3MDY4YjUyOTYyMjlhZDUzNjFjIj48c2FtbDpJc3N1ZXI+aHR0cDovL3NhbWwubG9jYWwvc2FtbDIvaWRwL21ldGFkYXRhLnBocDwvc2FtbDpJc3N1ZXI+PGRzOlNpZ25hdHVyZSB4bWxuczpkcz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnIyI+CiAgPGRzOlNpZ25lZEluZm8+PGRzOkNhbm9uaWNhbGl6YXRpb25NZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzEwL3htbC1leGMtYzE0biMiLz4KICAgIDxkczpTaWduYXR1cmVNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGRzaWctbW9yZSNyc2Etc2hhMjU2Ii8+CiAgPGRzOlJlZmVyZW5jZSBVUkk9IiNfNGRkNDU2NGRjNzk0MDYxZWYxYmFhMDQ2N2Q3OTAyOGNlZDNjZTU0YmVlIj48ZHM6VHJhbnNmb3Jtcz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMC8wOS94bWxkc2lnI2VudmVsb3BlZC1zaWduYXR1cmUiLz48ZHM6VHJhbnNmb3JtIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8+PC9kczpUcmFuc2Zvcm1zPjxkczpEaWdlc3RNZXRob2QgQWxnb3JpdGhtPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxLzA0L3htbGVuYyNzaGEyNTYiLz48ZHM6RGlnZXN0VmFsdWU+dm1oL1M3NU5mK2crZWNESkN6QWJaV0tKVmx1ZzdCZnNDKzlhV05lSXJlUT08L2RzOkRpZ2VzdFZhbHVlPjwvZHM6UmVmZXJlbmNlPjwvZHM6U2lnbmVkSW5mbz48ZHM6U2lnbmF0dXJlVmFsdWU+dnJhZ0tKWHNjVm5UNjJFaEk3bGk4MERUWHNOTGJOc3lwNWZ2QnU4WjFYSEtFUVA3QWpPNkcxcVBwaGpWQ2dRMzd6TldVVTZvUytQeFA3UDlHeG5xL3hKejRUT3lHcHJ5N1RoK2pIcHc0YWVzQTdrTmp6VU51UmU2c1ltWTlrRXh2VjMvTmJRZjROMlM2Y2RhRHIzWFRodllVVDcxYzQwNVVHOFJpQjJaY3liWHIxZU1yWCtXUDBnU2Qrc0F2RExqTjBJc3pVWlVUNThadFpEVE1ya1ZGL0pIbFBFQ04vVW1sYVBBeitTcUJ4c25xTndZK1oxYUt3MnlqeFRlNnUxM09Kb29OOVN1REowNE0rK2F3RlY3NkI4cXEyTzMxa3FBbDJibm1wTGxtTWdRNFEraUlnL3dCc09abTV1clphOWJObDNLVEhtTVBXbFpkbWhsLzgvMy9IT1RxN2thWGs3cnlWRHRLcFlsZ3FUajNhRUpuL0dwM2o4SFp5MUVialRiOTRRT1ZQMG5IQzB1V2hCaE13TjdzVjFrUSsxU2NjUlpUZXJKSGlSVUQvR0srTVg3M0YrbzJVTFRIL1Z6Tm9SM2o4N2hOLzZ1UC9JeG5aM1RudGR1MFZPZS9ucEdVWjBSMG9SWFhwa2JTL2poNWk1ZjU0RXN4eXZ1VEM5NHdKaEM8L2RzOlNpZ25hdHVyZVZhbHVlPgo8ZHM6S2V5SW5mbz48ZHM6WDUwOURhdGE+PGRzOlg1MDlDZXJ0aWZpY2F0ZT5NSUlFYXpDQ0F0T2dBd0lCQWdJVWU3YTA4OENucjRpem1ybkJFbng1cTNIVE12WXdEUVlKS29aSWh2Y05BUUVMQlFBd1JURUxNQWtHQTFVRUJoTUNSMEl4RXpBUkJnTlZCQWdNQ2xOdmJXVXRVM1JoZEdVeElUQWZCZ05WQkFvTUdFbHVkR1Z5Ym1WMElGZHBaR2RwZEhNZ1VIUjVJRXgwWkRBZUZ3MHhPVEV4TVRZeE1qRTNNVFZhRncweU9URXhNVFV4TWpFM01UVmFNRVV4Q3pBSkJnTlZCQVlUQWtkQ01STXdFUVlEVlFRSURBcFRiMjFsTFZOMFlYUmxNU0V3SHdZRFZRUUtEQmhKYm5SbGNtNWxkQ0JYYVdSbmFYUnpJRkIwZVNCTWRHUXdnZ0dpTUEwR0NTcUdTSWIzRFFFQkFRVUFBNElCandBd2dnR0tBb0lCZ1FEekxlOUZmZHlwbFR4SHA0U3VROWdRdFpUM3QrU0RmdkVMNzJwcENmRlp3NytCNXM1Qi9UNzNhWHBvUTNTNTNwR0kxUklXQ2dlMmlDVVEydHptMjdhU05IMGl1OWFKWWNVUVovUklUcWQwYXl5RGtzMU5BMlBUM1RXNnQzbTdLVjVyZTRQME5iK1lEZXV5SGRreitqY010cG44Q21Cb1QwSCtza2hhMGhpcUlOa2prUlBpSHZMSFZHcCt0SFVFQS9JNm1ONGFCL1VFeFNUTHM3OU5zTFVmdGVxcXhlOSt0dmRVYVRveURQcmhQRmpPTnMrOU5LQ2t6SUM2dmN2N0o2QXR1S0c2bkVUK3pCOXlPV2d0R1lRaWZYcVFBMnk1ZEw4MUJCMHE1dU1hQkxTMnBxM2FQUGp6VTJGMytFeXNqeVNXVG5Da2ZrN0M1U3NDWFJ1OFErVTk1dHVucE5md2Y1b2xFNldhczQ4Tk1NK1B3VjdpQ05NUGtOemxscTZQQ2lNK1A4RHJNU2N6elVaWlFVU3Y2ZFN3UENvK1lTVmltRU0wT2czWEpUaU5oUTVBTmxhSW42Nkt3NWdmb0JmdWlYbXlJS2lTRHlBaURZbUZhZjQzOTV3V3dMa1RSK2N3OFdmamFIc3dLWlRvbW4xTVIzT0pzWTJVSjBlUkJZTStZU3NDQXdFQUFhTlRNRkV3SFFZRFZSME9CQllFRkltcDJDWUNHZmNiN3c5MUgvY1NoVENrWHdSL01COEdBMVVkSXdRWU1CYUFGSW1wMkNZQ0dmY2I3dzkxSC9jU2hUQ2tYd1IvTUE4R0ExVWRFd0VCL3dRRk1BTUJBZjh3RFFZSktvWklodmNOQVFFTEJRQURnZ0dCQUErZy9DN3VMOWxuK1crcUJrbkxXODFrb2pZZmxnUEsxSTFNSEl3bk12bC9aVEhYNGRSWEtEcms3S2NVcTFLanFhak5WNjZmMWNha3AwM0lpakJpTzBYaTFnWFVaWUxvQ2lOR1V5eXA5WGxvaUl5OVh3MlBpV25ydzAreVp5dlZzc2JlaFhYWUpsNFJpaEJqQld1bDlSNHdNWUxPVVNKRGUyV3hjVUJoSm54eU5ScytQMHhMU1FYNkIybjZueG9Ea280cDA3czhaS1hRa2VpWjJpd0ZkVHh6UmtHanRoTVV2NzA0bnpzVkdCVDBEQ1B0ZlNhTzVLSlpXMXJDczN5aU10aG5CeHE0cUVET1FKRklsKy9MRDcxS2JCOXZaY1c1SnVhdnpCRm1rS0dOcm8vNkcxSTdlbDQ2SVI0d2lqVHlORkNZVXVEOWR0aWduTm1wV3ROOE9XK3B0aUwvanRUeVNXdWtqeXMwcyt2TG44M0NWdmpCMGRKdFZBSVlPZ1hGZEl1aWk2Nmdjend3TS9MR2lPRXhKbjBkVE56c0ovSVlocHhMNEZCRXVQMHBza1kwbzBhVWxKMkxTMmord1NRVFJLc0JnTWp5clVyZWtsZTJPRFN0U3RuM2VhYmpJeDAvRkhscEZyMGpOSW0vb01QN2t3anRVWDR6YU5lNDdRSTRHZz09PC9kczpYNTA5Q2VydGlmaWNhdGU+PC9kczpYNTA5RGF0YT48L2RzOktleUluZm8+PC9kczpTaWduYXR1cmU+PHNhbWxwOlN0YXR1cz48c2FtbHA6U3RhdHVzQ29kZSBWYWx1ZT0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOnN0YXR1czpTdWNjZXNzIi8+PC9zYW1scDpTdGF0dXM+PHNhbWw6QXNzZXJ0aW9uIHhtbG5zOnhzaT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS9YTUxTY2hlbWEtaW5zdGFuY2UiIHhtbG5zOnhzPSJodHRwOi8vd3d3LnczLm9yZy8yMDAxL1hNTFNjaGVtYSIgSUQ9Il82ODQyZGY5YzY1OWYxM2ZlNTE5NmNkOWVmNmMyZjAyODM2NGFlOTQzYjEiIFZlcnNpb249IjIuMCIgSXNzdWVJbnN0YW50PSIyMDE5LTExLTE3VDE3OjUzOjM5WiI+PHNhbWw6SXNzdWVyPmh0dHA6Ly9zYW1sLmxvY2FsL3NhbWwyL2lkcC9tZXRhZGF0YS5waHA8L3NhbWw6SXNzdWVyPjxkczpTaWduYXR1cmUgeG1sbnM6ZHM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyMiPgogIDxkczpTaWduZWRJbmZvPjxkczpDYW5vbmljYWxpemF0aW9uTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8xMC94bWwtZXhjLWMxNG4jIi8+CiAgICA8ZHM6U2lnbmF0dXJlTWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxkc2lnLW1vcmUjcnNhLXNoYTI1NiIvPgogIDxkczpSZWZlcmVuY2UgVVJJPSIjXzY4NDJkZjljNjU5ZjEzZmU1MTk2Y2Q5ZWY2YzJmMDI4MzY0YWU5NDNiMSI+PGRzOlRyYW5zZm9ybXM+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvMDkveG1sZHNpZyNlbnZlbG9wZWQtc2lnbmF0dXJlIi8+PGRzOlRyYW5zZm9ybSBBbGdvcml0aG09Imh0dHA6Ly93d3cudzMub3JnLzIwMDEvMTAveG1sLWV4Yy1jMTRuIyIvPjwvZHM6VHJhbnNmb3Jtcz48ZHM6RGlnZXN0TWV0aG9kIEFsZ29yaXRobT0iaHR0cDovL3d3dy53My5vcmcvMjAwMS8wNC94bWxlbmMjc2hhMjU2Ii8+PGRzOkRpZ2VzdFZhbHVlPmtyYjV3NlM4dG9YYy9lU3daUFVPQnZRem4zb3M0SkFDdXh4ckpreHBnRnc9PC9kczpEaWdlc3RWYWx1ZT48L2RzOlJlZmVyZW5jZT48L2RzOlNpZ25lZEluZm8+PGRzOlNpZ25hdHVyZVZhbHVlPjJxcW1Ba3hucXhOa3N5eXh5dnFTVDUxTDg5VS9ZdHpja2t1ekF4ci9hQ1JTK1NPRzg1YkFNWm8vU3puc3d0TVlBYlFRQ0VGb0R1amdNdlpzSFl3NlR2dmFHanlXWUpRNVZyYWhlemZaSWlCVUU0NHBtWGFrOCswV0l0WTVndnBGSXhxWFZaRmdFUkt2VExmZVFCMzhkMVZQc0ZVZ0RYdXQ4VS9Qdm43dXZwdXZjVXorMUUyOUVKR2FZL0dndnhUN0tyWU9SQTh3SitNdVRzUVZtanNlUnhveVJTejA4TmJ3ZTJIOGpXQnpFWWNxWWwyK0ZnK2hwNWd0S216VmhLRnBkNXZBNjdBSXo1NXN0QmNHNSswNHJVaWpFSzRzci9xa0x5QmtKQjdLdkwzanZKcG8zQjhxYkxYeXhLb1dSSmRnazhKNHMvTVp1QWk3QWUxUXNTTjl2Z3ZTdVRlc0VCUjVpSHJuS1lrbEpRWXNrbUQzbSsremE4U1NRbnBlM0UzYUZBY3p6cElUdUQ4YkFCWmRqcUk2TkhrSmFRQXBmb0hWNVQrZ244ejdUTWsrSStUU2JlQURubUxCS3lnMHRabW10L0ZKbDV6eWowVmxwc1dzTVM2OVE2bUZJVStqcEhSanpOb2FLMVM1dlQ3ZW1HbUhKSUp0cWlOdXJRN0tkQlBJPC9kczpTaWduYXR1cmVWYWx1ZT4KPGRzOktleUluZm8+PGRzOlg1MDlEYXRhPjxkczpYNTA5Q2VydGlmaWNhdGU+TUlJRWF6Q0NBdE9nQXdJQkFnSVVlN2EwODhDbnI0aXptcm5CRW54NXEzSFRNdll3RFFZSktvWklodmNOQVFFTEJRQXdSVEVMTUFrR0ExVUVCaE1DUjBJeEV6QVJCZ05WQkFnTUNsTnZiV1V0VTNSaGRHVXhJVEFmQmdOVkJBb01HRWx1ZEdWeWJtVjBJRmRwWkdkcGRITWdVSFI1SUV4MFpEQWVGdzB4T1RFeE1UWXhNakUzTVRWYUZ3MHlPVEV4TVRVeE1qRTNNVFZhTUVVeEN6QUpCZ05WQkFZVEFrZENNUk13RVFZRFZRUUlEQXBUYjIxbExWTjBZWFJsTVNFd0h3WURWUVFLREJoSmJuUmxjbTVsZENCWGFXUm5hWFJ6SUZCMGVTQk1kR1F3Z2dHaU1BMEdDU3FHU0liM0RRRUJBUVVBQTRJQmp3QXdnZ0dLQW9JQmdRRHpMZTlGZmR5cGxUeEhwNFN1UTlnUXRaVDN0K1NEZnZFTDcycHBDZkZadzcrQjVzNUIvVDczYVhwb1EzUzUzcEdJMVJJV0NnZTJpQ1VRMnR6bTI3YVNOSDBpdTlhSlljVVFaL1JJVHFkMGF5eURrczFOQTJQVDNUVzZ0M203S1Y1cmU0UDBOYitZRGV1eUhka3oramNNdHBuOENtQm9UMEgrc2toYTBoaXFJTmtqa1JQaUh2TEhWR3ArdEhVRUEvSTZtTjRhQi9VRXhTVExzNzlOc0xVZnRlcXF4ZTkrdHZkVWFUb3lEUHJoUEZqT05zKzlOS0NreklDNnZjdjdKNkF0dUtHNm5FVCt6Qjl5T1dndEdZUWlmWHFRQTJ5NWRMODFCQjBxNXVNYUJMUzJwcTNhUFBqelUyRjMrRXlzanlTV1RuQ2tmazdDNVNzQ1hSdThRK1U5NXR1bnBOZndmNW9sRTZXYXM0OE5NTStQd1Y3aUNOTVBrTnpsbHE2UENpTStQOERyTVNjenpVWlpRVVN2NmRTd1BDbytZU1ZpbUVNME9nM1hKVGlOaFE1QU5sYUluNjZLdzVnZm9CZnVpWG15SUtpU0R5QWlEWW1GYWY0Mzk1d1d3TGtUUitjdzhXZmphSHN3S1pUb21uMU1SM09Kc1kyVUowZVJCWU0rWVNzQ0F3RUFBYU5UTUZFd0hRWURWUjBPQkJZRUZJbXAyQ1lDR2ZjYjd3OTFIL2NTaFRDa1h3Ui9NQjhHQTFVZEl3UVlNQmFBRkltcDJDWUNHZmNiN3c5MUgvY1NoVENrWHdSL01BOEdBMVVkRXdFQi93UUZNQU1CQWY4d0RRWUpLb1pJaHZjTkFRRUxCUUFEZ2dHQkFBK2cvQzd1TDlsbitXK3FCa25MVzgxa29qWWZsZ1BLMUkxTUhJd25NdmwvWlRIWDRkUlhLRHJrN0tjVXExS2pxYWpOVjY2ZjFjYWtwMDNJaWpCaU8wWGkxZ1hVWllMb0NpTkdVeXlwOVhsb2lJeTlYdzJQaVducncwK3laeXZWc3NiZWhYWFlKbDRSaWhCakJXdWw5UjR3TVlMT1VTSkRlMld4Y1VCaEpueHlOUnMrUDB4TFNRWDZCMm42bnhvRGtvNHAwN3M4WktYUWtlaVoyaXdGZFR4elJrR2p0aE1VdjcwNG56c1ZHQlQwRENQdGZTYU81S0paVzFyQ3MzeWlNdGhuQnhxNHFFRE9RSkZJbCsvTEQ3MUtiQjl2WmNXNUp1YXZ6QkZta0tHTnJvLzZHMUk3ZWw0NklSNHdpalR5TkZDWVV1RDlkdGlnbk5tcFd0TjhPVytwdGlML2p0VHlTV3VranlzMHMrdkxuODNDVnZqQjBkSnRWQUlZT2dYRmRJdWlpNjZnY3p3d00vTEdpT0V4Sm4wZFROenNKL0lZaHB4TDRGQkV1UDBwc2tZMG8wYVVsSjJMUzJqK3dTUVRSS3NCZ01qeXJVcmVrbGUyT0RTdFN0bjNlYWJqSXgwL0ZIbHBGcjBqTkltL29NUDdrd2p0VVg0emFOZTQ3UUk0R2c9PTwvZHM6WDUwOUNlcnRpZmljYXRlPjwvZHM6WDUwOURhdGE+PC9kczpLZXlJbmZvPjwvZHM6U2lnbmF0dXJlPjxzYW1sOlN1YmplY3Q+PHNhbWw6TmFtZUlEIFNQTmFtZVF1YWxpZmllcj0iaHR0cDovL2Jvb2tzdGFjay5sb2NhbC9zYW1sMi9tZXRhZGF0YSIgRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6bmFtZWlkLWZvcm1hdDp0cmFuc2llbnQiPl8yYzdhYjg2ZWI4ZjFkMTA2MzQ0M2YyMTljYzU4NjhmZjY2NzA4OTEyZTM8L3NhbWw6TmFtZUlEPjxzYW1sOlN1YmplY3RDb25maXJtYXRpb24gTWV0aG9kPSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6Y206YmVhcmVyIj48c2FtbDpTdWJqZWN0Q29uZmlybWF0aW9uRGF0YSBOb3RPbk9yQWZ0ZXI9IjIwMTktMTEtMTdUMTc6NTg6MzlaIiBSZWNpcGllbnQ9Imh0dHA6Ly9ib29rc3RhY2subG9jYWwvc2FtbDIvYWNzIiBJblJlc3BvbnNlVG89Ik9ORUxPR0lOXzZhMGY0ZjM5OTMwNDBmMTk4N2ZkMzcwNjhiNTI5NjIyOWFkNTM2MWMiLz48L3NhbWw6U3ViamVjdENvbmZpcm1hdGlvbj48L3NhbWw6U3ViamVjdD48c2FtbDpDb25kaXRpb25zIE5vdEJlZm9yZT0iMjAxOS0xMS0xN1QxNzo1MzowOVoiIE5vdE9uT3JBZnRlcj0iMjAxOS0xMS0xN1QxNzo1ODozOVoiPjxzYW1sOkF1ZGllbmNlUmVzdHJpY3Rpb24+PHNhbWw6QXVkaWVuY2U+aHR0cDovL2Jvb2tzdGFjay5sb2NhbC9zYW1sMi9tZXRhZGF0YTwvc2FtbDpBdWRpZW5jZT48L3NhbWw6QXVkaWVuY2VSZXN0cmljdGlvbj48L3NhbWw6Q29uZGl0aW9ucz48c2FtbDpBdXRoblN0YXRlbWVudCBBdXRobkluc3RhbnQ9IjIwMTktMTEtMTdUMTc6NTM6MzlaIiBTZXNzaW9uTm90T25PckFmdGVyPSIyMDE5LTExLTE4VDAxOjUzOjM5WiIgU2Vzc2lvbkluZGV4PSJfNGZlN2MwZDE1NzJkNjRiMjdmOTMwYWE2ZjIzNmE2ZjQyZTkzMDkwMWNjIj48c2FtbDpBdXRobkNvbnRleHQ+PHNhbWw6QXV0aG5Db250ZXh0Q2xhc3NSZWY+dXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmFjOmNsYXNzZXM6UGFzc3dvcmQ8L3NhbWw6QXV0aG5Db250ZXh0Q2xhc3NSZWY+PC9zYW1sOkF1dGhuQ29udGV4dD48L3NhbWw6QXV0aG5TdGF0ZW1lbnQ+PHNhbWw6QXR0cmlidXRlU3RhdGVtZW50PjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJ1aWQiIE5hbWVGb3JtYXQ9InVybjpvYXNpczpuYW1lczp0YzpTQU1MOjIuMDphdHRybmFtZS1mb3JtYXQ6YmFzaWMiPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPnVzZXI8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0iZmlyc3RfbmFtZSIgTmFtZUZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDpiYXNpYyI+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+QmFycnk8L3NhbWw6QXR0cmlidXRlVmFsdWU+PC9zYW1sOkF0dHJpYnV0ZT48c2FtbDpBdHRyaWJ1dGUgTmFtZT0ibGFzdF9uYW1lIiBOYW1lRm9ybWF0PSJ1cm46b2FzaXM6bmFtZXM6dGM6U0FNTDoyLjA6YXR0cm5hbWUtZm9ybWF0OmJhc2ljIj48c2FtbDpBdHRyaWJ1dGVWYWx1ZSB4c2k6dHlwZT0ieHM6c3RyaW5nIj5TY290dDwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJlbWFpbCIgTmFtZUZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDpiYXNpYyI+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+dXNlckBleGFtcGxlLmNvbTwvc2FtbDpBdHRyaWJ1dGVWYWx1ZT48L3NhbWw6QXR0cmlidXRlPjxzYW1sOkF0dHJpYnV0ZSBOYW1lPSJ1c2VyX2dyb3VwcyIgTmFtZUZvcm1hdD0idXJuOm9hc2lzOm5hbWVzOnRjOlNBTUw6Mi4wOmF0dHJuYW1lLWZvcm1hdDpiYXNpYyI+PHNhbWw6QXR0cmlidXRlVmFsdWUgeHNpOnR5cGU9InhzOnN0cmluZyI+bWVtYmVyPC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjxzYW1sOkF0dHJpYnV0ZVZhbHVlIHhzaTp0eXBlPSJ4czpzdHJpbmciPmFkbWluPC9zYW1sOkF0dHJpYnV0ZVZhbHVlPjwvc2FtbDpBdHRyaWJ1dGU+PC9zYW1sOkF0dHJpYnV0ZVN0YXRlbWVudD48L3NhbWw6QXNzZXJ0aW9uPjwvc2FtbHA6UmVzcG9uc2U+';

    protected $sloResponseData = 'fZHRa8IwEMb/lZJ3bdJa04a2MOYYglOY4sNe5JKms9gmpZfC/vxF3ZjC8OXgLvl938ddjtC1vVjZTzu6d429NaiDr641KC5PBRkHIyxgg8JAp1E4JbZPbysRTanoB+ussi25QR4TgKgH11hDguWiIIeawTxOaK1iPYt5XcczHUlJeVRlMklBJjOuM1qDVCTY6wE9WRAv5HHEUS8NOjDOjyjLJoxNGN+xVESpSNgHCRYaXWPAXaijc70IQ2ntyUPqNG2tgjY8Z45CbNFLmt8V7GxBNuuX1eZ1uT7EcZJKAE4TJhXPaMxlVlFffPKKJnXE5ryusoiU+VlMXJIN5Y/feXRn1VR92GkHFTiY9sc+D2+p/HqRrQM34n33bCsd7KEd9eMd4+W32I5KaUQSlleHP9Hwv6uX3w==';

    protected $testCert = 'MIIEazCCAtOgAwIBAgIUe7a088Cnr4izmrnBEnx5q3HTMvYwDQYJKoZIhvcNAQELBQAwRTELMAkGA1UEBhMCR0IxEzARBgNVBAgMClNvbWUtU3RhdGUxITAfBgNVBAoMGEludGVybmV0IFdpZGdpdHMgUHR5IEx0ZDAeFw0xOTExMTYxMjE3MTVaFw0yOTExMTUxMjE3MTVaMEUxCzAJBgNVBAYTAkdCMRMwEQYDVQQIDApTb21lLVN0YXRlMSEwHwYDVQQKDBhJbnRlcm5ldCBXaWRnaXRzIFB0eSBMdGQwggGiMA0GCSqGSIb3DQEBAQUAA4IBjwAwggGKAoIBgQDzLe9FfdyplTxHp4SuQ9gQtZT3t+SDfvEL72ppCfFZw7+B5s5B/T73aXpoQ3S53pGI1RIWCge2iCUQ2tzm27aSNH0iu9aJYcUQZ/RITqd0ayyDks1NA2PT3TW6t3m7KV5re4P0Nb+YDeuyHdkz+jcMtpn8CmBoT0H+skha0hiqINkjkRPiHvLHVGp+tHUEA/I6mN4aB/UExSTLs79NsLUfteqqxe9+tvdUaToyDPrhPFjONs+9NKCkzIC6vcv7J6AtuKG6nET+zB9yOWgtGYQifXqQA2y5dL81BB0q5uMaBLS2pq3aPPjzU2F3+EysjySWTnCkfk7C5SsCXRu8Q+U95tunpNfwf5olE6Was48NMM+PwV7iCNMPkNzllq6PCiM+P8DrMSczzUZZQUSv6dSwPCo+YSVimEM0Og3XJTiNhQ5ANlaIn66Kw5gfoBfuiXmyIKiSDyAiDYmFaf4395wWwLkTR+cw8WfjaHswKZTomn1MR3OJsY2UJ0eRBYM+YSsCAwEAAaNTMFEwHQYDVR0OBBYEFImp2CYCGfcb7w91H/cShTCkXwR/MB8GA1UdIwQYMBaAFImp2CYCGfcb7w91H/cShTCkXwR/MA8GA1UdEwEB/wQFMAMBAf8wDQYJKoZIhvcNAQELBQADggGBAA+g/C7uL9ln+W+qBknLW81kojYflgPK1I1MHIwnMvl/ZTHX4dRXKDrk7KcUq1KjqajNV66f1cakp03IijBiO0Xi1gXUZYLoCiNGUyyp9XloiIy9Xw2PiWnrw0+yZyvVssbehXXYJl4RihBjBWul9R4wMYLOUSJDe2WxcUBhJnxyNRs+P0xLSQX6B2n6nxoDko4p07s8ZKXQkeiZ2iwFdTxzRkGjthMUv704nzsVGBT0DCPtfSaO5KJZW1rCs3yiMthnBxq4qEDOQJFIl+/LD71KbB9vZcW5JuavzBFmkKGNro/6G1I7el46IR4wijTyNFCYUuD9dtignNmpWtN8OW+ptiL/jtTySWukjys0s+vLn83CVvjB0dJtVAIYOgXFdIuii66gczwwM/LGiOExJn0dTNzsJ/IYhpxL4FBEuP0pskY0o0aUlJ2LS2j+wSQTRKsBgMjyrUrekle2ODStStn3eabjIx0/FHlpFr0jNIm/oMP7kwjtUX4zaNe47QI4Gg==';
}
