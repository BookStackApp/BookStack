<?php namespace Tests;

use BookStack\Notifications\ConfirmEmail;
use Illuminate\Support\Facades\Notification;

class AuthTest extends BrowserKitTest
{

    public function test_auth_working()
    {
        $this->visit('/')
            ->seePageIs('/login');
    }

    public function test_login()
    {
        $this->login('admin@admin.com', 'password')
            ->seePageIs('/');
    }

    public function test_public_viewing()
    {
        $settings = app('BookStack\Services\SettingService');
        $settings->put('app-public', 'true');
        $this->visit('/')
            ->seePageIs('/')
            ->see('Log In');
    }

    public function test_registration_showing()
    {
        // Ensure registration form is showing
        $this->setSettings(['registration-enabled' => 'true']);
        $this->visit('/login')
            ->see('Sign up')
            ->click('Sign up')
            ->seePageIs('/register');
    }

    public function test_normal_registration()
    {
        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true']);
        $user = factory(\BookStack\User::class)->make();

        // Test form and ensure user is created
        $this->visit('/register')
            ->see('Sign Up')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Create Account')
            ->seePageIs('/')
            ->see($user->name)
            ->seeInDatabase('users', ['name' => $user->name, 'email' => $user->email]);
    }


    public function test_confirmed_registration()
    {
        // Fake notifications
        Notification::fake();

        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'true']);
        $user = factory(\BookStack\User::class)->make();

        // Go through registration process
        $this->visit('/register')
            ->see('Sign Up')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Create Account')
            ->seePageIs('/register/confirm')
            ->seeInDatabase('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => false]);

        // Ensure notification sent
        $dbUser = \BookStack\User::where('email', '=', $user->email)->first();
        Notification::assertSentTo($dbUser, ConfirmEmail::class);

        // Test access and resend confirmation email
        $this->login($user->email, $user->password)
            ->seePageIs('/register/confirm/awaiting')
            ->see('Resend')
            ->visit('/books')
            ->seePageIs('/register/confirm/awaiting')
            ->press('Resend Confirmation Email');

        // Get confirmation and confirm notification matches
        $emailConfirmation = \DB::table('email_confirmations')->where('user_id', '=', $dbUser->id)->first();
        Notification::assertSentTo($dbUser, ConfirmEmail::class, function($notification, $channels) use ($emailConfirmation) {
            return $notification->token === $emailConfirmation->token;
        });
        
        // Check confirmation email confirmation activation.
        $this->visit('/register/confirm/' . $emailConfirmation->token)
            ->seePageIs('/')
            ->see($user->name)
            ->notSeeInDatabase('email_confirmations', ['token' => $emailConfirmation->token])
            ->seeInDatabase('users', ['name' => $dbUser->name, 'email' => $dbUser->email, 'email_confirmed' => true]);
    }

    public function test_restricted_registration()
    {
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'true', 'registration-restrict' => 'example.com']);
        $user = factory(\BookStack\User::class)->make();
        // Go through registration process
        $this->visit('/register')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Create Account')
            ->seePageIs('/register')
            ->dontSeeInDatabase('users', ['email' => $user->email])
            ->see('That email domain does not have access to this application');

        $user->email = 'barry@example.com';

        $this->visit('/register')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Create Account')
            ->seePageIs('/register/confirm')
            ->seeInDatabase('users', ['name' => $user->name, 'email' => $user->email, 'email_confirmed' => false]);
    }

    public function test_user_creation()
    {
        $user = factory(\BookStack\User::class)->make();

        $this->asAdmin()
            ->visit('/settings/users')
            ->click('Add New User')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->check('roles[admin]')
            ->type($user->password, '#password')
            ->type($user->password, '#password-confirm')
            ->press('Save')
            ->seePageIs('/settings/users')
            ->seeInDatabase('users', $user->toArray())
            ->see($user->name);
    }

    public function test_user_updating()
    {
        $user = $this->getNormalUser();
        $password = $user->password;
        $this->asAdmin()
            ->visit('/settings/users')
            ->click($user->name)
            ->seePageIs('/settings/users/' . $user->id)
            ->see($user->email)
            ->type('Barry Scott', '#name')
            ->press('Save')
            ->seePageIs('/settings/users')
            ->seeInDatabase('users', ['id' => $user->id, 'name' => 'Barry Scott', 'password' => $password])
            ->notSeeInDatabase('users', ['name' => $user->name]);
    }

    public function test_user_password_update()
    {
        $user = $this->getNormalUser();
        $userProfilePage = '/settings/users/' . $user->id;
        $this->asAdmin()
            ->visit($userProfilePage)
            ->type('newpassword', '#password')
            ->press('Save')
            ->seePageIs($userProfilePage)
            ->see('Password confirmation required')

            ->type('newpassword', '#password')
            ->type('newpassword', '#password-confirm')
            ->press('Save')
            ->seePageIs('/settings/users');

            $userPassword = \BookStack\User::find($user->id)->password;
            $this->assertTrue(\Hash::check('newpassword', $userPassword));
    }

    public function test_user_deletion()
    {
        $userDetails = factory(\BookStack\User::class)->make();
        $user = $this->getEditor($userDetails->toArray());

        $this->asAdmin()
            ->visit('/settings/users/' . $user->id)
            ->click('Delete User')
            ->see($user->name)
            ->press('Confirm')
            ->seePageIs('/settings/users')
            ->notSeeInDatabase('users', ['name' => $user->name]);
    }

    public function test_user_cannot_be_deleted_if_last_admin()
    {
        $adminRole = \BookStack\Role::getRole('admin');
        // Ensure we currently only have 1 admin user
        $this->assertEquals(1, $adminRole->users()->count());
        $user = $adminRole->users->first();

        $this->asAdmin()->visit('/settings/users/' . $user->id)
            ->click('Delete User')
            ->press('Confirm')
            ->seePageIs('/settings/users/' . $user->id)
            ->see('You cannot delete the only admin');
    }

    public function test_logout()
    {
        $this->asAdmin()
            ->visit('/')
            ->seePageIs('/')
            ->visit('/logout')
            ->visit('/')
            ->seePageIs('/login');
    }

    public function test_reset_password_flow()
    {

        Notification::fake();

        $this->visit('/login')->click('Forgot Password?')
            ->seePageIs('/password/email')
            ->type('admin@admin.com', 'email')
            ->press('Send Reset Link')
            ->see('A password reset link has been sent to admin@admin.com');

        $this->seeInDatabase('password_resets', [
            'email' => 'admin@admin.com'
        ]);

        $user = \BookStack\User::where('email', '=', 'admin@admin.com')->first();

        Notification::assertSentTo($user, \BookStack\Notifications\ResetPassword::class);
        $n = Notification::sent($user, \BookStack\Notifications\ResetPassword::class);

        $this->visit('/password/reset/' . $n->first()->token)
            ->see('Reset Password')
            ->submitForm('Reset Password', [
                'email' => 'admin@admin.com',
                'password' => 'randompass',
                'password_confirmation' => 'randompass'
            ])->seePageIs('/')
            ->see('Your password has been successfully reset');
    }

    public function test_reset_password_page_shows_sign_links()
    {
        $this->setSettings(['registration-enabled' => 'true']);
        $this->visit('/password/email')
            ->seeLink('Log in')
            ->seeLink('Sign up');
    }

    /**
     * Perform a login
     * @param string $email
     * @param string $password
     * @return $this
     */
    protected function login($email, $password)
    {
        return $this->visit('/login')
            ->type($email, '#email')
            ->type($password, '#password')
            ->press('Log In');
    }
}
