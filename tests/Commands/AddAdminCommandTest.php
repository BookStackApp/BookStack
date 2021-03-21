<?php namespace Tests\Commands;

use BookStack\Auth\User;
use Tests\TestCase;

class AddAdminCommandTest extends TestCase
{
    public function test_add_admin_command()
    {
        $exitCode = \Artisan::call('bookstack:create-admin', [
            '--email' => 'admintest@example.com',
            '--name' => 'Admin Test',
            '--password' => 'testing-4',
        ]);
        $this->assertTrue($exitCode === 0, 'Command executed successfully');

        $this->assertDatabaseHas('users', [
            'email' => 'admintest@example.com',
            'name' => 'Admin Test'
        ]);

        $this->assertTrue(User::query()->where('email', '=', 'admintest@example.com')->first()->hasSystemRole('admin'), 'User has admin role as expected');
        $this->assertTrue(\Auth::attempt(['email' => 'admintest@example.com', 'password' => 'testing-4']), 'Password stored as expected');
    }
}