<?php

namespace BookStack\Console\Commands;

use BookStack\Users\Models\Role;
use BookStack\Users\UserRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Validation\Rules\Unique;

class CreateAdminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:create-admin
                            {--email= : The email address for the new admin user}
                            {--name= : The name of the new admin user}
                            {--password= : The password to assign to the new admin user}
                            {--external-auth-id= : The external authentication system id for the new admin user (SAML2/LDAP/OIDC)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add a new admin user to the system';

    /**
     * Execute the console command.
     */
    public function handle(UserRepo $userRepo): int
    {
        $details = $this->snakeCaseOptions();

        if (empty($details['email'])) {
            $details['email'] = $this->ask('Please specify an email address for the new admin user');
        }

        if (empty($details['name'])) {
            $details['name'] = $this->ask('Please specify a name for the new admin user');
        }

        if (empty($details['password'])) {
            if (empty($details['external_auth_id'])) {
                $details['password'] = $this->ask('Please specify a password for the new admin user (8 characters min)');
            } else {
                $details['password'] = Str::random(32);
            }
        }

        $validator = Validator::make($details, [
            'email'            => ['required', 'email', 'min:5', new Unique('users', 'email')],
            'name'             => ['required', 'min:2'],
            'password'         => ['required_without:external_auth_id', Password::default()],
            'external_auth_id' => ['required_without:password'],
        ]);

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $error) {
                $this->error($error);
            }

            return 1;
        }

        $user = $userRepo->createWithoutActivity($validator->validated());
        $user->attachRole(Role::getSystemRole('admin'));
        $user->email_confirmed = true;
        $user->save();

        $this->info("Admin account with email \"{$user->email}\" successfully created!");

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
