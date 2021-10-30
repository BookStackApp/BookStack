<?php

namespace Tests\Auth;

use BookStack\Auth\Access\Ldap;
use BookStack\Auth\Access\LdapService;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use Mockery\MockInterface;
use Tests\TestCase;
use Tests\TestResponse;

class LdapTest extends TestCase
{
    /**
     * @var MockInterface
     */
    protected $mockLdap;

    protected $mockUser;
    protected $resourceId = 'resource-test';

    protected function setUp(): void
    {
        parent::setUp();
        if (!defined('LDAP_OPT_REFERRALS')) {
            define('LDAP_OPT_REFERRALS', 1);
        }
        config()->set([
            'auth.method'                          => 'ldap',
            'auth.defaults.guard'                  => 'ldap',
            'services.ldap.base_dn'                => 'dc=ldap,dc=local',
            'services.ldap.email_attribute'        => 'mail',
            'services.ldap.display_name_attribute' => 'cn',
            'services.ldap.id_attribute'           => 'uid',
            'services.ldap.user_to_groups'         => false,
            'services.ldap.version'                => '3',
            'services.ldap.user_filter'            => '(&(uid=${user}))',
            'services.ldap.follow_referrals'       => false,
            'services.ldap.tls_insecure'           => false,
            'services.ldap.thumbnail_attribute'    => null,
        ]);
        $this->mockLdap = \Mockery::mock(Ldap::class);
        $this->app[Ldap::class] = $this->mockLdap;
        $this->mockUser = User::factory()->make();
    }

    protected function runFailedAuthLogin()
    {
        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->andReturn(['count' => 0]);
        $this->post('/login', ['username' => 'timmyjenkins', 'password' => 'cattreedog']);
    }

    protected function mockEscapes($times = 1)
    {
        $this->mockLdap->shouldReceive('escape')->times($times)->andReturnUsing(function ($val) {
            return ldap_escape($val);
        });
    }

    protected function mockExplodes($times = 1)
    {
        $this->mockLdap->shouldReceive('explodeDn')->times($times)->andReturnUsing(function ($dn, $withAttrib) {
            return ldap_explode_dn($dn, $withAttrib);
        });
    }

