<?php namespace Tests\Auth;

use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Role;
use BookStack\Auth\Access\Ldap;
use BookStack\Auth\User;
use Mockery\MockInterface;
use Tests\BrowserKitTest;

class LdapTest extends BrowserKitTest
{

    /**
     * @var MockInterface
     */
    protected $mockLdap;

    protected $mockUser;
    protected $resourceId = 'resource-test';

    public function setUp(): void
    {
        parent::setUp();
        if (!defined('LDAP_OPT_REFERRALS')) define('LDAP_OPT_REFERRALS', 1);
        config()->set([
            'auth.method' => 'ldap',
            'auth.defaults.guard' => 'ldap',
            'services.ldap.base_dn' => 'dc=ldap,dc=local',
            'services.ldap.email_attribute' => 'mail',
            'services.ldap.display_name_attribute' => 'cn',
            'services.ldap.id_attribute' => 'uid',
            'services.ldap.user_to_groups' => false,
            'services.ldap.version' => '3',
            'services.ldap.user_filter' => '(&(uid=${user}))',
            'services.ldap.follow_referrals' => false,
            'services.ldap.tls_insecure' => false,
        ]);
        $this->mockLdap = \Mockery::mock(Ldap::class);
        $this->app[Ldap::class] = $this->mockLdap;
        $this->mockUser = factory(User::class)->make();
    }

    protected function mockEscapes($times = 1)
    {
        $this->mockLdap->shouldReceive('escape')->times($times)->andReturnUsing(function($val) {
            return ldap_escape($val);
        });
    }

    protected function mockExplodes($times = 1)
    {
        $this->mockLdap->shouldReceive('explodeDn')->times($times)->andReturnUsing(function($dn, $withAttrib) {
            return ldap_explode_dn($dn, $withAttrib);
        });
    }

    protected function mockUserLogin()
    {
        return $this->visit('/login')
            ->see('Username')
            ->type($this->mockUser->name, '#username')
            ->type($this->mockUser->password, '#password')
            ->press('Log In');
    }

    /**
     * Set LDAP method mocks for things we commonly call without altering.
     */
    protected function commonLdapMocks(int $connects = 1, int $versions = 1, int $options = 2, int $binds = 4, int $escapes = 2, int $explodes = 0)
    {
        $this->mockLdap->shouldReceive('connect')->times($connects)->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->times($versions);
        $this->mockLdap->shouldReceive('setOption')->times($options);
        $this->mockLdap->shouldReceive('bind')->times($binds)->andReturn(true);
        $this->mockEscapes($escapes);
        $this->mockExplodes($explodes);
    }

    public function test_login()
    {
        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);

        $this->mockUserLogin()
            ->seePageIs('/login')->see('Please enter an email to use for this account.');

