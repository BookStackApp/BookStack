<?php

namespace Tests\Auth;

use BookStack\Auth\Access\Mfa\MfaSession;
use BookStack\Auth\Role;
use BookStack\Auth\User;
use BookStack\Entities\Models\Page;
use BookStack\Notifications\ConfirmEmail;
use BookStack\Notifications\ResetPassword;
use BookStack\Settings\SettingService;
use DB;
use Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Tests\BrowserKitTest;

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
        $settings = app(SettingService::class);
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
        $user = factory(User::class)->make();

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

    public function test_empty_registration_redirects_back_with_errors()
    {
        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true']);

        // Test form and ensure user is created
        $this->visit('/register')
            ->press('Create Account')
            ->see('The name field is required')
            ->seePageIs('/register');
    }

    public function test_registration_validation()
    {
        $this->setSettings(['registration-enabled' => 'true']);

        $this->visit('/register')
            ->type('1', '#name')
            ->type('1', '#email')
            ->type('1', '#password')
            ->press('Create Account')
            ->see('The name must be at least 2 characters.')
            ->see('The email must be a valid email address.')
            ->see('The password must be at least 8 characters.')
            ->seePageIs('/register');
    }

    public function test_sign_up_link_on_login()
    {
        $this->visit('/login')
            ->dontSee('Sign up');

        $this->setSettings(['registration-enabled' => 'true']);

        $this->visit('/login')
            ->see('Sign up');
    }

    public function test_confirmed_registration()
    {
        // Fake notifications
        Notification::fake();

        // Set settings and get user instance
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'true']);
        $user = factory(User::class)->make();

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
        $dbUser = User::where('email', '=', $user->email)->first();
        Notification::assertSentTo($dbUser, ConfirmEmail::class);

        // Test access and resend confirmation email
        $this->login($user->email, $user->password)
            ->seePageIs('/register/confirm/awaiting')
            ->see('Resend')
            ->visit('/books')
            ->seePageIs('/login')
            ->visit('/register/confirm/awaiting')
            ->press('Resend Confirmation Email');

        // Get confirmation and confirm notification matches
        $emailConfirmation = DB::table('email_confirmations')->where('user_id', '=', $dbUser->id)->first();
        Notification::assertSentTo($dbUser, ConfirmEmail::class, function ($notification, $channels) use ($emailConfirmation) {
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
        $user = factory(User::class)->make();
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

        $this->assertNull(auth()->user());

        $this->visit('/')->seePageIs('/login')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Log In')
            ->seePageIs('/register/confirm/awaiting')
            ->seeText('Email Address Not Confirmed');
    }

    public function test_restricted_registration_with_confirmation_disabled()
    {
        $this->setSettings(['registration-enabled' => 'true', 'registration-confirmation' => 'false', 'registration-restrict' => 'example.com']);
        $user = factory(User::class)->make();
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

        $this->assertNull(auth()->user());

        $this->visit('/')->seePageIs('/login')
            ->type($user->email, '#email')
            ->type($user->password, '#password')
            ->press('Log In')
            ->seePageIs('/register/confirm/awaiting')
            ->seeText('Email Address Not Confirmed');
    }

    public function test_user_creation()
    {
        /** @var User $user */
        $user = factory(User::class)->make();
        $adminRole = Role::getRole('admin');

        $this->asAdmin()
            ->visit('/settings/users')
            ->click('Add New User')
            ->type($user->name, '#name')
            ->type($user->email, '#email')
            ->check("roles[{$adminRole->id}]")
            ->type($user->password, '#password')
            ->type($user->password, '#password-confirm')
            ->press('Save')
            ->seePageIs('/settings/users')
            ->seeInDatabase('users', $user->only(['name', 'email']))
            ->see($user->name);

        $user->refresh();
        $this->assertStringStartsWith(Str::slug($user->name), $user->slug);
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

        $user->refresh();
        $this->assertStringStartsWith(Str::slug($user->name), $user->slug);
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

        $userPassword = User::find($user->id)->password;
        $this->assertTrue(Hash::check('newpassword', $userPassword));
    }

    public function test_user_deletion()
    {
        $userDetails = factory(User::class)->make();
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
        $adminRole = Role::getRole('admin');

        // Delete all but one admin user if there are more than one
        $adminUsers = $adminRole->users;
        if (count($adminUsers) > 1) {
            foreach ($adminUsers->splice(1) as $user) {
                $user->delete();
            }
        }

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

    public function test_mfa_session_cleared_on_logout()
    {
        $user = $this->getEditor();
        $mfaSession = $this->app->make(MfaSession::class);

        $mfaSession->markVerifiedForUser($user);;
        $this->assertTrue($mfaSession->isVerifiedForUser($user));

        $this->asAdmin()->visit('/logout');
        $this->assertFalse($mfaSession->isVerifiedForUser($user));
    }

    public function test_reset_password_flow()
    {
        Notification::fake();

        $this->visit('/login')->click('Forgot Password?')
            ->seePageIs('/password/email')
            ->type('admin@admin.com', 'email')
            ->press('Send Reset Link')
            ->see('A password reset link will be sent to admin@admin.com if that email address is found in the system.');

        $this->seeInDatabase('password_resets', [
            'email' => 'admin@admin.com',
        ]);

        $user = User::where('email', '=', 'admin@admin.com')->first();

        Notification::assertSentTo($user, ResetPassword::class);
        $n = Notification::sent($user, ResetPassword::class);

        $this->visit('/password/reset/' . $n->first()->token)
            ->see('Reset Password')
            ->submitForm('Reset Password', [
                'email'                 => 'admin@admin.com',
                'password'              => 'randompass',
                'password_confirmation' => 'randompass',
            ])->seePageIs('/')
            ->see('Your password has been successfully reset');
    }

    public function test_reset_password_flow_shows_success_message_even_if_wrong_password_to_prevent_user_discovery()
    {
        $this->visit('/login')->click('Forgot Password?')
            ->seePageIs('/password/email')
            ->type('barry@admin.com', 'email')
            ->press('Send Reset Link')
            ->see('A password reset link will be sent to barry@admin.com if that email address is found in the system.')
            ->dontSee('We can\'t find a user');

        $this->visit('/password/reset/arandometokenvalue')
            ->see('Reset Password')
            ->submitForm('Reset Password', [
                'email'                 => 'barry@admin.com',
                'password'              => 'randompass',
                'password_confirmation' => 'randompass',
            ])->followRedirects()
            ->seePageIs('/password/reset/arandometokenvalue')
            ->dontSee('We can\'t find a user')
            ->see('The password reset token is invalid for this email address.');
    }

    public function test_reset_password_page_shows_sign_links()
    {
        $this->setSettings(['registration-enabled' => 'true']);
        $this->visit('/password/email')
            ->seeLink('Log in')
            ->seeLink('Sign up');
    }

    public function test_login_redirects_to_initially_requested_url_correctly()
    {
        config()->set('app.url', 'http://localhost');
        $page = Page::query()->first();

        $this->visit($page->getUrl())
            ->seePageUrlIs(url('/login'));
        $this->login('admin@admin.com', 'password')
            ->seePageUrlIs($page->getUrl());
    }

    public function test_login_intended_redirect_does_not_redirect_to_external_pages()
    {
        config()->set('app.url', 'http://localhost');
        $this->setSettings(['app-public' => true]);

        $this->get('/login', ['referer' => 'https://example.com']);
        $login = $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);

        $login->assertRedirectedTo('http://localhost');
    }

    public function test_login_authenticates_admins_on_all_guards()
    {
        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $this->assertTrue(auth()->check());
        $this->assertTrue(auth('ldap')->check());
        $this->assertTrue(auth('saml2')->check());
    }

    public function test_login_authenticates_nonadmins_on_default_guard_only()
    {
        $editor = $this->getEditor();
        $editor->password = bcrypt('password');
        $editor->save();

        $this->post('/login', ['email' => $editor->email, 'password' => 'password']);
        $this->assertTrue(auth()->check());
        $this->assertFalse(auth('ldap')->check());
        $this->assertFalse(auth('saml2')->check());
    }

    public function test_failed_logins_are_logged_when_message_configured()
    {
        $log = $this->withTestLogger();
        config()->set(['logging.failed_login.message' => 'Failed login for %u']);

        $this->post('/login', ['email' => 'admin@example.com', 'password' => 'cattreedog']);
        $this->assertTrue($log->hasWarningThatContains('Failed login for admin@example.com'));

        $this->post('/login', ['email' => 'admin@admin.com', 'password' => 'password']);
        $this->assertFalse($log->hasWarningThatContains('Failed login for admin@admin.com'));
    }

    /**
     * Perform a login.
     */
    protected function login(string $email, string $password): AuthTest
    {
        return $this->visit('/login')
            ->type($email, '#email')
            ->type($password, '#password')
            ->press('Log In');
    }
}
