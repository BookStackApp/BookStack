<?php

namespace BookStack\Console\Commands;

use Exception;
use Illuminate\Console\Command;

class ResetMfaCommand extends Command
{
    use HandlesSingleUser;

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
     * Execute the console command.
     */
    public function handle(): int
    {
        try {
            $user = $this->fetchProvidedUser();
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
            return 1;
        }

        $this->info("This will delete any configure multi-factor authentication methods for user: \n- ID: {$user->id}\n- Name: {$user->name}\n- Email: {$user->email}\n");
        $this->info('If multi-factor authentication is required for this user they will be asked to reconfigure their methods on next login.');
        $confirm = $this->confirm('Are you sure you want to proceed?');
        if (!$confirm) {
            return 1;
        }

        $user->mfaValues()->delete();
        $this->info('User MFA methods have been reset.');

        return 0;
    }
}
