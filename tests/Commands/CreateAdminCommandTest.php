<?php

namespace Tests\Commands;

use BookStack\Auth\User;
use Illuminate\Support\Facades\Auth;
use Tests\TestCase;

class CreateAdminCommandTest extends TestCase
{
    public function test_standard_command_usage()
    {
        $this->artisan('bookstack:create-admin', [
            '--email'    => 'admintest@example.com',
            '--name'     => 'Admin Test',
            '--password' => 'testing-4',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name'  => 'Admin Test',
        ]);

        /** @var User $user */
        $user = User::query()->where('email', '=', 'admintest@example.com')->first();
        $this->assertTrue($user->hasSystemRole('admin'));
        $this->assertTrue(Auth::attempt(['email' => 'admintest@example.com', 'password' => 'testing-4']));
    }

    public function test_providing_external_auth_id()
    {
        $this->artisan('bookstack:create-admin', [
            '--email'            => 'admintest@example.com',
            '--name'             => 'Admin Test',
            '--external-auth-id' => 'xX_admin_Xx',
        ])->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email'            => 'admintest@example.com',
            'name'             => 'Admin Test',
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
            '--name'  => 'Admin Test',
        ])->expectsQuestion('Please specify a password for the new admin user (8 characters min)', 'hunter2000')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name'  => 'Admin Test',
        ]);
        $this->assertTrue(Auth::attempt(['email' => 'admintest@example.com', 'password' => 'hunter2000']));
    }
}
