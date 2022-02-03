<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\User;
use Illuminate\Console\Command;
use BookStack\Auth\UserRepo;

class ResetAvatar extends Command
{
    /**
     * The name and signature of the console command.
     *
     */
    protected $signature = 'bookstack:reset-avatar
                            {--id= : Numeric ID of the user to reset MFA for}
                            {--email= : Email address of the user to reset MFA for} 
                            ';

    /**
     * The console command description.
     *
     */

    protected $description = 'Reset & Fetch avatar for given user';

    // protected $user;
    protected $userRepo;

     /**
     * Create a new command instance.
     *
     */

    public function __construct(User $user, UserRepo $userRepo)
    {
        $this->user = $user;
        $this->userRepo = $userRepo;
        parent::__construct();
    }



    /**
     * Execute the console command.
     *
     */
    public function handle()
    {
        $id = $this->option('id');
        $email = $this->option('email');
        if (!$id && !$email) {
            $this->error('Either a --id=<number> or --email=<email> option must be provided.');

            return 1;
        }

        $field = $id ? 'id' : 'email';
        $value = $id ?: $email;

        $user = User::query()
            ->where($field, '=', $value)
            ->first();

        if (!$user) {
            $this->error("A user where {$field}={$value} could not be found.");

            return 1;
        }

        $this->info("This will delete and re-fetch the avatar for user: \n- ID: {$user->id}\n- Name: {$user->name}\n- Email: {$user->email}\n");
        $confirm = $this->confirm('Are you sure you want to proceed?');
        if ($confirm) {
            
            $this->userRepo->downloadAndAssignUserAvatar($user);

            $this->info('User avatar have been reset.');
    
            return 0;
        }

        return 1;
    }
}
