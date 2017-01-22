<?php

class SocialAuthTest extends TestCase
{

    public function test_social_registration()
    {
        // http://docs.mockery.io/en/latest/reference/startup_methods.html
        $user = factory(\BookStack\User::class)->make();

        $this->setSettings(['registration-enabled' => 'true']);
        config(['GOOGLE_APP_ID' => 'abc123', 'GOOGLE_APP_SECRET' => '123abc', 'APP_URL' => 'http://localhost']);

        $mockSocialite = Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $mockSocialDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialUser = Mockery::mock('\Laravel\Socialite\Contracts\User');

        $mockSocialite->shouldReceive('driver')->twice()->with('google')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->once()->andReturn(redirect('/'));
        $mockSocialDriver->shouldReceive('user')->once()->andReturn($mockSocialUser);

        $mockSocialUser->shouldReceive('getId')->twice()->andReturn(1);
        $mockSocialUser->shouldReceive('getEmail')->twice()->andReturn($user->email);
        $mockSocialUser->shouldReceive('getName')->once()->andReturn($user->name);
        $mockSocialUser->shouldReceive('getAvatar')->once()->andReturn('avatar_placeholder');

        $this->visit('/register/service/google');
        $this->visit('/login/service/google/callback');
        $this->seeInDatabase('users', ['name' => $user->name, 'email' => $user->email]);
        $user = $user->whereEmail($user->email)->first();
        $this->seeInDatabase('social_accounts', ['user_id' => $user->id]);
    }

    public function test_social_login()
    {
        $user = factory(\BookStack\User::class)->make();

        config([
            'GOOGLE_APP_ID' => 'abc123', 'GOOGLE_APP_SECRET' => '123abc',
            'GITHUB_APP_ID' => 'abc123', 'GITHUB_APP_SECRET' => '123abc',
            'APP_URL' => 'http://localhost'
        ]);

        $mockSocialite = Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $mockSocialDriver = Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialUser = Mockery::mock('\Laravel\Socialite\Contracts\User');

        $mockSocialUser->shouldReceive('getId')->twice()->andReturn('logintest123');

        $mockSocialDriver->shouldReceive('user')->twice()->andReturn($mockSocialUser);
        $mockSocialite->shouldReceive('driver')->twice()->with('google')->andReturn($mockSocialDriver);
        $mockSocialite->shouldReceive('driver')->twice()->with('github')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->twice()->andReturn(redirect('/'));

        // Test login routes
        $this->visit('/login')->seeElement('#social-login-google')
            ->click('#social-login-google')
            ->seePageIs('/login');

        // Test social callback
        $this->visit('/login/service/google/callback')->seePageIs('/login')
            ->see(trans('errors.social_account_not_used', ['socialAccount' => 'Google']));

        $this->visit('/login')->seeElement('#social-login-github')
        ->click('#social-login-github')
        ->seePageIs('/login');

        // Test social callback with matching social account
        DB::table('social_accounts')->insert([
            'user_id' => $this->getAdmin()->id,
            'driver' => 'github',
            'driver_id' => 'logintest123'
        ]);
        $this->visit('/login/service/github/callback')->seePageIs('/');
    }

}
