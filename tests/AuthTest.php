<?php

class AuthTest extends TestCase
{

    public function testAuthWorking()
    {
        $this->visit('/')
            ->seePageIs('/login');
    }

    public function testLogin()
    {
        $this->visit('/')
            ->seePageIs('/login')
            ->type('admin@admin.com', '#email')
            ->type('password', '#password')
            ->press('Sign In')
            ->seePageIs('/')
            ->see('BookStack');
    }

    public function testPublicViewing()
    {
        $settings = app('BookStack\Services\SettingService');
        $settings->put('app-public', 'true');
        $this->visit('/')
            ->seePageIs('/')
            ->see('Sign In');
    }

    public function testRegistrationShowing()
    {
        // Ensure registration form is showing
        $this->setSettings(['registration-enabled' => 'true']);
        $this->visit('/login')
            ->see('Sign up')
            ->click('Sign up')
            ->seePageIs('/register');
    }

    public function testNormalRegistration()
    {
        $this->setSettings(['registration-enabled' => 'true']);
        $user = factory(\BookStack\User::class)->make();

        $this->visit('/register')
            ->see('Sign Up')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Create Account')
            ->seePageIs('/')
            ->see($user->name);
    }

    private function setSettings($settingsArray)
    {
        $settings = app('BookStack\Services\SettingService');
        foreach($settingsArray as $key => $value) {
            $settings->put($key, $value);
        }
    }

    public function testLogout()
    {
        $this->asAdmin()
            ->visit('/')
            ->seePageIs('/')
            ->visit('/logout')
            ->visit('/')
            ->seePageIs('/login');
    }
}
