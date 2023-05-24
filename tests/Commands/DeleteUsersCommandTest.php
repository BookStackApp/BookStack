<?php

namespace Tests\Commands;

use BookStack\Users\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class DeleteUsersCommandTest extends TestCase
{
    public function test_command_deletes_users()
    {
        $userCount = User::query()->count();
        $normalUsers = $this->getNormalUsers();

        $normalUserCount = $userCount - count($normalUsers);
        $this->artisan('bookstack:delete-users')
            ->expectsQuestion('This will delete all users from the system that are not "admin" or system users. Are you sure you want to continue? (Type "yes" to continue)', 'yes')
            ->expectsOutputToContain("Deleted $normalUserCount of $userCount total users.")
            ->assertExitCode(0);

        $this->assertDatabaseMissing('users', ['id' => $normalUsers->first()->id]);
    }

    public function test_command_requires_confirmation()
    {
        $normalUsers = $this->getNormalUsers();

        $this->artisan('bookstack:delete-users')
            ->expectsQuestion('This will delete all users from the system that are not "admin" or system users. Are you sure you want to continue? (Type "yes" to continue)', 'no')
            ->assertExitCode(0);

        $this->assertDatabaseHas('users', ['id' => $normalUsers->first()->id]);
    }

    protected function getNormalUsers(): Collection
    {
        return User::query()->whereNull('system_name')
            ->get()
            ->filter(function (User $user) {
                return !$user->hasSystemRole('admin');
            });
    }
}
