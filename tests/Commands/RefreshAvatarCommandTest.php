<?php

declare(strict_types=1);

namespace Tests\Commands;

use BookStack\Console\Commands\RefreshAvatarCommand;
use BookStack\Uploads\UserAvatars;
use BookStack\Users\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Symfony\Component\Console\Command\Command;
use Tests\TestCase;

final class RefreshAvatarCommandTest extends TestCase
{
    public function test_command_requires_email_or_id_option()
    {
        $this->artisan(RefreshAvatarCommand::class)
            ->expectsOutput('Either a --id=<number> or --email=<email> option must be provided.')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_runs_with_provided_email()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);
        config()->set(['services.disable_services' => false]);

        /** @var User $user */
        $user = User::query()->first();

        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);
        $avatar->destroyAllForUser($user);

        $this->assertFalse($user->avatar()->exists());
        $this->artisan(RefreshAvatarCommand::class, ['--email' => $user->email])
            ->expectsOutputToContain("- ID: {$user->id}")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput('User avatar has been updated.')
            ->assertExitCode(Command::SUCCESS);

        $expectedUri = 'https://www.gravatar.com/avatar/' . md5(strtolower($user->email)) . '?s=500&d=identicon';
        $this->assertEquals($expectedUri, $requests->latestRequest()->getUri());

        $user->refresh();
        $this->assertTrue($user->avatar()->exists());
    }

    public function test_command_runs_with_provided_id()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);
        config()->set(['services.disable_services' => false]);

        /** @var User $user */
        $user = User::query()->first();

        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);
        $avatar->destroyAllForUser($user);

        $this->assertFalse($user->avatar()->exists());
        $this->artisan(RefreshAvatarCommand::class, ['--id' => $user->id])
            ->expectsOutputToContain("- ID: {$user->id}")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput('User avatar has been updated.')
            ->assertExitCode(Command::SUCCESS);

        $expectedUri = 'https://www.gravatar.com/avatar/' . md5(strtolower($user->email)) . '?s=500&d=identicon';
        $this->assertEquals($expectedUri, $requests->latestRequest()->getUri());

        $user->refresh();
        $this->assertTrue($user->avatar()->exists());
    }

    public function test_command_runs_with_provided_id_error_upstream()
    {
        $requests = $this->mockHttpClient([new Response(404)]);
        config()->set(['services.disable_services' => false]);

        /** @var User $user */
        $user = User::query()->first();
        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);
        $avatar->assignToUserFromExistingData($user, $this->files->pngImageData(), 'png');

        $oldId = $user->avatar->id ?? 0;

        $this->artisan(RefreshAvatarCommand::class, ['--id' => $user->id])
            ->expectsOutputToContain("- ID: {$user->id}")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput('Could not update avatar please review logs.')
            ->assertExitCode(Command::FAILURE);

        $this->assertEquals(1, $requests->requestCount());

        $user->refresh();
        $newId = $user->avatar->id ?? $oldId;
        $this->assertEquals($oldId, $newId);
    }

    public function test_saying_no_to_confirmation_does_not_refresh_avatar()
    {
        /** @var User $user */
        $user = User::query()->first();

        $this->assertFalse($user->avatar()->exists());
        $this->artisan(RefreshAvatarCommand::class, ['--id' => $user->id])
            ->expectsQuestion('Are you sure you want to proceed?', false)
            ->assertExitCode(Command::FAILURE);
        $this->assertFalse($user->avatar()->exists());
    }

    public function test_giving_non_existing_user_shows_error_message()
    {
        $this->artisan(RefreshAvatarCommand::class, ['--email' => 'donkeys@example.com'])
            ->expectsOutput('A user where email=donkeys@example.com could not be found.')
            ->assertExitCode(Command::FAILURE);
    }

    public function test_command_runs_all_users_without_avatars_dry_run()
    {
        $users = User::query()->where('image_id', '=', 0)->get();

        $this->artisan(RefreshAvatarCommand::class, ['--users-without-avatars' => true])
            ->expectsOutput(count($users) . ' user(s) found without avatars.')
            ->expectsOutput("ID {$users[0]->id} - ")
            ->expectsOutput('Not updated')
            ->expectsOutput('Dry run, no avatars have been updated')
            ->assertExitCode(Command::SUCCESS);
    }

    public function test_command_runs_all_users_without_avatars_non_to_update()
    {
        config()->set(['services.disable_services' => false]);

        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);

        /** @var Collection|User[] $users */
        $users = User::query()->get();
        $responses = [];
        foreach ($users as $user) {
            $avatar->fetchAndAssignToUser($user);
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }
        $requests = $this->mockHttpClient($responses);

        $this->artisan(RefreshAvatarCommand::class, ['--users-without-avatars' => true, '-f' => true])
            ->expectsOutput('0 user(s) found without avatars.')
            ->expectsQuestion('Are you sure you want to refresh avatars of users that do not have one?', true)
            ->assertExitCode(Command::SUCCESS);

        $userWithAvatars = User::query()->where('image_id', '==', 0)->count();
        $this->assertEquals(0, $userWithAvatars);
        $this->assertEquals(0, $requests->requestCount());
    }

    public function test_command_runs_all_users_without_avatars()
    {
        config()->set(['services.disable_services' => false]);

        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);

        /** @var Collection|User[] $users */
        $users = User::query()->get();
        foreach ($users as $user) {
            $avatar->destroyAllForUser($user);
        }

        /** @var Collection|User[] $users */
        $users = User::query()->where('image_id', '=', 0)->get();

        $pendingCommand = $this->artisan(RefreshAvatarCommand::class, ['--users-without-avatars' => true, '-f' => true]);
        $pendingCommand
            ->expectsOutput($users->count() . ' user(s) found without avatars.')
            ->expectsQuestion('Are you sure you want to refresh avatars of users that do not have one?', true);

        $responses = [];
        foreach ($users as $user) {
            $pendingCommand->expectsOutput("ID {$user->id} - ");
            $pendingCommand->expectsOutput('Updated');
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }
        $requests = $this->mockHttpClient($responses);

        $pendingCommand->assertExitCode(Command::SUCCESS);
        $pendingCommand->run();

        $userWithAvatars = User::query()->where('image_id', '!=', 0)->count();
        $this->assertEquals($users->count(), $userWithAvatars);
        $this->assertEquals($users->count(), $requests->requestCount());
    }

    public function test_saying_no_to_confirmation_all_users_without_avatars()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);
        config()->set(['services.disable_services' => false]);

        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);

        /** @var Collection|User[] $users */
        $users = User::query()->get();
        foreach ($users as $user) {
            $avatar->destroyAllForUser($user);
        }

        $this->artisan(RefreshAvatarCommand::class, ['--users-without-avatars' => true, '-f' => true])
            ->expectsQuestion('Are you sure you want to refresh avatars of users that do not have one?', false)
            ->assertExitCode(Command::SUCCESS);

        $userWithAvatars = User::query()->where('image_id', '=', 0)->count();
        $this->assertEquals($users->count(), $userWithAvatars);
        $this->assertEquals(0, $requests->requestCount());
    }

    public function test_command_runs_all_users_dry_run()
    {
        $users = User::query()->where('image_id', '=', 0)->get();

        $this->artisan(RefreshAvatarCommand::class, ['--all' => true])
            ->expectsOutput(count($users) . ' user(s) found.')
            ->expectsOutput("ID {$users[0]->id} - ")
            ->expectsOutput('Not updated')
            ->expectsOutput('Dry run, no avatars have been updated')
            ->assertExitCode(Command::SUCCESS);
    }

    public function test_command_runs_update_all_users_avatar()
    {
        config()->set(['services.disable_services' => false]);

        /** @var Collection|User[] $users */
        $users = User::query()->get();

        $pendingCommand = $this->artisan(RefreshAvatarCommand::class, ['--all' => true, '-f' => true]);
        $pendingCommand
            ->expectsOutput($users->count() . ' user(s) found.')
            ->expectsQuestion('Are you sure you want to refresh avatars for ALL USERS?', true);

        $responses = [];
        foreach ($users as $user) {
            $pendingCommand->expectsOutput("ID {$user->id} - ");
            $pendingCommand->expectsOutput('Updated');
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }
        $requests = $this->mockHttpClient($responses);

        $pendingCommand->assertExitCode(Command::SUCCESS);
        $pendingCommand->run();

        $userWithAvatars = User::query()->where('image_id', '!=', 0)->count();
        $this->assertEquals($users->count(), $userWithAvatars);
        $this->assertEquals($users->count(), $requests->requestCount());
    }

    public function test_command_runs_update_all_users_avatar_errors()
    {
        config()->set(['services.disable_services' => false]);

        /** @var Collection|User[] $users */
        $users = User::query()->get();

        $pendingCommand = $this->artisan(RefreshAvatarCommand::class, ['--all' => true, '-f' => true]);
        $pendingCommand
            ->expectsOutput($users->count() . ' user(s) found.')
            ->expectsQuestion('Are you sure you want to refresh avatars for ALL USERS?', true);

        $responses = [];
        foreach ($users as $key => $user) {
            $pendingCommand->expectsOutput("ID {$user->id} - ");

            if ($key == 1) {
                $pendingCommand->expectsOutput('Not updated');
                $responses[] = new Response(404);
                continue;
            }

            $pendingCommand->expectsOutput('Updated');
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }

        $requests = $this->mockHttpClient($responses);

        $pendingCommand->assertExitCode(Command::FAILURE);
        $pendingCommand->run();

        $userWithAvatars = User::query()->where('image_id', '!=', 0)->count();
        $this->assertEquals($users->count() - 1, $userWithAvatars);
        $this->assertEquals($users->count(), $requests->requestCount());
    }

    public function test_saying_no_to_confirmation_update_all_users_avatar()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);
        config()->set(['services.disable_services' => false]);

        /** @var UserAvatars $avatar */
        $avatar = app()->make(UserAvatars::class);

        /** @var Collection|User[] $users */
        $users = User::query()->get();
        foreach ($users as $user) {
            $avatar->destroyAllForUser($user);
        }

        $this->artisan(RefreshAvatarCommand::class, ['--all' => true, '-f' => true])
            ->expectsQuestion('Are you sure you want to refresh avatars for ALL USERS?', false)
            ->assertExitCode(Command::SUCCESS);

        $userWithAvatars = User::query()->where('image_id', '=', 0)->count();
        $this->assertEquals($users->count(), $userWithAvatars);
        $this->assertEquals(0, $requests->requestCount());
    }
}
