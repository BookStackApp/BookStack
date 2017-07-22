<?php namespace Tests;
use BookStack\User;

class LdapTest extends BrowserKitTest
{

    protected $mockLdap;
    protected $mockUser;
    protected $resourceId = 'resource-test';

    public function setUp()
    {
        parent::setUp();
        if (!defined('LDAP_OPT_REFERRALS')) define('LDAP_OPT_REFERRALS', 1);
        app('config')->set(['auth.method' => 'ldap', 'services.ldap.base_dn' => 'dc=ldap,dc=local', 'auth.providers.users.driver' => 'ldap']);
        $this->mockLdap = \Mockery::mock(\BookStack\Services\Ldap::class);
        $this->app['BookStack\Services\Ldap'] = $this->mockLdap;
        $this->mockUser = factory(User::class)->make();
    }

    public function test_login()
    {
        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setVersion')->once();
        $this->mockLdap->shouldReceive('setOption')->times(4);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(4)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn' => ['dc=test' . config('services.ldap.base_dn')]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(6)->andReturn(true);

        $this->visit('/login')
            ->see('Username')
            ->type($this->mockUser->name, '#username')
            ->type($this->mockUser->password, '#password')
            ->press('Log In')
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
        $this->mockLdap->shouldReceive('setOption')->times(2);
        $this->mockLdap->shouldReceive('searchAndGetEntries')->times(2)
            ->with($this->resourceId, config('services.ldap.base_dn'), \Mockery::type('string'), \Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'cn' => [$this->mockUser->name],
                'dn' => $ldapDn,
                'mail' => [$this->mockUser->email]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(3)->andReturn(true);

        $this->visit('/login')
            ->see('Username')
            ->type($this->mockUser->name, '#username')
            ->type($this->mockUser->password, '#password')
            ->press('Log In')
            ->seePageIs('/')
            ->see($this->mockUser->name)
            ->seeInDatabase('users', ['email' => $this->mockUser->email, 'email_confirmed' => false, 'external_auth_id' => $ldapDn]);
    }

    public function test_initial_incorrect_details()
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
        $this->mockLdap->shouldReceive('bind')->times(3)->andReturn(true, true, false);

        $this->visit('/login')
            ->see('Username')
            ->type($this->mockUser->name, '#username')
            ->type($this->mockUser->password, '#password')
            ->press('Log In')
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

}