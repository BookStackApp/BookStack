<?php

namespace BookStack\Console\Commands;

use BookStack\Users\Models\Role;
use BookStack\Users\UserRepo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;

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
                            {--external-auth-id= : The external authentication system id for the new admin user (SAML2/LDAP/OIDC)}
                            {--generate-password : Generate a random password for the new admin user}
                            {--initial : Indicate if this should set/update the details of the initial admin user}';

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
        $initialAdminOnly = $this->option('initial');
        $shouldGeneratePassword = $this->option('generate-password');
        $details = $this->gatherDetails($shouldGeneratePassword, $initialAdminOnly);

        $validator = Validator::make($details, [
            'email'            => ['required', 'email', 'min:5'],
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

        $adminRole = Role::getSystemRole('admin');

        if ($initialAdminOnly) {
            $handled = $this->handleInitialAdminIfExists($userRepo, $details, $shouldGeneratePassword, $adminRole);
            if ($handled !== null) {
                return $handled;
            }
        }

        $emailUsed = $userRepo->getByEmail($details['email']) !== null;
        if ($emailUsed) {
            $this->error("Could not create admin account.");
            $this->error("An account with the email address \"{$details['email']}\" already exists.");
            return 1;
        }

        $user = $userRepo->createWithoutActivity($validator->validated());
        $user->attachRole($adminRole);
        $user->email_confirmed = true;
        $user->save();

        if ($shouldGeneratePassword) {
            $this->line($details['password']);
        } else {
            $this->info("Admin account with email \"{$user->email}\" successfully created!");
        }

        return 0;
    }

    /**
     * Handle updates to the original admin account if it exists.
     * Returns an int return status if handled, otherwise returns null if not handled (new user to be created).
     */
    protected function handleInitialAdminIfExists(UserRepo $userRepo, array $data, bool $generatePassword, Role $adminRole): int|null
    {
        $defaultAdmin = $userRepo->getByEmail('admin@admin.com');
        if ($defaultAdmin && $defaultAdmin->hasSystemRole('admin')) {
            if ($defaultAdmin->email !== $data['email'] && $userRepo->getByEmail($data['email']) !== null) {
                $this->error("Could not create admin account.");
                $this->error("An account with the email address \"{$data['email']}\" already exists.");
                return 1;
            }

            $userRepo->updateWithoutActivity($defaultAdmin, $data, true);
            if ($generatePassword) {
                $this->line($data['password']);
            } else {
                $this->info("The default admin user has been updated with the provided details!");
            }

            return 0;
        } else if ($adminRole->users()->count() > 0) {
            $this->warn('Non-default admin user already exists. Skipping creation of new admin user.');
            return 2;
        }

        return null;
    }

    protected function gatherDetails(bool $generatePassword, bool $initialAdmin): array
    {
        $details = $this->snakeCaseOptions();

        if (empty($details['email'])) {
            if ($initialAdmin) {
                $details['email'] = 'admin@example.com';
            } else {
                $details['email'] = $this->ask('Please specify an email address for the new admin user');
            }
        }

        if (empty($details['name'])) {
            if ($initialAdmin) {
                $details['name'] = 'Admin';
            } else {
                $details['name'] = $this->ask('Please specify a name for the new admin user');
            }
        }

        if (empty($details['password'])) {
            if (empty($details['external_auth_id'])) {
                if ($generatePassword) {
                    $details['password'] = Str::random(32);
                } else {
                    $details['password'] = $this->ask('Please specify a password for the new admin user (8 characters min)');
                }
            } else {
                $details['password'] = Str::random(32);
            }
        }

        return $details;
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
