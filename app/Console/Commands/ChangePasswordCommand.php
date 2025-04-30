<?php

namespace BookStack\Console\Commands;

use BookStack\Users\Models\Role;
use BookStack\Users\UserRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;

class ChangePasswordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:change-password
                            {--email= : The email address of the account}
                            {--password= : The password to assign}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change the password of a user';

    /**
     * Execute the console command.
     */
    public function handle(UserRepo $userRepo): int
    {
        $details = $this->snakeCaseOptions();

        if (empty($details['email'])) {
            $details['email'] = $this->ask('Please specify an email address');
        }

        if (empty($details['password'])) {
	    $details['password'] = $this->ask('Please specify a password (8 characters minimum)');
        }

        $validator = Validator::make($details, [
            'email'            => ['required', 'email', 'min:5'],
            'password'         => [Password::default()],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

	$user = $userRepo->getByEmail($details['email']);

	if (empty($user)) {
	    $this->error("Could not find user!");
	    return 1;
	}

	$user->password = Hash::make($details['password']);
        $user->save();

        $this->info("Password for account with email \"{$user->email}\" successfully updated!");

        return 0;
    }

    protected function snakeCaseOptions(): array
    {
        $returnOpts = [];
        foreach ($this->options() as $key => $value) {
            $returnOpts[str_replace('-', '_', $key)] = $value;
        }

        return $returnOpts;
    }
}
