<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\UserRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;
use Symfony\Component\Console\Command\Command as SymfonyCommand;

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
     */
    public function __construct(UserRepo $userRepo)
    {
        $this->userRepo = $userRepo;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @throws \BookStack\Exceptions\NotFoundException
     *
     * @return mixed
     */
    public function handle()
    {

        $details = $this->options();

        if (empty($details['email'])) {
            $details['email'] = $this->ask('Please specify an email address for the new admin user');
        }
        if (empty($details['name'])) {
            $details['name'] = $this->ask('Please specify a name for the new admin user');
        }
        if (empty($details['password'])) {
            $details['password'] = $this->ask('Please specify a password for the new admin user (8 characters min)');
        }

        $validator = Validator::make($details, [
            'email' => ['required', 'email', 'min:5', new Unique('users', 'email')],
            'name' => ['required', 'min:2'],
            'password' => ['required', Password::default()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }
            return SymfonyCommand::FAILURE;
        }

        $user = $this->userRepo->create($validator->validated());
        $this->userRepo->attachSystemRole($user, 'admin');
        $this->userRepo->downloadAndAssignUserAvatar($user);
        $user->email_confirmed = true;
        $user->save();

        $this->info("Admin account with email \"{$user->email}\" successfully created!");

        return SymfonyCommand::SUCCESS;
    }
}
