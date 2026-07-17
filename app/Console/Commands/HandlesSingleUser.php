<?php

namespace BookStack\Console\Commands;

use BookStack\Users\Models\User;
use Exception;
use Illuminate\Console\Command;

/**
 * @mixin Command
 */
trait HandlesSingleUser
{
    /**
     * Fetch a user provided to this command.
     * Expects the command to accept 'id' and 'email' options.
     * @throws Exception
     */
    private function fetchProvidedUser(): User
    {
        $id = $this->option('id');
        $email = $this->option('email');
        if (!$id && !$email) {
            throw new Exception("Either a --id=<number> or --email=<email> option must be provided.\nRun this command with `--help` to show more options.");
        }

        $field = $id ? 'id' : 'email';
        $value = $id ?: $email;

        $user = User::query()
            ->where($field, '=', $value)
            ->first();

        if (!$user) {
            throw new Exception("A user where {$field}={$value} could not be found.");
        }

        return $user;
    }
}