        $this->type($this->mockUser->email, '#email')
            ->press('Log In')
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $this->mockUser->name]);
    }

    public function test_email_domain_restriction_active_on_new_ldap_login()
    {
        $this->setSettings([
            'registration-restrict' => 'testing.com'
        ]);

        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);

        $this->mockUserLogin()
            ->seePageIs('/login')
            ->see('Please enter an email to use for this account.');

        $email = 'tester@invaliddomain.com';

        $this->type($email, '#email')
            ->press('Log In')
            ->seePageIs('/login')
            ->see('That email domain does not have access to this application')
            ->dontSeeInDatabase('users', ['email' => $email]);
    }

    public function test_login_works_when_no_uid_provided_by_ldap_server()
    {
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');

        $this->commonLdapMocks(1, 1, 1, 2, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn' => [$this->mockUser->name],
                'dn' => $ldapDn,
                'mail' => [$this->mockUser->email]
            ]]);

        $this->mockUserLogin()
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $ldapDn]);
    }

    public function test_a_custom_uid_attribute_can_be_specified_and_is_used_properly()
    {
        config()->set(['services.ldap.id_attribute' => 'my_custom_id']);

        $this->commonLdapMocks(1, 1, 1, 2, 1);
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn' => [$this->mockUser->name],
                'dn' => $ldapDn,
                'my_custom_id' => ['cooluser456'],
                'mail' => [$this->mockUser->email]
            ]]);


        $this->mockUserLogin()
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => 'cooluser456']);
    }

    public function test_initial_incorrect_credentials()
    {
        $this->commonLdapMocks(1, 1, 1, 0, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(2)->andReturn(true, false);

        $this->mockUserLogin()
            ->seePageIs('/login')->see('These credentials do not match our records.')
            ->dontSeeInDatabase('users', ['external_auth_id' => $this->mockUser->name]);
    }

    public function test_login_not_found_username()
    {
        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 0]);

        $this->mockUserLogin()
            ->seePageIs('/login')->see('These credentials do not match our records.')
            ->dontSeeInDatabase('users', ['external_auth_id' => $this->mockUser->name]);
    }


    public function test_create_user_form()
    {
        $this->asAdmin()->visit('/settings/users/create')
            ->dontSee('Password')
            ->type($this->mockUser->name, '#name')
            ->type($this->mockUser->email, '#email')
            ->press('Save')
            ->see('The external auth id field is required.')
            ->type($this->mockUser->name, '#external_auth_id')
            ->press('Save')
            ->seePageIs('/settings/users')
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'external_auth_id' => $this->mockUser->name, 'email_confirmed' => true]);
    }

    public function test_user_edit_form()
    {
        $editUser = $this->getNormalUser();
        $this->asAdmin()->visit('/settings/users/' . $editUser->id)
            ->see('Edit User')
            ->dontSee('Password')
            ->type('test_auth_id', '#external_auth_id')
            ->press('Save')
            ->seePageIs('/settings/users')
            ->seeInDatabase('users', ['email' => $editUser->email, 'external_auth_id' => 'test_auth_id']);
    }

    public function test_registration_disabled()
    {
        $this->visit('/register')
            ->seePageIs('/login');
    }

    public function test_non_admins_cannot_change_auth_id()
    {
        $testUser = $this->getNormalUser();
        $this->actingAs($testUser)->visit('/settings/users/' . $testUser->id)
            ->dontSee('External Authentication');
    }

    public function test_login_maps_roles_and_retains_existing_roles()
    {
        $roleToReceive = factory(Role::class)->create(['display_name' => 'LdapTester']);
        $roleToReceive2 = factory(Role::class)->create(['display_name' => 'LdapTester Second']);
        $existingRole = factory(Role::class)->create(['display_name' => 'ldaptester-existing']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();
        $this->mockUser->attachRole($existingRole);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => false,
        ]);

        $this->commonLdapMocks(1, 1, 4, 5, 4, 6);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(4)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => [$this->mockUser->email],
                'memberof' => [
                    'count' => 2,
                    0 => "cn=ldaptester,ou=groups,dc=example,dc=com",
                    1 => "cn=ldaptester-second,ou=groups,dc=example,dc=com",
                ]
            ]]);

        $this->mockUserLogin()->seePageIs('/');

        $user = User::where('email', $this->mockUser->email)->first();
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id
        ]);
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive2->id
        ]);
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $existingRole->id
        ]);
    }

    public function test_login_maps_roles_and_removes_old_roles_if_set()
    {
        $roleToReceive = factory(Role::class)->create(['display_name' => 'LdapTester']);
        $existingRole = factory(Role::class)->create(['display_name' => 'ldaptester-existing']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();
        $this->mockUser->attachRole($existingRole);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 3, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(3)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => [$this->mockUser->email],
                'memberof' => [
                    'count' => 1,
                    0 => "cn=ldaptester,ou=groups,dc=example,dc=com",
                ]
            ]]);

        $this->mockUserLogin()->seePageIs('/');

        $user = User::where('email', $this->mockUser->email)->first();
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id
        ]);
        $this->dontSeeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $existingRole->id
        ]);
    }

    public function test_external_auth_id_visible_in_roles_page_when_ldap_active()
    {
        $role = factory(Role::class)->create(['display_name' => 'ldaptester', 'external_auth_id' => 'ex-auth-a, test-second-param']);
        $this->asAdmin()->visit('/settings/roles/' . $role->id)
            ->see('ex-auth-a');
    }

    public function test_login_maps_roles_using_external_auth_ids_if_set()
    {
        $roleToReceive = factory(Role::class)->create(['display_name' => 'ldaptester', 'external_auth_id' => 'test-second-param, ex-auth-a']);
        $roleToNotReceive = factory(Role::class)->create(['display_name' => 'ex-auth-a', 'external_auth_id' => 'test-second-param']);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 3, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(3)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => [$this->mockUser->email],
                'memberof' => [
                    'count' => 1,
                    0 => "cn=ex-auth-a,ou=groups,dc=example,dc=com",
                ]
            ]]);

        $this->mockUserLogin()->seePageIs('/');

        $user = User::where('email', $this->mockUser->email)->first();
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id
        ]);
        $this->dontSeeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToNotReceive->id
        ]);
    }

    public function test_login_group_mapping_does_not_conflict_with_default_role()
    {
        $roleToReceive = factory(Role::class)->create(['display_name' => 'LdapTester']);
        $roleToReceive2 = factory(Role::class)->create(['display_name' => 'LdapTester Second']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();

        setting()->put('registration-role', $roleToReceive->id);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 4, 5, 4, 6);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(4)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => [$this->mockUser->email],
                'memberof' => [
                    'count' => 2,
                    0 => "cn=ldaptester,ou=groups,dc=example,dc=com",
                    1 => "cn=ldaptester-second,ou=groups,dc=example,dc=com",
                ]
            ]]);

        $this->mockUserLogin()->seePageIs('/');

        $user = User::where('email', $this->mockUser->email)->first();
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id
        ]);
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive2->id
        ]);
    }

    public function test_login_uses_specified_display_name_attribute()
    {
        app('config')->set([
            'services.ldap.display_name_attribute' => 'displayName'
        ]);

        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'displayname' => 'displayNameAttribute'
            ]]);

        $this->mockUserLogin()
            ->seePageIs('/login')->see('Please enter an email to use for this account.');

        $this->type($this->mockUser->email, '#email')
            ->press('Log In')
            ->seePageIs('/')
            ->see('displayNameAttribute')
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $this->mockUser->name, 'name' => 'displayNameAttribute']);
    }

    public function test_login_uses_default_display_name_attribute_if_specified_not_present()
    {
        app('config')->set([
            'services.ldap.display_name_attribute' => 'displayName'
        ]);

        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);

        $this->mockUserLogin()
            ->seePageIs('/login')->see('Please enter an email to use for this account.');

        $this->type($this->mockUser->email, '#email')
            ->press('Log In')
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $this->mockUser->name, 'name' => $this->mockUser->name]);
    }

    protected function checkLdapReceivesCorrectDetails($serverString, $expectedHost, $expectedPort)
    {
        app('config')->set([
            'services.ldap.server' => $serverString
        ]);

        // Standard mocks
        $this->commonLdapMocks(0, 1, 1, 2, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)->andReturn(['count' => 1, 0 => [
            'uid' => [$this->mockUser->name],
            'cn' => [$this->mockUser->name],
            'dn' => ['dc=test' . config('services.ldap.base_dn')]
        ]]);

        $this->mockLdap->shouldReceive('connect')->once()
            ->with($expectedHost, $expectedPort)->andReturn($this->resourceId);
        $this->mockUserLogin();
    }

    public function test_ldap_port_provided_on_host_if_host_is_full_uri()
    {
        $hostName = 'ldaps://bookstack:8080';
        $this->checkLdapReceivesCorrectDetails($hostName, $hostName, 389);
    }

    public function test_ldap_port_parsed_from_server_if_host_is_not_full_uri()
    {
        $this->checkLdapReceivesCorrectDetails('ldap.bookstack.com:8080', 'ldap.bookstack.com', 8080);
    }

    public function test_default_ldap_port_used_if_not_in_server_string_and_not_uri()
    {
        $this->checkLdapReceivesCorrectDetails('ldap.bookstack.com', 'ldap.bookstack.com', 389);
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

    public function test_dump_user_details_option_works()
    {
        config()->set(['services.ldap.dump_user_details' => true]);

        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);

        $this->post('/login', [
            'username' => $this->mockUser->name,
            'password' => $this->mockUser->password,
        ]);
        $this->seeJsonStructure([
            'details_from_ldap' => [],
            'details_bookstack_parsed' => [],
        ]);
    }

    public function test_ldap_attributes_can_be_binary_decoded_if_marked()
    {
        config()->set(['services.ldap.id_attribute' => 'BIN;uid']);
        $ldapService = app()->make(LdapService::class);
        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), ['cn', 'dn', 'uid', 'mail', 'cn'])
            ->andReturn(['count' => 1, 0 => [
                'uid' => [hex2bin('FFF8F7')],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);

        $details = $ldapService->getUserDetails('test');
        $this->assertEquals('fff8f7', $details['uid']);
    }

    public function test_new_ldap_user_login_with_already_used_email_address_shows_error_message_to_user()
    {
        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => 'tester@example.com',
            ]], ['count' => 1, 0 => [
                'uid' => ['Barry'],
                'cn' => ['Scott'],
                'dn' => ['dc=bscott' . config('services.ldap.base_dn')],
                'mail' => 'tester@example.com',
            ]]);

        // First user login
        $this->mockUserLogin()->seePageIs('/');

        // Second user login
        auth()->logout();
        $this->post('/login', ['username' => 'bscott', 'password' => 'pass'])->followRedirects();

        $this->see('A user with the email tester@example.com already exists but with different credentials');
    }

    public function test_login_with_email_confirmation_required_maps_groups_but_shows_confirmation_screen()
    {
        $roleToReceive = factory(Role::class)->create(['display_name' => 'LdapTester']);
        $user = factory(User::class)->make();
        setting()->put('registration-confirmation', 'true');

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 3, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')
            ->times(3)
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$user->name],
                'cn' => [$user->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => [$user->email],
                'memberof' => [
                    'count' => 1,
                    0 => "cn=ldaptester,ou=groups,dc=example,dc=com",
                ]
            ]]);

        $this->mockUserLogin()->seePageIs('/register/confirm');
        $this->seeInDatabase('users', [
            'email' => $user->email,
            'email_confirmed' => false,
        ]);

        $user  = User::query()->where('email', '=', $user->email)->first();
        $this->seeInDatabase('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id
        ]);

        $homePage = $this->get('/');
        $homePage->assertRedirectedTo('/register/confirm/awaiting');
    }

    public function test_failed_logins_are_logged_when_message_configured()
    {
        $log = $this->withTestLogger();
        config()->set(['logging.failed_login.message' => 'Failed login for %u']);

        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->andReturn(['count' => 0]);

        $this->post('/login', ['username' => 'timmyjenkins', 'password' => 'cattreedog']);
        $this->assertTrue($log->hasWarningThatContains('Failed login for timmyjenkins'));
    }
}
