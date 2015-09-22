<?php

class SocialAuthTest extends TestCase
{

    public function testSocialRegistration()
    {
        // http://docs.mockery.io/en/latest/reference/startup_methods.html
        $user = factory(\BookStack\User::class)->make();

        $this->setSettings(['registration-enabled' => 'true']);
        $this->setEnvironment(['GOOGLE_APP_ID' => 'abc123', 'GOOGLE_APP_SECRET' => '123abc', 'APP_URL' => 'http://localhost']);

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

    protected function setEnvironment($array)
    {
        foreach ($array as $key => $value) {
            putenv("$key=$value");
        }
    }

}
