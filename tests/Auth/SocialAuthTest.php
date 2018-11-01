<?php namespace Tests;

class SocialAuthTest extends TestCase
{

    public function test_social_registration()
    {
        // http://docs.mockery.io/en/latest/reference/startup_methods.html
        $user = factory(\BookStack\Auth\User::class)->make();

        $this->setSettings(['registration-enabled' => 'true']);
        config(['GOOGLE_APP_ID' => 'abc123', 'GOOGLE_APP_SECRET' => '123abc', 'APP_URL' => 'http://localhost']);

        $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $mockSocialDriver = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialUser = \Mockery::mock('\Laravel\Socialite\Contracts\User');

        $mockSocialite->shouldReceive('driver')->twice()->with('google')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->once()->andReturn(redirect('/'));
        $mockSocialDriver->shouldReceive('user')->once()->andReturn($mockSocialUser);

        $mockSocialUser->shouldReceive('getId')->twice()->andReturn(1);
        $mockSocialUser->shouldReceive('getEmail')->twice()->andReturn($user->email);
        $mockSocialUser->shouldReceive('getName')->once()->andReturn($user->name);
        $mockSocialUser->shouldReceive('getAvatar')->once()->andReturn('avatar_placeholder');

        $this->get('/register/service/google');
        $this->get('/login/service/google/callback');
        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email]);
        $user = $user->whereEmail($user->email)->first();
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id]);
    }

    public function test_social_login()
    {
        config([
            'GOOGLE_APP_ID' => 'abc123', 'GOOGLE_APP_SECRET' => '123abc',
            'GITHUB_APP_ID' => 'abc123', 'GITHUB_APP_SECRET' => '123abc',
            'APP_URL' => 'http://localhost'
        ]);

        $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $mockSocialDriver = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialUser = \Mockery::mock('\Laravel\Socialite\Contracts\User');

        $mockSocialUser->shouldReceive('getId')->twice()->andReturn('logintest123');

        $mockSocialDriver->shouldReceive('user')->twice()->andReturn($mockSocialUser);
        $mockSocialite->shouldReceive('driver')->twice()->with('google')->andReturn($mockSocialDriver);
        $mockSocialite->shouldReceive('driver')->twice()->with('github')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->twice()->andReturn(redirect('/'));

        // Test login routes
        $resp = $this->get('/login');
        $resp->assertElementExists('a#social-login-google[href$="/login/service/google"]');
        $resp = $this->followingRedirects()->get("/login/service/google");
        $resp->assertSee('login-form');

        // Test social callback
        $resp = $this->followingRedirects()->get('/login/service/google/callback');
        $resp->assertSee('login-form');
        $resp->assertSee(trans('errors.social_account_not_used', ['socialAccount' => 'Google']));

        $resp = $this->get('/login');
        $resp->assertElementExists('a#social-login-github[href$="/login/service/github"]');
        $resp = $this->followingRedirects()->get("/login/service/github");
        $resp->assertSee('login-form');


        // Test social callback with matching social account
        \DB::table('social_accounts')->insert([
            'user_id' => $this->getAdmin()->id,
            'driver' => 'github',
            'driver_id' => 'logintest123'
        ]);
        $resp = $this->followingRedirects()->get('/login/service/github/callback');
        $resp->assertDontSee("login-form");
    }

    public function test_social_autoregister()
    {
        config([
            'services.google.client_id' => 'abc123', 'services.google.client_secret' => '123abc',
            'APP_URL' => 'http://localhost'
        ]);

        $user = factory(\BookStack\Auth\User::class)->make();
        $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $mockSocialDriver = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialUser = \Mockery::mock('\Laravel\Socialite\Contracts\User');

        $mockSocialUser->shouldReceive('getId')->times(4)->andReturn(1);
        $mockSocialUser->shouldReceive('getEmail')->times(2)->andReturn($user->email);
        $mockSocialUser->shouldReceive('getName')->once()->andReturn($user->name);
        $mockSocialUser->shouldReceive('getAvatar')->once()->andReturn('avatar_placeholder');

        $mockSocialDriver->shouldReceive('user')->times(2)->andReturn($mockSocialUser);
        $mockSocialite->shouldReceive('driver')->times(4)->with('google')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->twice()->andReturn(redirect('/'));

        $googleAccountNotUsedMessage = trans('errors.social_account_not_used', ['socialAccount' => 'Google']);

        $this->get('/login/service/google');
        $resp = $this->followingRedirects()->get('/login/service/google/callback');
        $resp->assertSee($googleAccountNotUsedMessage);

        config(['services.google.auto_register' => true]);

        $this->get('/login/service/google');
        $resp = $this->followingRedirects()->get('/login/service/google/callback');
        $resp->assertDontSee($googleAccountNotUsedMessage);

        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => false]);
        $user = $user->whereEmail($user->email)->first();
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id]);
    }

    public function test_social_auto_email_confirm()
    {
        config([
            'services.google.client_id' => 'abc123', 'services.google.client_secret' => '123abc',
            'APP_URL' => 'http://localhost', 'services.google.auto_register' => true, 'services.google.auto_confirm' => true
        ]);

        $user = factory(\BookStack\Auth\User::class)->make();
        $mockSocialite = \Mockery::mock('Laravel\Socialite\Contracts\Factory');
        $this->app['Laravel\Socialite\Contracts\Factory'] = $mockSocialite;
        $mockSocialDriver = \Mockery::mock('Laravel\Socialite\Contracts\Provider');
        $mockSocialUser = \Mockery::mock('\Laravel\Socialite\Contracts\User');

        $mockSocialUser->shouldReceive('getId')->times(3)->andReturn(1);
        $mockSocialUser->shouldReceive('getEmail')->times(2)->andReturn($user->email);
        $mockSocialUser->shouldReceive('getName')->once()->andReturn($user->name);
        $mockSocialUser->shouldReceive('getAvatar')->once()->andReturn('avatar_placeholder');

        $mockSocialDriver->shouldReceive('user')->times(1)->andReturn($mockSocialUser);
        $mockSocialite->shouldReceive('driver')->times(2)->with('google')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->once()->andReturn(redirect('/'));

        $this->get('/login/service/google');
        $this->get('/login/service/google/callback');

        $this->assertDatabaseHas('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => true]);
        $user = $user->whereEmail($user->email)->first();
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id]);
    }

}
