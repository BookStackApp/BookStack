<?php

namespace BookStack\Console\Commands;

use BookStack\Auth\User;
use Illuminate\Console\Command;

class ResetMfa extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:reset-mfa
                            {--id= : Numeric ID of the user to reset MFA for}
                            {--email= : Email address of the user to reset MFA for} 
                            ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset & Clear any configured MFA methods for the given user';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $id = $this->option('id');
        $email = $this->option('email');
        if (!$id && !$email) {
            $this->error('Either a --id=<number> or --email=<email> option must be provided.');

            return 1;
        }

        /** @var User $user */
        $field = $id ? 'id' : 'email';
        $value = $id ?: $email;
        $user = User::query()
            ->where($field, '=', $value)
            ->first();

        if (!$user) {
            $this->error("A user where {$field}={$value} could not be found.");

            return 1;
        }

        $this->info("This will delete any configure multi-factor authentication methods for user: \n- ID: {$user->id}\n- Name: {$user->name}\n- Email: {$user->email}\n");
        $this->info('If multi-factor authentication is required for this user they will be asked to reconfigure their methods on next login.');
        $confirm = $this->confirm('Are you sure you want to proceed?');
        if ($confirm) {
            $user->mfaValues()->delete();
            $this->info('User MFA methods have been reset.');

            return 0;
        }

        return 1;
    }
}
