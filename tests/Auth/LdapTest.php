<?php namespace Tests;

use BookStack\Auth\Role;
use BookStack\Auth\Access\Ldap;
use BookStack\Auth\User;
use Mockery\MockInterface;

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
        app('config')->set([
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

    public function test_login()
    {
        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $this->mockLdap->shouldReceive('setOption')->times(2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(4)->andReturn(true);
        $this->mockEscapes(2);

        $this->mockUserLogin()
            ->seePageIs('/login')->see('Please enter an email to use for this account.');

        $this->type($this->mockUser->email, '#email')
            ->press('Log In')
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $this->mockUser->name]);
    }

    public function test_login_works_when_no_uid_provided_by_ldap_server()
    {
        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');
        $this->mockLdap->shouldReceive('setOption')->times(1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn' => [$this->mockUser->name],
                'dn' => $ldapDn,
                'mail' => [$this->mockUser->email]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(2)->andReturn(true);
        $this->mockEscapes(1);

        $this->mockUserLogin()
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $ldapDn]);
    }

    public function test_a_custom_uid_attribute_can_be_specified_and_is_used_properly()
    {
        config()->set(['services.ldap.id_attribute' => 'my_custom_id']);
        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');
        $this->mockLdap->shouldReceive('setOption')->times(1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn' => [$this->mockUser->name],
                'dn' => $ldapDn,
                'my_custom_id' => ['cooluser456'],
                'mail' => [$this->mockUser->email]
            ]]);


        $this->mockLdap->shouldReceive('bind')->times(2)->andReturn(true);
        $this->mockEscapes(1);

        $this->mockUserLogin()
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => 'cooluser456']);
    }

    public function test_initial_incorrect_details()
    {
        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $this->mockLdap->shouldReceive('setOption')->times(1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(2)->andReturn(true, false);
        $this->mockEscapes(1);

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
        $roleToReceive = factory(Role::class)->create(['name' => 'ldaptester', 'display_name' => 'LdapTester']);
        $roleToReceive2 = factory(Role::class)->create(['name' => 'ldaptester-second', 'display_name' => 'LdapTester Second']);
        $existingRole = factory(Role::class)->create(['name' => 'ldaptester-existing']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();
        $this->mockUser->attachRole($existingRole);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => false,
        ]);
        $this->mockLdap->shouldReceive('connect')->times(1)->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->times(1);
        $this->mockLdap->shouldReceive('setOption')->times(4);
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
        $this->mockLdap->shouldReceive('bind')->times(5)->andReturn(true);
        $this->mockEscapes(4);
        $this->mockExplodes(6);

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
        $roleToReceive = factory(Role::class)->create(['name' => 'ldaptester', 'display_name' => 'LdapTester']);
        $existingRole = factory(Role::class)->create(['name' => 'ldaptester-existing']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();
        $this->mockUser->attachRole($existingRole);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);
        $this->mockLdap->shouldReceive('connect')->times(1)->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->times(1);
        $this->mockLdap->shouldReceive('setOption')->times(3);
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
        $this->mockLdap->shouldReceive('bind')->times(4)->andReturn(true);
        $this->mockEscapes(3);
        $this->mockExplodes(2);

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
        $role = factory(Role::class)->create(['name' => 'ldaptester', 'external_auth_id' => 'ex-auth-a, test-second-param']);
        $this->asAdmin()->visit('/settings/roles/' . $role->id)
            ->see('ex-auth-a');
    }

    public function test_login_maps_roles_using_external_auth_ids_if_set()
    {
        $roleToReceive = factory(Role::class)->create(['name' => 'ldaptester', 'external_auth_id' => 'test-second-param, ex-auth-a']);
        $roleToNotReceive = factory(Role::class)->create(['name' => 'ldaptester-not-receive', 'display_name' => 'ex-auth-a', 'external_auth_id' => 'test-second-param']);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);
        $this->mockLdap->shouldReceive('connect')->times(1)->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->times(1);
        $this->mockLdap->shouldReceive('setOption')->times(3);
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
        $this->mockLdap->shouldReceive('bind')->times(4)->andReturn(true);
        $this->mockEscapes(3);
        $this->mockExplodes(2);

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
        $roleToReceive = factory(Role::class)->create(['name' => 'ldaptester', 'display_name' => 'LdapTester']);
        $roleToReceive2 = factory(Role::class)->create(['name' => 'ldaptester-second', 'display_name' => 'LdapTester Second']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();

        setting()->put('registration-role', $roleToReceive->id);

        app('config')->set([
            'services.ldap.user_to_groups' => true,
            'services.ldap.group_attribute' => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);
        $this->mockLdap->shouldReceive('connect')->times(1)->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->times(1);
        $this->mockLdap->shouldReceive('setOption')->times(4);
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
        $this->mockLdap->shouldReceive('bind')->times(5)->andReturn(true);
        $this->mockEscapes(4);
        $this->mockExplodes(6);

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

        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $this->mockLdap->shouldReceive('setOption')->times(2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')],
                'displayname' => 'displayNameAttribute'
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(4)->andReturn(true);
        $this->mockEscapes(2);

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

        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $this->mockLdap->shouldReceive('setOption')->times(2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(4)->andReturn(true);
        $this->mockEscapes(2);

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
        $this->mockLdap->shouldReceive('setVersion')->once();
        $this->mockLdap->shouldReceive('setOption')->times(1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)->andReturn(['count' => 1, 0 => [
            'uid' => [$this->mockUser->name],
            'cn' => [$this->mockUser->name],
            'dn' => ['dc=test' . config('services.ldap.base_dn')]
        ]]);
        $this->mockLdap->shouldReceive('bind')->times(2)->andReturn(true);
        $this->mockEscapes(1);

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
}
