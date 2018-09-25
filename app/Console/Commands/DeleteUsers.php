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

    protected $user;

    protected $userRepo;

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete users that are not "admin" or system users.';

    public function __construct(User $user, UserRepo $userRepo)
    {
        $this->user = $user;
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    public function handle()
    {
        $confirm = $this->ask('This will delete all users from the system that are not "admin" or system users. Are you sure you want to continue? (Type "yes" to continue)');
        $numDeleted = 0;
        if (strtolower(trim($confirm)) === 'yes') {
            $totalUsers = $this->user->count();
            $users = $this->user->where('system_name', '=', null)->with('roles')->get();
            foreach ($users as $user) {
                if ($user->hasSystemRole('admin')) {
                    // don't delete users with "admin" role
                    continue;
                }
                $this->userRepo->destroy($user);
                ++$numDeleted;
            }
            $this->info("Deleted $numDeleted of $totalUsers total users.");
        } else {
            $this->info('Exiting...');
        }
    }
}
