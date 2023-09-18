<?php

declare(strict_types=1);

namespace BookStack\Console\Commands;

use BookStack\Users\Models\User;
use Illuminate\Console\Command;
use BookStack\Uploads\UserAvatars;
use Illuminate\Database\Eloquent\Collection;

final class RefreshAvatarCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:refresh-avatar
                            {--id= : Numeric ID of the user to refresh avatar for}
                            {--email= : Email address of the user to refresh avatar for}
                            {--users-without-avatars : Refresh avatars for users that currently have no avatar}
                            {--a|all : Refresh all user avatars}
                            {--f|force : Actually run the update for --users-without-avatars, Defaults to a dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh avatar for given user or users';

    public function handle(UserAvatars $userAvatar): int
    {
        $dryRun = !$this->option('force');

        if ($this->option('users-without-avatars')) {
            return $this->handleUpdateWithoutAvatars($userAvatar, $dryRun);
        }

        if ($this->option('all')) {
            return $this->handleUpdateAllAvatars($userAvatar, $dryRun);
        }

        return $this->handleSingleUserUpdate($userAvatar);
    }

    private function handleUpdateWithoutAvatars(UserAvatars $userAvatar, bool $dryRun): int
    {
        $users = User::query()->where('image_id', '=', 0)->get();
        $this->info(count($users) . ' user(s) found without avatars.');

        if (!$dryRun) {
            $proceed = !$this->input->isInteractive() || $this->confirm('Are you sure you want to refresh avatars of users that do not have one?');
            if (!$proceed) {
                return self::SUCCESS;
            }
        }

        return $this->processUsers($users, $userAvatar, $dryRun);
    }

    private function handleUpdateAllAvatars(UserAvatars $userAvatar, bool $dryRun): int
    {
        $users = User::query()->get();
        $this->info(count($users) . ' user(s) found.');

        if (!$dryRun) {
            $proceed = !$this->input->isInteractive() || $this->confirm('Are you sure you want to refresh avatars for ALL USERS?');
            if (!$proceed) {
                return self::SUCCESS;
            }
        }

        return $this->processUsers($users, $userAvatar, $dryRun);
    }

    private function processUsers(Collection $users, UserAvatars $userAvatar, bool $dryRun): int
    {
        $exitCode = self::SUCCESS;
        foreach ($users as $user) {
            $this->getOutput()->write("ID {$user->id} - ", false);

            if ($dryRun) {
                $this->warn('Not updated');
                continue;
            }

            if ($this->fetchAvatar($userAvatar, $user)) {
                $this->info('Updated');
            } else {
                $this->error('Not updated');
                $exitCode = self::FAILURE;
            }
        }

        $this->getOutput()->newLine();
        if ($dryRun) {
            $this->comment('Dry run, no avatars have been updated');
            $this->comment('Run with -f or --force to perform the update');
        }

        return $exitCode;
    }


    private function handleSingleUserUpdate(UserAvatars $userAvatar): int
    {
        $id = $this->option('id');
        $email = $this->option('email');
        if (!$id && !$email) {
            $this->error('Either a --id=<number> or --email=<email> option must be provided.');
            $this->error('Run with `--help` to more options');

            return self::FAILURE;
        }

        $field = $id ? 'id' : 'email';
        $value = $id ?: $email;

        $user = User::query()
            ->where($field, '=', $value)
            ->first();

        if (!$user) {
            $this->error("A user where {$field}={$value} could not be found.");

            return self::FAILURE;
        }

        $this->info("This will refresh the avatar for user: \n- ID: {$user->id}\n- Name: {$user->name}\n- Email: {$user->email}\n");
        $confirm = $this->confirm('Are you sure you want to proceed?');
        if ($confirm) {
            if ($this->fetchAvatar($userAvatar, $user)) {
                $this->info('User avatar has been updated.');
                return self::SUCCESS;
            }

            $this->info('Could not update avatar please review logs.');
        }

        return self::FAILURE;
    }

    private function fetchAvatar(UserAvatars $userAvatar, User $user): bool
    {
        $oldId = $user->avatar->id ?? 0;

        $userAvatar->fetchAndAssignToUser($user);

        $user->refresh();
        $newId = $user->avatar->id ?? $oldId;
        return $oldId !== $newId;
    }
}