    protected function mockUserLogin(?string $email = null): TestResponse
    {
        return $this->post('/login', [
            'username' => $this->mockUser->name,
            'password' => $this->mockUser->password,
        ] + ($email ? ['email' => $email] : []));
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
                'cn'  => [$this->mockUser->name],
                'dn'  => ['dc=test' . config('services.ldap.base_dn')],
            ]]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
        $resp = $this->followRedirects($resp);
        $resp->assertSee('Please enter an email to use for this account.');
        $resp->assertSee($this->mockUser->name);

        $resp = $this->followingRedirects()->mockUserLogin($this->mockUser->email);
        $resp->assertElementExists('#home-default');
        $resp->assertSee($this->mockUser->name);
        $this->assertDatabaseHas('users', [
            'email'            => $this->mockUser->email,
            'email_confirmed'  => false,
            'external_auth_id' => $this->mockUser->name,
        ]);
    }

    public function test_email_domain_restriction_active_on_new_ldap_login()
    {
        $this->setSettings([
            'registration-restrict' => 'testing.com',
        ]);

        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn'  => [$this->mockUser->name],
                'dn'  => ['dc=test' . config('services.ldap.base_dn')],
            ]]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
        $this->followRedirects($resp)->assertSee('Please enter an email to use for this account.');

        $email = 'tester@invaliddomain.com';
        $resp = $this->mockUserLogin($email);
        $resp->assertRedirect('/login');
        $this->followRedirects($resp)->assertSee('That email domain does not have access to this application');

        $this->assertDatabaseMissing('users', ['email' => $email]);
    }

    public function test_login_works_when_no_uid_provided_by_ldap_server()
    {
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');

        $this->commonLdapMocks(1, 1, 1, 2, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn'   => [$this->mockUser->name],
                'dn'   => $ldapDn,
                'mail' => [$this->mockUser->email],
            ]]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/');
        $this->followRedirects($resp)->assertSee($this->mockUser->name);
        $this->assertDatabaseHas('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $ldapDn]);
    }

    public function test_a_custom_uid_attribute_can_be_specified_and_is_used_properly()
    {
        config()->set(['services.ldap.id_attribute' => 'my_custom_id']);

        $this->commonLdapMocks(1, 1, 1, 2, 1);
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn'           => [$this->mockUser->name],
                'dn'           => $ldapDn,
                'my_custom_id' => ['cooluser456'],
                'mail'         => [$this->mockUser->email],
            ]]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/');
        $this->followRedirects($resp)->assertSee($this->mockUser->name);
        $this->assertDatabaseHas('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => 'cooluser456']);
    }

    public function test_initial_incorrect_credentials()
    {
        $this->commonLdapMocks(1, 1, 1, 0, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn'  => [$this->mockUser->name],
                'dn'  => ['dc=test' . config('services.ldap.base_dn')],
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(2)->andReturn(true, false);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
        $this->followRedirects($resp)->assertSee('These credentials do not match our records.');
        $this->assertDatabaseMissing('users', ['external_auth_id' => $this->mockUser->name]);
    }

    public function test_login_not_found_username()
    {
        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 0]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
        $this->followRedirects($resp)->assertSee('These credentials do not match our records.');
        $this->assertDatabaseMissing('users', ['external_auth_id' => $this->mockUser->name]);
    }

    public function test_create_user_form()
    {
        $userForm = $this->asAdmin()->get('/settings/users/create');
        $userForm->assertDontSee('Password');

        $save = $this->post('/settings/users/create', [
            'name'  => $this->mockUser->name,
            'email' => $this->mockUser->email,
        ]);
        $save->assertSessionHasErrors(['external_auth_id' => 'The external auth id field is required.']);

        $save = $this->post('/settings/users/create', [
            'name'             => $this->mockUser->name,
            'email'            => $this->mockUser->email,
            'external_auth_id' => $this->mockUser->name,
        ]);
        $save->assertRedirect('/settings/users');
        $this->assertDatabaseHas('users', ['email' => $this->mockUser->email, 'external_auth_id' => $this->mockUser->name, 'email_confirmed' => true]);
    }

    public function test_user_edit_form()
    {
        $editUser = $this->getNormalUser();
        $editPage = $this->asAdmin()->get("/settings/users/{$editUser->id}");
        $editPage->assertSee('Edit User');
        $editPage->assertDontSee('Password');

        $update = $this->put("/settings/users/{$editUser->id}", [
            'name'             => $editUser->name,
            'email'            => $editUser->email,
            'external_auth_id' => 'test_auth_id',
        ]);
        $update->assertRedirect('/settings/users');
        $this->assertDatabaseHas('users', ['email' => $editUser->email, 'external_auth_id' => 'test_auth_id']);
    }

    public function test_registration_disabled()
    {
        $this->followingRedirects()->get('/register')->assertElementContains('#content', 'Log In');
    }

    public function test_non_admins_cannot_change_auth_id()
    {
        $testUser = $this->getNormalUser();
        $this->actingAs($testUser)
            ->get('/settings/users/' . $testUser->id)
            ->assertDontSee('External Authentication');
    }

    public function test_login_maps_roles_and_retains_existing_roles()
    {
        $roleToReceive = Role::factory()->create(['display_name' => 'LdapTester']);
        $roleToReceive2 = Role::factory()->create(['display_name' => 'LdapTester Second']);
        $existingRole = Role::factory()->create(['display_name' => 'ldaptester-existing']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();
        $this->mockUser->attachRole($existingRole);

        app('config')->set([
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => false,
        ]);

        $this->commonLdapMocks(1, 1, 4, 5, 4, 6);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(4)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => ['dc=test' . config('services.ldap.base_dn')],
                'mail'     => [$this->mockUser->email],
                'memberof' => [
                    'count' => 2,
                    0       => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                    1       => 'cn=ldaptester-second,ou=groups,dc=example,dc=com',
                ],
            ]]);

        $this->mockUserLogin()->assertRedirect('/');

        $user = User::where('email', $this->mockUser->email)->first();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive2->id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $existingRole->id,
        ]);
    }

    public function test_login_maps_roles_and_removes_old_roles_if_set()
    {
        $roleToReceive = Role::factory()->create(['display_name' => 'LdapTester']);
        $existingRole = Role::factory()->create(['display_name' => 'ldaptester-existing']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();
        $this->mockUser->attachRole($existingRole);

        app('config')->set([
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 3, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(3)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => ['dc=test' . config('services.ldap.base_dn')],
                'mail'     => [$this->mockUser->email],
                'memberof' => [
                    'count' => 1,
                    0       => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                ],
            ]]);

        $this->mockUserLogin()->assertRedirect('/');

        $user = User::query()->where('email', $this->mockUser->email)->first();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id,
        ]);
        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $existingRole->id,
        ]);
    }

    public function test_external_auth_id_visible_in_roles_page_when_ldap_active()
    {
        $role = Role::factory()->create(['display_name' => 'ldaptester', 'external_auth_id' => 'ex-auth-a, test-second-param']);
        $this->asAdmin()->get('/settings/roles/' . $role->id)
            ->assertSee('ex-auth-a');
    }

    public function test_login_maps_roles_using_external_auth_ids_if_set()
    {
        $roleToReceive = Role::factory()->create(['display_name' => 'ldaptester', 'external_auth_id' => 'test-second-param, ex-auth-a']);
        $roleToNotReceive = Role::factory()->create(['display_name' => 'ex-auth-a', 'external_auth_id' => 'test-second-param']);

        app('config')->set([
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 3, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(3)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => ['dc=test' . config('services.ldap.base_dn')],
                'mail'     => [$this->mockUser->email],
                'memberof' => [
                    'count' => 1,
                    0       => 'cn=ex-auth-a,ou=groups,dc=example,dc=com',
                ],
            ]]);

        $this->mockUserLogin()->assertRedirect('/');

        $user = User::query()->where('email', $this->mockUser->email)->first();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id,
        ]);
        $this->assertDatabaseMissing('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToNotReceive->id,
        ]);
    }

    public function test_login_group_mapping_does_not_conflict_with_default_role()
    {
        $roleToReceive = Role::factory()->create(['display_name' => 'LdapTester']);
        $roleToReceive2 = Role::factory()->create(['display_name' => 'LdapTester Second']);
        $this->mockUser->forceFill(['external_auth_id' => $this->mockUser->name])->save();

        setting()->put('registration-role', $roleToReceive->id);

        app('config')->set([
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 4, 5, 4, 6);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(4)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => ['dc=test' . config('services.ldap.base_dn')],
                'mail'     => [$this->mockUser->email],
                'memberof' => [
                    'count' => 2,
                    0       => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                    1       => 'cn=ldaptester-second,ou=groups,dc=example,dc=com',
                ],
            ]]);

        $this->mockUserLogin()->assertRedirect('/');

        $user = User::query()->where('email', $this->mockUser->email)->first();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id,
        ]);
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive2->id,
        ]);
    }

    public function test_login_uses_specified_display_name_attribute()
    {
        app('config')->set([
            'services.ldap.display_name_attribute' => 'displayName',
        ]);

        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'         => [$this->mockUser->name],
                'cn'          => [$this->mockUser->name],
                'dn'          => ['dc=test' . config('services.ldap.base_dn')],
                'displayname' => 'displayNameAttribute',
            ]]);

        $this->mockUserLogin()->assertRedirect('/login');
        $this->get('/login')->assertSee('Please enter an email to use for this account.');

        $resp = $this->mockUserLogin($this->mockUser->email);
        $resp->assertRedirect('/');
        $this->get('/')->assertSee('displayNameAttribute');
        $this->assertDatabaseHas('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $this->mockUser->name, 'name' => 'displayNameAttribute']);
    }

    public function test_login_uses_default_display_name_attribute_if_specified_not_present()
    {
        app('config')->set([
            'services.ldap.display_name_attribute' => 'displayName',
        ]);

        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn'  => [$this->mockUser->name],
                'dn'  => ['dc=test' . config('services.ldap.base_dn')],
            ]]);

        $this->mockUserLogin()->assertRedirect('/login');
        $this->get('/login')->assertSee('Please enter an email to use for this account.');

        $resp = $this->mockUserLogin($this->mockUser->email);
        $resp->assertRedirect('/');
        $this->get('/')->assertSee($this->mockUser->name);
        $this->assertDatabaseHas('users', [
            'email'            => $this->mockUser->email,
            'email_confirmed'  => false,
            'external_auth_id' => $this->mockUser->name,
            'name'             => $this->mockUser->name,
        ]);
    }

    protected function checkLdapReceivesCorrectDetails($serverString, $expectedHost, $expectedPort)
    {
        app('config')->set([
            'services.ldap.server' => $serverString,
        ]);

        // Standard mocks
        $this->commonLdapMocks(0, 1, 1, 2, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)->andReturn(['count' => 1, 0 => [
            'uid' => [$this->mockUser->name],
            'cn'  => [$this->mockUser->name],
            'dn'  => ['dc=test' . config('services.ldap.base_dn')],
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
                'cn'  => [$this->mockUser->name],
                'dn'  => ['dc=test' . config('services.ldap.base_dn')],
            ]]);

        $resp = $this->post('/login', [
            'username' => $this->mockUser->name,
            'password' => $this->mockUser->password,
        ]);
        $resp->assertJsonStructure([
            'details_from_ldap'        => [],
            'details_bookstack_parsed' => [],
        ]);
    }

    public function test_start_tls_called_if_option_set()
    {
        config()->set(['services.ldap.start_tls' => true]);
        $this->mockLdap->shouldReceive('startTls')->once()->andReturn(true);
        $this->runFailedAuthLogin();
    }

    public function test_connection_fails_if_tls_fails()
    {
        config()->set(['services.ldap.start_tls' => true]);
        $this->mockLdap->shouldReceive('startTls')->once()->andReturn(false);
        $this->commonLdapMocks(1, 1, 0, 0, 0);
        $resp = $this->post('/login', ['username' => 'timmyjenkins', 'password' => 'cattreedog']);
        $resp->assertStatus(500);
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
                'cn'  => [$this->mockUser->name],
                'dn'  => ['dc=test' . config('services.ldap.base_dn')],
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
                'uid'  => [$this->mockUser->name],
                'cn'   => [$this->mockUser->name],
                'dn'   => ['dc=test' . config('services.ldap.base_dn')],
                'mail' => 'tester@example.com',
            ]], ['count' => 1, 0 => [
                'uid'  => ['Barry'],
                'cn'   => ['Scott'],
                'dn'   => ['dc=bscott' . config('services.ldap.base_dn')],
                'mail' => 'tester@example.com',
            ]]);

        // First user login
        $this->mockUserLogin()->assertRedirect('/');

        // Second user login
        auth()->logout();
        $resp = $this->followingRedirects()->post('/login', ['username' => 'bscott', 'password' => 'pass']);
        $resp->assertSee('A user with the email tester@example.com already exists but with different credentials');
    }

    public function test_login_with_email_confirmation_required_maps_groups_but_shows_confirmation_screen()
    {
        $roleToReceive = Role::factory()->create(['display_name' => 'LdapTester']);
        $user = User::factory()->make();
        setting()->put('registration-confirmation', 'true');

        app('config')->set([
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 6, 8, 6, 4);
        $this->mockLdap->shouldReceive('searchAndGetEntries')
            ->times(6)
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$user->name],
                'cn'       => [$user->name],
                'dn'       => ['dc=test' . config('services.ldap.base_dn')],
                'mail'     => [$user->email],
                'memberof' => [
                    'count' => 1,
                    0       => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                ],
            ]]);

        $login = $this->followingRedirects()->mockUserLogin();
        $login->assertSee('Thanks for registering!');
        $this->assertDatabaseHas('users', [
            'email'           => $user->email,
            'email_confirmed' => false,
        ]);

        $user = User::query()->where('email', '=', $user->email)->first();
        $this->assertDatabaseHas('role_user', [
            'user_id' => $user->id,
            'role_id' => $roleToReceive->id,
        ]);

        $this->assertNull(auth()->user());

        $homePage = $this->get('/');
        $homePage->assertRedirect('/login');

        $login = $this->followingRedirects()->mockUserLogin();
        $login->assertSee('Email Address Not Confirmed');
    }

    public function test_failed_logins_are_logged_when_message_configured()
    {
        $log = $this->withTestLogger();
        config()->set(['logging.failed_login.message' => 'Failed login for %u']);
        $this->runFailedAuthLogin();
        $this->assertTrue($log->hasWarningThatContains('Failed login for timmyjenkins'));
    }

    public function test_thumbnail_attribute_used_as_user_avatar_if_configured()
    {
        config()->set(['services.ldap.thumbnail_attribute' => 'jpegPhoto']);

        $this->commonLdapMocks(1, 1, 1, 2, 1);
        $ldapDn = 'cn=test-user,dc=test' . config('services.ldap.base_dn');
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn'        => [$this->mockUser->name],
                'dn'        => $ldapDn,
                'jpegphoto' => [base64_decode('/9j/2wBDAAMCAgICAgMCAgIDAwMDBAYEBAQEBAgGBgUGCQgKCgkICQkKDA8MCgsOCwkJDRENDg8Q
EBEQCgwSExIQEw8QEBD/yQALCAABAAEBAREA/8wABgAQEAX/2gAIAQEAAD8A0s8g/9k=')],
                'mail' => [$this->mockUser->email],
            ]]);

        $this->mockUserLogin()
            ->assertRedirect('/');

        $user = User::query()->where('email', '=', $this->mockUser->email)->first();
        $this->assertNotNull($user->avatar);
        $this->assertEquals('8c90748342f19b195b9c6b4eff742ded', md5_file(public_path($user->avatar->path)));
    }
}
