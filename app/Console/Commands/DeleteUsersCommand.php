<?php

namespace BookStack\Console\Commands;

use BookStack\Users\Models\User;
use BookStack\Users\UserRepo;
use Illuminate\Console\Command;

class DeleteUsersCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:delete-users';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users that are not "admin" or system users';

    /**
     * Execute the console command.
     */
    public function handle(UserRepo $userRepo): int
    {
        $this->warn('This will delete all users from the system that are not "admin" or system users.');
        $confirm = $this->confirm('Are you sure you want to continue?');

        if (!$confirm) {
            return 0;
        }

        $totalUsers = User::query()->count();
        $numDeleted = 0;
        $users = User::query()->whereNull('system_name')->with('roles')->get();

        foreach ($users as $user) {
            if ($user->hasSystemRole('admin')) {
                // don't delete users with "admin" role
                continue;
            }
            $userRepo->destroy($user);
            $numDeleted++;
        }

        $this->info("Deleted $numDeleted of $totalUsers total users.");
        return 0;
    }
}
