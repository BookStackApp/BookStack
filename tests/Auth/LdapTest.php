<?php

namespace Tests\Auth;

use BookStack\Access\Ldap;
use BookStack\Access\LdapService;
use BookStack\Exceptions\LdapException;
use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Testing\TestResponse;
use Mockery\MockInterface;
use Tests\TestCase;

class LdapTest extends TestCase
{
    protected MockInterface $mockLdap;

    protected User $mockUser;
    protected string $resourceId = 'resource-test';

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
            'services.ldap.user_filter'            => '(&(uid={user}))',
            'services.ldap.follow_referrals'       => false,
            'services.ldap.tls_insecure'           => false,
            'services.ldap.tls_ca_cert'            => false,
            'services.ldap.thumbnail_attribute'    => null,
        ]);
        $this->mockLdap = $this->mock(Ldap::class);
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
    protected function commonLdapMocks(int $connects = 1, int $versions = 1, int $options = 2, int $binds = 4, int $escapes = 2, int $explodes = 0, int $groups = 0)
    {
        $this->mockLdap->shouldReceive('connect')->times($connects)->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->times($versions);
        $this->mockLdap->shouldReceive('setOption')->times($options);
        $this->mockLdap->shouldReceive('bind')->times($binds)->andReturn(true);
        $this->mockEscapes($escapes);
        $this->mockExplodes($explodes);
        $this->mockGroupLookups($groups);
    }

    protected function mockGroupLookups(int $times = 1): void
    {
        $this->mockLdap->shouldReceive('read')->times($times)->andReturn(['count' => 0]);
        $this->mockLdap->shouldReceive('getEntries')->times($times)->andReturn(['count' => 0]);
    }

    public function test_login()
    {
        $this->commonLdapMocks(1, 1, 2, 4, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn'  => [$this->mockUser->name],
                'dn'  => 'dc=test' . config('services.ldap.base_dn'),
            ]]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
        $resp = $this->followRedirects($resp);
        $resp->assertSee('Please enter an email to use for this account.');
        $resp->assertSee($this->mockUser->name);

        $resp = $this->followingRedirects()->mockUserLogin($this->mockUser->email);
        $this->withHtml($resp)->assertElementExists('#home-default');
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
                'dn'  => 'dc=test' . config('services.ldap.base_dn'),
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

    public function test_user_filter_default_placeholder_format()
    {
        config()->set('services.ldap.user_filter', '(&(uid={user}))');
        $this->mockUser->name = 'barryldapuser';
        $expectedFilter = '(&(uid=\62\61\72\72\79\6c\64\61\70\75\73\65\72))';

        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')
            ->once()
            ->with($this->resourceId, config('services.ldap.base_dn'), $expectedFilter, \Mockery::type('array'))
            ->andReturn(['count' => 0, 0 => []]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
    }

    public function test_user_filter_old_placeholder_format()
    {
        config()->set('services.ldap.user_filter', '(&(username=${user}))');
        $this->mockUser->name = 'barryldapuser';
        $expectedFilter = '(&(username=\62\61\72\72\79\6c\64\61\70\75\73\65\72))';

        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')
            ->once()
            ->with($this->resourceId, config('services.ldap.base_dn'), $expectedFilter, \Mockery::type('array'))
            ->andReturn(['count' => 0, 0 => []]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/login');
    }

    public function test_initial_incorrect_credentials()
    {
        $this->commonLdapMocks(1, 1, 1, 0, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn'  => [$this->mockUser->name],
                'dn'  => 'dc=test' . config('services.ldap.base_dn'),
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
        $editUser = $this->users->viewer();
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
        $resp = $this->followingRedirects()->get('/register');
        $this->withHtml($resp)->assertElementContains('#content', 'Log In');
    }

    public function test_non_admins_cannot_change_auth_id()
    {
        $testUser = $this->users->viewer();
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
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => false,
        ]);

        $this->commonLdapMocks(1, 1, 4, 5, 2, 2, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => 'dc=test' . config('services.ldap.base_dn'),
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
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 2, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => 'dc=test' . config('services.ldap.base_dn'),
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

    public function test_dump_user_groups_shows_group_related_details_as_json()
    {
        app('config')->set([
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
            'services.ldap.dump_user_groups'   => true,
        ]);

        $userResp = ['count' => 1, 0 => [
            'uid'      => [$this->mockUser->name],
            'cn'       => [$this->mockUser->name],
            'dn'       => 'dc=test,' . config('services.ldap.base_dn'),
            'mail'     => [$this->mockUser->email],
        ]];
        $this->commonLdapMocks(1, 1, 4, 5, 2, 2, 0);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn($userResp, ['count' => 1,
                0 => [
                    'dn' => 'dc=test,' . config('services.ldap.base_dn'),
                    'memberof' => [
                        'count' => 1,
                        0 => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                    ],
                ],
            ]);

        $this->mockLdap->shouldReceive('read')->times(2);
        $this->mockLdap->shouldReceive('getEntries')->times(2)
            ->andReturn([
                'count' => 1,
                0 => [
                    'dn'        => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                    'memberof'  => [
                        'count' => 1,
                        0       => 'cn=monsters,ou=groups,dc=example,dc=com',
                    ],
                ],
            ], ['count' => 0]);

        $resp = $this->mockUserLogin();
        $resp->assertJson([
            'details_from_ldap' => [
                'dn'       => 'dc=test,' . config('services.ldap.base_dn'),
                'memberof' => [
                    0       => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                    'count' => 1,
                ],
            ],
            'parsed_direct_user_groups' => [
                'cn=ldaptester,ou=groups,dc=example,dc=com',
            ],
            'parsed_recursive_user_groups' => [
                'cn=ldaptester,ou=groups,dc=example,dc=com',
                'cn=monsters,ou=groups,dc=example,dc=com',
            ],
            'parsed_resulting_group_names' => [
                'ldaptester',
                'monsters',
            ],
        ]);
    }

    public function test_recursive_group_search_queries_via_full_dn()
    {
        app('config')->set([
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
        ]);

        $userResp = ['count' => 1, 0 => [
            'uid'      => [$this->mockUser->name],
            'cn'       => [$this->mockUser->name],
            'dn'       => 'dc=test,' . config('services.ldap.base_dn'),
            'mail'     => [$this->mockUser->email],
        ]];
        $groupResp = ['count' => 1,
                      0 => [
                          'dn'       => 'dc=test,' . config('services.ldap.base_dn'),
                          'memberof' => [
                              'count' => 1,
                              0       => 'cn=ldaptester,ou=groups,dc=example,dc=com',
                          ],
                      ],
        ];

        $this->commonLdapMocks(1, 1, 3, 4, 2, 1);

        $escapedName = ldap_escape($this->mockUser->name);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->twice()
            ->with($this->resourceId, config('services.ldap.base_dn'), "(&(uid={$escapedName}))", \Mockery::type('array'))
            ->andReturn($userResp, $groupResp);

        $this->mockLdap->shouldReceive('read')->times(1)
            ->with($this->resourceId, 'cn=ldaptester,ou=groups,dc=example,dc=com', '(objectClass=*)', ['memberof'])
            ->andReturn(['count' => 0]);
        $this->mockLdap->shouldReceive('getEntries')->times(1)
            ->with($this->resourceId, ['count' => 0])
            ->andReturn(['count' => 0]);

        $resp = $this->mockUserLogin();
        $resp->assertRedirect('/');
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
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 3, 4, 2, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => 'dc=test' . config('services.ldap.base_dn'),
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
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 4, 5, 2, 2, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$this->mockUser->name],
                'cn'       => [$this->mockUser->name],
                'dn'       => 'dc=test' . config('services.ldap.base_dn'),
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
                'dn'          => 'dc=test' . config('services.ldap.base_dn'),
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
                'dn'  => 'dc=test' . config('services.ldap.base_dn'),
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

    protected function checkLdapReceivesCorrectDetails($serverString, $expectedHostString): void
    {
        app('config')->set(['services.ldap.server' => $serverString]);

        $this->mockLdap->shouldReceive('connect')
            ->once()
            ->with($expectedHostString)
            ->andReturn(false);

        $this->mockUserLogin();
    }

    public function test_ldap_receives_correct_connect_host_from_config()
    {
        $expectedResultByInput = [
            'ldaps://bookstack:8080' => 'ldaps://bookstack:8080',
            'ldap.bookstack.com:8080' => 'ldap://ldap.bookstack.com:8080',
            'ldap.bookstack.com' => 'ldap://ldap.bookstack.com',
            'ldaps://ldap.bookstack.com' => 'ldaps://ldap.bookstack.com',
            'ldaps://ldap.bookstack.com ldap://a.b.com' => 'ldaps://ldap.bookstack.com ldap://a.b.com',
        ];

        foreach ($expectedResultByInput as $input => $expectedResult) {
            $this->checkLdapReceivesCorrectDetails($input, $expectedResult);
            $this->refreshApplication();
            $this->setUp();
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
        config()->set(['services.ldap.dump_user_details' => true, 'services.ldap.thumbnail_attribute' => 'jpegphoto']);

        $this->commonLdapMocks(1, 1, 1, 1, 1);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(1)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn'  => [$this->mockUser->name],
                // Test dumping binary data for avatar responses
                'jpegphoto' => base64_decode('/9j/4AAQSkZJRg=='),
                'dn'        => 'dc=test' . config('services.ldap.base_dn'),
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
                'dn'  => 'dc=test' . config('services.ldap.base_dn'),
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
                'dn'   => 'dc=test' . config('services.ldap.base_dn'),
                'mail' => 'tester@example.com',
            ]], ['count' => 1, 0 => [
                'uid'  => ['Barry'],
                'cn'   => ['Scott'],
                'dn'   => 'dc=bscott' . config('services.ldap.base_dn'),
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
            'services.ldap.group_style'        => 'RFC2703bis',
            'services.ldap.user_to_groups'     => true,
            'services.ldap.group_attribute'    => 'memberOf',
            'services.ldap.remove_from_groups' => true,
        ]);

        $this->commonLdapMocks(1, 1, 6, 8, 4, 2, 2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')
            ->times(4)
            ->andReturn(['count' => 1, 0 => [
                'uid'      => [$user->name],
                'cn'       => [$user->name],
                'dn'       => 'dc=test' . config('services.ldap.base_dn'),
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

    public function test_tls_ca_cert_option_throws_if_set_to_invalid_location()
    {
        $path = 'non_found_' . time();
        config()->set(['services.ldap.tls_ca_cert' => $path]);

        $this->commonLdapMocks(0, 0, 0, 0, 0);

        $this->assertThrows(function () {
            $this->withoutExceptionHandling()->mockUserLogin();
        }, LdapException::class, "Provided path [{$path}] for LDAP TLS CA certs could not be resolved to an existing location");
    }

    public function test_tls_ca_cert_option_used_if_set_to_a_folder()
    {
        $path = $this->files->testFilePath('');
        config()->set(['services.ldap.tls_ca_cert' => $path]);

        $this->mockLdap->shouldReceive('setOption')->once()->with(null, LDAP_OPT_X_TLS_CACERTDIR, rtrim($path, '/'))->andReturn(true);
        $this->runFailedAuthLogin();
    }

    public function test_tls_ca_cert_option_used_if_set_to_a_file()
    {
        $path = $this->files->testFilePath('test-file.txt');
        config()->set(['services.ldap.tls_ca_cert' => $path]);

        $this->mockLdap->shouldReceive('setOption')->once()->with(null, LDAP_OPT_X_TLS_CACERTFILE, $path)->andReturn(true);
        $this->runFailedAuthLogin();
    }
}
