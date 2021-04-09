<?php namespace Tests\Auth;

use BookStack\Auth\User;
use DB;
use Laravel\Socialite\Contracts\Factory;
use Laravel\Socialite\Contracts\Provider;
use Mockery;
use Tests\TestCase;

class SocialAuthTest extends TestCase
{

    public function test_social_registration()
    {
        $user = factory(User::class)->make();

        $this->setSettings(['registration-enabled' => 'true']);
        config(['GOOGLE_APP_ID' => 'abc123', 'GOOGLE_APP_SECRET' => '123abc', 'APP_URL' => 'http://localhost']);

        $mockSocialite = $this->mock(Factory::class);
        $mockSocialDriver = Mockery::mock(Provider::class);
        $mockSocialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);

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

        $mockSocialite = $this->mock(Factory::class);
        $mockSocialDriver = Mockery::mock(Provider::class);
        $mockSocialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);

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
        DB::table('social_accounts')->insert([
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

        $user = factory(User::class)->make();
        $mockSocialite = $this->mock(Factory::class);
        $mockSocialDriver = Mockery::mock(Provider::class);
        $mockSocialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);

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

        $user = factory(User::class)->make();
        $mockSocialite = $this->mock(Factory::class);
        $mockSocialDriver = Mockery::mock(Provider::class);
        $mockSocialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);

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

    public function test_google_select_account_option_changes_redirect_url()
    {
        config()->set('services.google.select_account', 'true');

        $resp = $this->get('/login/service/google');
        $this->assertStringContainsString('prompt=select_account', $resp->headers->get('Location'));
    }

    public function test_social_registration_with_no_name_uses_email_as_name()
    {
        $user = factory(User::class)->make(['email' => 'nonameuser@example.com']);

        $this->setSettings(['registration-enabled' => 'true']);
        config(['GITHUB_APP_ID' => 'abc123', 'GITHUB_APP_SECRET' => '123abc', 'APP_URL' => 'http://localhost']);

        $mockSocialite = $this->mock(Factory::class);
        $mockSocialDriver = Mockery::mock(Provider::class);
        $mockSocialUser = Mockery::mock(\Laravel\Socialite\Contracts\User::class);

        $mockSocialite->shouldReceive('driver')->twice()->with('github')->andReturn($mockSocialDriver);
        $mockSocialDriver->shouldReceive('redirect')->once()->andReturn(redirect('/'));
        $mockSocialDriver->shouldReceive('user')->once()->andReturn($mockSocialUser);

        $mockSocialUser->shouldReceive('getId')->twice()->andReturn(1);
        $mockSocialUser->shouldReceive('getEmail')->twice()->andReturn($user->email);
        $mockSocialUser->shouldReceive('getName')->once()->andReturn('');
        $mockSocialUser->shouldReceive('getAvatar')->once()->andReturn('avatar_placeholder');

        $this->get('/register/service/github');
        $this->get('/login/service/github/callback');
        $this->assertDatabaseHas('users', ['name' => 'nonameuser', 'email' => $user->email]);
        $user = $user->whereEmail($user->email)->first();
        $this->assertDatabaseHas('social_accounts', ['user_id' => $user->id]);
    }

}
