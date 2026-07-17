<?php

namespace Tests\Commands;

use BookStack\Users\Models\Role;
use BookStack\Users\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    public function test_standard_command_usage()
    {
        $this->artisan('bookstack:create-admin', [
            '--email' => 'admintest@example.com',
            '--name' => 'Admin Test',
            '--password' => 'testing-4',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name' => 'Admin Test',
        ]);

        /** @var User $user */
        $user = User::query()->where('email', '=', 'admintest@example.com')->first();
        $this->assertTrue($user->hasSystemRole('admin'));
        $this->assertTrue(Auth::attempt(['email' => 'admintest@example.com', 'password' => 'testing-4']));
    }

    public function test_providing_external_auth_id()
    {
        $this->artisan('bookstack:create-admin', [
            '--email' => 'admintest@example.com',
            '--name' => 'Admin Test',
            '--external-auth-id' => 'xX_admin_Xx',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name' => 'Admin Test',
            'external_auth_id' => 'xX_admin_Xx',
        ]);

        /** @var User $user */
        $user = User::query()->where('email', '=', 'admintest@example.com')->first();
        $this->assertNotEmpty($user->password);
    }

    public function test_password_required_if_external_auth_id_not_given()
    {
        $this->artisan('bookstack:create-admin', [
            '--email' => 'admintest@example.com',
            '--name' => 'Admin Test',
        ])->expectsQuestion('Please specify a password for the new admin user (8 characters min)', 'hunter2000')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name' => 'Admin Test',
        ]);
        $this->assertTrue(Auth::attempt(['email' => 'admintest@example.com', 'password' => 'hunter2000']));
    }

    public function test_generate_password_option()
    {
        $this->withoutMockingConsoleOutput()
            ->artisan('bookstack:create-admin', [
                '--email' => 'admintest@example.com',
                '--name' => 'Admin Test',
                '--generate-password' => true,
            ]);

        $output = trim(Artisan::output());
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{32}$/', $output);

        $user = User::query()->where('email', '=', 'admintest@example.com')->first();
        $this->assertTrue(Hash::check($output, $user->password));
    }

    public function test_initial_option_updates_default_admin()
    {
        $defaultAdmin = User::query()->where('email', '=', 'admin@admin.com')->first();

        $this->artisan('bookstack:create-admin', [
            '--email' => 'firstadmin@example.com',
            '--name' => 'Admin Test',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput('The default admin user has been updated with the provided details!')
            ->assertExitCode(0);

        $defaultAdmin->refresh();

        $this->assertEquals('firstadmin@example.com', $defaultAdmin->email);
    }

    public function test_initial_option_does_not_update_if_only_non_default_admin_exists()
    {
        $defaultAdmin = User::query()->where('email', '=', 'admin@admin.com')->first();
        $defaultAdmin->email = 'testadmin@example.com';
        $defaultAdmin->save();

        $this->artisan('bookstack:create-admin', [
            '--email' => 'firstadmin@example.com',
            '--name' => 'Admin Test',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput('Non-default admin user already exists. Skipping creation of new admin user.')
            ->assertExitCode(2);

        $defaultAdmin->refresh();

        $this->assertEquals('testadmin@example.com', $defaultAdmin->email);
    }

    public function test_initial_option_updates_creates_new_admin_if_none_exists()
    {
        $adminRole = Role::getSystemRole('admin');
        $adminRole->users()->delete();
        $this->assertEquals(0, $adminRole->users()->count());

        $this->artisan('bookstack:create-admin', [
            '--email' => 'firstadmin@example.com',
            '--name' => 'My initial admin',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput("Admin account with email \"firstadmin@example.com\" successfully created!")
            ->assertExitCode(0);

        $this->assertEquals(1, $adminRole->users()->count());
        $this->assertDatabaseHas('users', [
            'email' => 'firstadmin@example.com',
            'name' => 'My initial admin',
        ]);
    }

    public function test_initial_rerun_does_not_error_but_skips()
    {
        $adminRole = Role::getSystemRole('admin');
        $adminRole->users()->delete();

        $this->artisan('bookstack:create-admin', [
            '--email' => 'firstadmin@example.com',
            '--name' => 'My initial admin',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput("Admin account with email \"firstadmin@example.com\" successfully created!")
            ->assertExitCode(0);

        $this->artisan('bookstack:create-admin', [
            '--email' => 'firstadmin@example.com',
            '--name' => 'My initial admin',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput("Non-default admin user already exists. Skipping creation of new admin user.")
            ->assertExitCode(2);
    }

    public function test_initial_option_creation_errors_if_email_already_exists()
    {
        $adminRole = Role::getSystemRole('admin');
        $adminRole->users()->delete();
        $editor = $this->users->editor();

        $this->artisan('bookstack:create-admin', [
            '--email' => $editor->email,
            '--name' => 'My initial admin',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput("Could not create admin account.")
            ->expectsOutput("An account with the email address \"{$editor->email}\" already exists.")
            ->assertExitCode(1);
    }

    public function test_initial_option_updating_errors_if_email_already_exists()
    {
        $editor = $this->users->editor();
        $defaultAdmin = User::query()->where('email', '=', 'admin@admin.com')->first();
        $this->assertNotNull($defaultAdmin);

        $this->artisan('bookstack:create-admin', [
            '--email' => $editor->email,
            '--name' => 'My initial admin',
            '--password' => 'testing-7',
            '--initial' => true,
        ])->expectsOutput("Could not create admin account.")
            ->expectsOutput("An account with the email address \"{$editor->email}\" already exists.")
            ->assertExitCode(1);
    }

    public function test_initial_option_does_not_require_name_or_email_to_be_passed()
    {
        $adminRole = Role::getSystemRole('admin');
        $adminRole->users()->delete();
        $this->assertEquals(0, $adminRole->users()->count());

        $this->artisan('bookstack:create-admin', [
            '--generate-password' => true,
            '--initial' => true,
        ])->assertExitCode(0);

        $this->assertEquals(1, $adminRole->users()->count());
        $this->assertDatabaseHas('users', [
            'email' => 'admin@example.com',
            'name' => 'Admin',
        ]);
    }

    public function test_initial_option_updating_existing_user_with_generate_password_only_outputs_password()
    {
        $defaultAdmin = User::query()->where('email', '=', 'admin@admin.com')->first();

        $this->withoutMockingConsoleOutput()
            ->artisan('bookstack:create-admin', [
            '--email' => 'firstadmin@example.com',
            '--name' => 'Admin Test',
            '--generate-password' => true,
            '--initial' => true,
        ]);

        $output = Artisan::output();
        $this->assertMatchesRegularExpression('/^[a-zA-Z0-9]{32}$/', $output);

        $defaultAdmin->refresh();
        $this->assertEquals('firstadmin@example.com', $defaultAdmin->email);
    }
}
