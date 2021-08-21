<?php

namespace Tests\Commands;

use BookStack\Auth\Access\Mfa\MfaValue;
use BookStack\Auth\User;
use Tests\TestCase;

class ResetMfaCommandTest extends TestCase
{
    public function test_command_requires_email_or_id_option()
    {
        $this->artisan('bookstack:reset-mfa')
            ->expectsOutput('Either a --id=<number> or --email=<email> option must be provided.')
            ->assertExitCode(1);
    }

    public function test_command_runs_with_provided_email()
    {
        /** @var User $user */
        $user = User::query()->first();
        MfaValue::upsertWithValue($user, MfaValue::METHOD_TOTP, 'test');

        $this->assertEquals(1, $user->mfaValues()->count());
        $this->artisan("bookstack:reset-mfa --email={$user->email}")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput('User MFA methods have been reset.')
            ->assertExitCode(0);
        $this->assertEquals(0, $user->mfaValues()->count());
    }

    public function test_command_runs_with_provided_id()
    {
        /** @var User $user */
        $user = User::query()->first();
        MfaValue::upsertWithValue($user, MfaValue::METHOD_TOTP, 'test');

        $this->assertEquals(1, $user->mfaValues()->count());
        $this->artisan("bookstack:reset-mfa --id={$user->id}")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput('User MFA methods have been reset.')
            ->assertExitCode(0);
        $this->assertEquals(0, $user->mfaValues()->count());
    }

    public function test_saying_no_to_confirmation_does_not_reset_mfa()
    {
        /** @var User $user */
        $user = User::query()->first();
        MfaValue::upsertWithValue($user, MfaValue::METHOD_TOTP, 'test');

        $this->assertEquals(1, $user->mfaValues()->count());
        $this->artisan("bookstack:reset-mfa --id={$user->id}")
            ->expectsQuestion('Are you sure you want to proceed?', false)
            ->assertExitCode(1);
        $this->assertEquals(1, $user->mfaValues()->count());
    }

    public function test_giving_non_existing_user_shows_error_message()
    {
        $this->artisan("bookstack:reset-mfa --email=donkeys@example.com")
            ->expectsOutput('A user where email=donkeys@example.com could not be found.')
            ->assertExitCode(1);
    }
}
