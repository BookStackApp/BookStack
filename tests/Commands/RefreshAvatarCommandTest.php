<?php

namespace Tests\Commands;

use BookStack\Uploads\Image;
use BookStack\Users\Models\User;
use GuzzleHttp\Psr7\Response;
use Illuminate\Database\Eloquent\Collection;
use Tests\TestCase;

class RefreshAvatarCommandTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        config()->set([
            'services.disable_services' => false,
            'services.avatar_url' => 'https://avatars.example.com?a=b',
        ]);
    }

    public function test_command_errors_if_avatar_fetch_disabled()
    {
        config()->set(['services.avatar_url' => false]);

        $this->artisan('bookstack:refresh-avatar')
            ->expectsOutputToContain("Avatar fetching is disabled on this instance")
            ->assertExitCode(1);
    }

    public function test_command_requires_email_or_id_option()
    {
        $this->artisan('bookstack:refresh-avatar')
            ->expectsOutputToContain("Either a --id=<number> or --email=<email> option must be provided")
            ->assertExitCode(1);
    }

    public function test_command_runs_with_provided_email()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);

        $user = $this->users->viewer();
        $this->assertFalse($user->avatar()->exists());

        $this->artisan("bookstack:refresh-avatar --email={$user->email} -f")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput("[ID: {$user->id}] {$user->email} - Updated")
            ->expectsOutputToContain('This will destroy any existing avatar images these users have, and attempt to fetch new avatar images from avatars.example.com')
            ->assertExitCode(0);

        $this->assertEquals('https://avatars.example.com?a=b', $requests->latestRequest()->getUri());

        $user->refresh();
        $this->assertTrue($user->avatar()->exists());
    }

    public function test_command_runs_with_provided_id()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);

        $user = $this->users->viewer();
        $this->assertFalse($user->avatar()->exists());

        $this->artisan("bookstack:refresh-avatar --id={$user->id} -f")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput("[ID: {$user->id}] {$user->email} - Updated")
            ->assertExitCode(0);

        $this->assertEquals('https://avatars.example.com?a=b', $requests->latestRequest()->getUri());

        $user->refresh();
        $this->assertTrue($user->avatar()->exists());
    }

    public function test_command_runs_with_provided_id_error_upstream()
    {
        $requests = $this->mockHttpClient([new Response(404)]);

        $user = $this->users->viewer();
        $this->assertFalse($user->avatar()->exists());

        $this->artisan("bookstack:refresh-avatar --id={$user->id} -f")
            ->expectsQuestion('Are you sure you want to proceed?', true)
            ->expectsOutput("[ID: {$user->id}] {$user->email} - Not updated")
            ->assertExitCode(1);

        $this->assertEquals(1, $requests->requestCount());
        $this->assertFalse($user->avatar()->exists());
    }

    public function test_saying_no_to_confirmation_does_not_refresh_avatar()
    {
        $user = $this->users->viewer();

        $this->assertFalse($user->avatar()->exists());
        $this->artisan("bookstack:refresh-avatar --id={$user->id} -f")
            ->expectsQuestion('Are you sure you want to proceed?', false)
            ->assertExitCode(0);
        $this->assertFalse($user->avatar()->exists());
    }

    public function test_giving_non_existing_user_shows_error_message()
    {
        $this->artisan('bookstack:refresh-avatar --email=donkeys@example.com')
            ->expectsOutput('A user where email=donkeys@example.com could not be found.')
            ->assertExitCode(1);
    }

    public function test_command_runs_all_users_without_avatars_dry_run()
    {
        $users = User::query()->where('image_id', '=', 0)->get();

        $this->artisan('bookstack:refresh-avatar --users-without-avatars')
            ->expectsOutput(count($users) . ' user(s) found to update avatars for.')
            ->expectsOutput("[ID: {$users[0]->id}] {$users[0]->email} - Not updated")
            ->expectsOutput('Dry run, no avatars were updated.')
            ->assertExitCode(0);
    }

    public function test_command_runs_all_users_without_avatars_with_none_to_update()
    {
        $requests = $this->mockHttpClient();
        $image = Image::factory()->create();
        User::query()->update(['image_id' => $image->id]);

        $this->artisan('bookstack:refresh-avatar --users-without-avatars -f')
            ->expectsOutput('0 user(s) found to update avatars for.')
            ->assertExitCode(0);

        $this->assertEquals(0, $requests->requestCount());
    }

    public function test_command_runs_all_users_without_avatars()
    {
        /** @var Collection|User[] $users */
        $users = User::query()->where('image_id', '=', 0)->get();

        $pendingCommand = $this->artisan('bookstack:refresh-avatar --users-without-avatars -f');
        $pendingCommand
            ->expectsOutput($users->count() . ' user(s) found to update avatars for.')
            ->expectsQuestion('Are you sure you want to proceed?', true);

        $responses = [];
        foreach ($users as $user) {
            $pendingCommand->expectsOutput("[ID: {$user->id}] {$user->email} - Updated");
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }
        $requests = $this->mockHttpClient($responses);

        $pendingCommand->assertExitCode(0);
        $pendingCommand->run();

        $this->assertEquals(0, User::query()->where('image_id', '=', 0)->count());
        $this->assertEquals($users->count(), $requests->requestCount());
    }

    public function test_saying_no_to_confirmation_all_users_without_avatars()
    {
        $requests = $this->mockHttpClient();

        $this->artisan('bookstack:refresh-avatar --users-without-avatars -f')
            ->expectsQuestion('Are you sure you want to proceed?', false)
            ->assertExitCode(0);

        $this->assertEquals(0, $requests->requestCount());
    }

    public function test_command_runs_all_users_dry_run()
    {
        $users = User::query()->where('image_id', '=', 0)->get();

        $this->artisan('bookstack:refresh-avatar --all')
            ->expectsOutput(count($users) . ' user(s) found to update avatars for.')
            ->expectsOutput("[ID: {$users[0]->id}] {$users[0]->email} - Not updated")
            ->expectsOutput('Dry run, no avatars were updated.')
            ->assertExitCode(0);
    }

    public function test_command_runs_update_all_users_avatar()
    {
        /** @var Collection|User[] $users */
        $users = User::query()->get();

        $pendingCommand = $this->artisan('bookstack:refresh-avatar --all -f');
        $pendingCommand
            ->expectsOutput($users->count() . ' user(s) found to update avatars for.')
            ->expectsQuestion('Are you sure you want to proceed?', true);

        $responses = [];
        foreach ($users as $user) {
            $pendingCommand->expectsOutput("[ID: {$user->id}] {$user->email} - Updated");
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }
        $requests = $this->mockHttpClient($responses);

        $pendingCommand->assertExitCode(0);
        $pendingCommand->run();

        $this->assertEquals(0, User::query()->where('image_id', '=', 0)->count());
        $this->assertEquals($users->count(), $requests->requestCount());
    }

    public function test_command_runs_update_all_users_avatar_errors()
    {
        /** @var Collection|User[] $users */
        $users = array_values(User::query()->get()->all());

        $pendingCommand = $this->artisan('bookstack:refresh-avatar --all -f');
        $pendingCommand
            ->expectsOutput(count($users) . ' user(s) found to update avatars for.')
            ->expectsQuestion('Are you sure you want to proceed?', true);

        $responses = [];
        foreach ($users as $index => $user) {
            if ($index === 0) {
                $pendingCommand->expectsOutput("[ID: {$user->id}] {$user->email} - Not updated");
                $responses[] = new Response(404);
                continue;
            }

            $pendingCommand->expectsOutput("[ID: {$user->id}] {$user->email} - Updated");
            $responses[] = new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData());
        }

        $requests = $this->mockHttpClient($responses);

        $pendingCommand->assertExitCode(1);
        $pendingCommand->run();

        $userWithAvatars = User::query()->where('image_id', '!=', 0)->count();
        $this->assertEquals(count($users) - 1, $userWithAvatars);
        $this->assertEquals(count($users), $requests->requestCount());
    }

    public function test_saying_no_to_confirmation_update_all_users_avatar()
    {
        $requests = $this->mockHttpClient([new Response(200, ['Content-Type' => 'image/png'], $this->files->pngImageData())]);

        $this->artisan('bookstack:refresh-avatar --all -f')
            ->expectsQuestion('Are you sure you want to proceed?', false)
            ->assertExitCode(0);

        $this->assertEquals(0, $requests->requestCount());
    }
}
