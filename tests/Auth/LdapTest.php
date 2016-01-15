<?php

use BookStack\Services\LdapService;
use BookStack\User;

class LdapTest extends \TestCase
{

    protected $mockLdap;
    protected $mockUser;
    protected $resourceId = 'resource-test';

    public function setUp()
    {
        parent::setUp();
        app('config')->set(['auth.method' => 'ldap', 'services.ldap.base_dn' => 'dc=ldap,dc=local', 'auth.providers.users.driver' => 'ldap']);
        $this->mockLdap = Mockery::mock(BookStack\Services\Ldap::class);
        $this->app['BookStack\Services\Ldap'] = $this->mockLdap;
        $this->mockUser = factory(User::class)->make();
    }

    public function test_ldap_login()
    {
        $this->mockLdap->shouldReceive('connect')->once()->andReturn($this->resourceId);
        $this->mockLdap->shouldReceive('setOption')->once();
        $this->mockLdap->shouldReceive('searchAndGetEntries')->twice()
            ->with($this->resourceId, config('services.ldap.base_dn'), Mockery::type('string'), Mockery::type('array'))
            ->andReturn(['count' => 1, 0 => [
                'uid' => [$this->mockUser->name],
                'cn' => [$this->mockUser->name],
                'dn'    => ['dc=test'.config('services.ldap.base_dn')]
            ]]);
        $this->mockLdap->shouldReceive('bind')->times(1)->andReturn(true);

        $this->visit('/login')
            ->see('Username')
            ->type($this->mockUser->name, '#username')
            ->type($this->mockUser->password, '#password')
            ->press('Sign In')
            ->seePageIs('/login')->see('Please enter an email to use for this account.');
    }

}