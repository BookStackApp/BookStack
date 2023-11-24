<?php

namespace BookStack\Console\Commands;

use BookStack\Users\Models\User;
use Exception;
use Illuminate\Console\Command;
use BookStack\Uploads\UserAvatars;

class RefreshAvatarCommand extends Command
{
    use HandlesSingleUser;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'bookstack:refresh-avatar
                            {--id= : Numeric ID of the user to refresh avatar for}
                            {--email= : Email address of the user to refresh avatar for}
                            {--users-without-avatars : Refresh avatars for users that currently have no avatar}
                            {--a|all : Refresh avatars for all users}
                            {--f|force : Actually run the update, Defaults to a dry-run}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh avatar for the given user(s)';

    public function handle(UserAvatars $userAvatar): int
    {
        if (!$userAvatar->avatarFetchEnabled()) {
            $this->error("Avatar fetching is disabled on this instance.");
            return self::FAILURE;
        }

        if ($this->option('users-without-avatars')) {
            return $this->processUsers(User::query()->whereDoesntHave('avatar')->get()->all(), $userAvatar);
        }

        if ($this->option('all')) {
            return $this->processUsers(User::query()->get()->all(), $userAvatar);
        }

        try {
            $user = $this->fetchProvidedUser();
            return $this->processUsers([$user], $userAvatar);
        } catch (Exception $exception) {
            $this->error($exception->getMessage());
            return self::FAILURE;
        }
    }

    /**
     * @param User[] $users
     */
    private function processUsers(array $users, UserAvatars $userAvatar): int
    {
        $dryRun = !$this->option('force');
        $this->info(count($users) . " user(s) found to update avatars for.");

        if (count($users) === 0) {
            return self::SUCCESS;
        }

        if (!$dryRun) {
            $fetchHost = parse_url($userAvatar->getAvatarUrl(), PHP_URL_HOST);
            $this->warn("This will destroy any existing avatar images these users have, and attempt to fetch new avatar images from {$fetchHost}.");
            $proceed = !$this->input->isInteractive() || $this->confirm('Are you sure you want to proceed?');
            if (!$proceed) {
                return self::SUCCESS;
            }
        }

        $this->info("");

        $exitCode = self::SUCCESS;
        foreach ($users as $user) {
            $linePrefix = "[ID: {$user->id}] $user->email -";

            if ($dryRun) {
                $this->warn("{$linePrefix} Not updated");
                continue;
            }

            if ($this->fetchAvatar($userAvatar, $user)) {
                $this->info("{$linePrefix} Updated");
            } else {
                $this->error("{$linePrefix} Not updated");
                $exitCode = self::FAILURE;
            }
        }

        if ($dryRun) {
            $this->comment("");
            $this->comment("Dry run, no avatars were updated.");
            $this->comment('Run with -f or --force to perform the update.');
        }

        return $exitCode;
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
