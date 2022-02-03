<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\User;
use BookStack\Auth\UserRepo;
use Illuminate\Console\Command;

class DeleteUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:delete-users';

    protected $userRepo;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users that are not "admin" or system users';

    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    public function handle()
    {
        $confirm = $this->ask('This will delete all users from the system that are not "admin" or system users. Are you sure you want to continue? (Type "yes" to continue)');
        $numDeleted = 0;
        if (strtolower(trim($confirm)) === 'yes') {
            $totalUsers = User::query()->count();
            $users = User::query()->whereNull('system_name')->with('roles')->get();
            foreach ($users as $user) {
                if ($user->hasSystemRole('admin')) {
                    // don't delete users with "admin" role
                    continue;
                }
                $this->userRepo->destroy($user);
                $numDeleted++;
            }
            $this->info("Deleted $numDeleted of $totalUsers total users.");
        } else {
            $this->info('Exiting...');
        }
    }
}
