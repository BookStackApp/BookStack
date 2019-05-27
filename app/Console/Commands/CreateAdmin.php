<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\UserRepo;
use Illuminate\Console\Command;

class CreateAdmin extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:create-admin
                            {--email= : The email address for the new admin user}
                            {--name= : The name of the new admin user}
                            {--password= : The password to assign to the new admin user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new admin user to the system';

    protected $userRepo;

    /**
     * Create a new command instance.
     *
     * @param UserRepo $userRepo
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     * @throws \BookStack\Exceptions\NotFoundException
     */
    public function handle()
    {
        $email = trim($this->option('email'));
        if (empty($email)) {
            $email = $this->ask('Please specify an email address for the new admin user');
        }
        if (mb_strlen($email) < 5 || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->error('Invalid email address provided');
        }

        if ($this->userRepo->getByEmail($email) !== null) {
            return $this->error('A user with the provided email already exists!');
        }

        $name = trim($this->option('name'));
        if (empty($name)) {
            $name = $this->ask('Please specify an name for the new admin user');
        }
        if (mb_strlen($name) < 2) {
            return $this->error('Invalid name provided');
        }

        $password = trim($this->option('password'));
        if (empty($password)) {
            $password = $this->secret('Please specify a password for the new admin user');
        }
        if (mb_strlen($password) < 5) {
            return $this->error('Invalid password provided, Must be at least 5 characters');
        }


        $user = $this->userRepo->create(['email' => $email, 'name' => $name, 'password' => $password]);
        $this->userRepo->attachSystemRole($user, 'admin');
        $this->userRepo->downloadAndAssignUserAvatar($user);
        $user->email_confirmed = true;
        $user->save();

        $this->info("Admin account with email \"{$user->email}\" successfully created!");
    }
}
